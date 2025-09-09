<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\UserLimit;

class UserAdminController extends Controller
{
    /**
     * Page: Users — Live (table hydrates via /admin/users/table).
     */
    public function index()
    {
        return view('admin.users');
    }

    /**
     * JSON: Top 20 users by last activity/seen with optional ?q= search.
     * Returns rows shaped for the UI:
     *   id, user, email, last_seen, ip, country (optional), limit, enabled, banned
     */
    public function table(Request $request)
    {
        if (!Schema::hasTable('users')) {
            return response()->json(['rows' => []]);
        }

        $q = trim((string) $request->get('q', ''));

        $hasAnalyze = Schema::hasTable('analyze_logs');
        $hasLimits  = Schema::hasTable('user_limits');
        $hasLastSeen = Schema::hasColumn('users', 'last_seen_at');
        $hasLastLogin = Schema::hasColumn('users', 'last_login_at');
        $hasIp = Schema::hasColumn('users', 'last_ip');
        $hasCountry = Schema::hasColumn('users', 'country');
        $hasBanned = Schema::hasColumn('users', 'is_banned');

        $query = DB::table('users as u')
            ->addSelect('u.id', 'u.name', 'u.email', 'u.created_at');

        if ($hasLastSeen)  $query->addSelect('u.last_seen_at');
        if ($hasLastLogin) $query->addSelect('u.last_login_at');
        if ($hasIp)        $query->addSelect('u.last_ip');
        if ($hasCountry)   $query->addSelect('u.country');
        if ($hasBanned)    $query->addSelect('u.is_banned');

        // Join latest analyze activity (subquery -> last_activity)
        if ($hasAnalyze) {
            $agg = DB::table('analyze_logs')
                ->select('user_id', DB::raw('MAX(created_at) as last_activity'))
                ->groupBy('user_id');

            $query->leftJoinSub($agg, 'a', function ($j) {
                $j->on('a.user_id', '=', 'u.id');
            })->addSelect('a.last_activity');
        } else {
            $query->addSelect(DB::raw('NULL as last_activity'));
        }

        // Join limits
        if ($hasLimits) {
            $query->leftJoin('user_limits as ul', 'ul.user_id', '=', 'u.id')
                  ->addSelect('ul.daily_limit', 'ul.is_enabled');
        } else {
            $query->addSelect(DB::raw('NULL as daily_limit'), DB::raw('NULL as is_enabled'));
        }

        // Search
        if ($q !== '') {
            $query->where(function ($w) use ($q, $hasIp) {
                $w->where('u.email', 'like', "%{$q}%")
                  ->orWhere('u.name', 'like', "%{$q}%");
                if ($hasIp) $w->orWhere('u.last_ip', 'like', "%{$q}%");
            });
        }

        // Order by best-available signal
        $orderExpr = 'COALESCE('
            . ($hasLastSeen ? 'u.last_seen_at,' : '')
            . 'last_activity,u.created_at) DESC';

        // Group-by to satisfy SQL modes
        $groupCols = ['u.id', 'u.name', 'u.email', 'u.created_at'];
        if ($hasLastSeen)  $groupCols[] = 'u.last_seen_at';
        if ($hasLastLogin) $groupCols[] = 'u.last_login_at';
        if ($hasIp)        $groupCols[] = 'u.last_ip';
        if ($hasCountry)   $groupCols[] = 'u.country';
        if ($hasBanned)    $groupCols[] = 'u.is_banned';
        if ($hasAnalyze)   $groupCols[] = 'a.last_activity';
        if ($hasLimits)    { $groupCols[] = 'ul.daily_limit'; $groupCols[] = 'ul.is_enabled'; }

        $rows = $query->groupBy($groupCols)
            ->orderByRaw($orderExpr)
            ->limit(20)
            ->get();

        $out = $rows->map(function ($r) {
            $last = $r->last_seen_at ?? $r->last_login_at ?? $r->last_activity ?? $r->created_at;
            return [
                'id'        => (int) $r->id,
                'user'      => $r->name ?: $r->email,
                'email'     => $r->email,
                'last_seen' => optional($last)->format('Y-m-d H:i'),
                'ip'        => $r->last_ip ?? '—',
                'country'   => $r->country ?? null,
                'limit'     => $r->daily_limit ?? null,
                'enabled'   => isset($r->is_enabled) ? (int) $r->is_enabled : 1,
                'banned'    => isset($r->is_banned) ? (bool) $r->is_banned : false,
            ];
        });

        return response()->json(['rows' => $out]);
    }

    /**
     * Drawer JSON for a single user (id/email/last_seen/ip + limit + latest activity).
     * Route: GET /admin/users/{user}/live
     */
    public function live(User $user)
    {
        $limit = null;
        if (Schema::hasTable('user_limits')) {
            $limit = UserLimit::where('user_id', $user->id)->first();
        }

        $latest = [];
        if (Schema::hasTable('analyze_logs')) {
            $latest = DB::table('analyze_logs')
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->limit(20)
                ->get(['created_at', 'tool', 'keyword', 'tokens', 'cost_usd'])
                ->map(function ($r) {
                    return [
                        'created_at' => optional($r->created_at)->format('Y-m-d H:i'),
                        'type'       => $r->tool ?: 'analysis',
                        'keyword'    => $r->keyword,
                        'tokens'     => $r->tokens,
                        'cost'       => $r->cost_usd,
                    ];
                })->all();
        }

        return response()->json([
            'user' => [
                'id'            => $user->id,
                'email'         => $user->email,
                'name'          => $user->name,
                'last_seen_at'  => Schema::hasColumn('users', 'last_seen_at') ? optional($user->last_seen_at)->format('Y-m-d H:i') : null,
                'last_ip'       => Schema::hasColumn('users', 'last_ip') ? $user->last_ip : null,
            ],
            'limit' => $limit ? [
                'daily_limit' => (int) $limit->daily_limit,
                'is_enabled'  => (bool) $limit->is_enabled,
                'reason'      => $limit->reason,
            ] : null,
            'latest' => $latest,
        ]);
    }

    /**
     * Save limit (back-compat + new UI).
     * Route: PATCH /admin/users/{user}/limits
     * Accepts: daily_limit (int), is_enabled (bool), reason (nullable string)
     */
    public function updateUserLimit(Request $request, User $user)
    {
        if (!Schema::hasTable('user_limits')) {
            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => 'user_limits table not found'], 422)
                : back()->with('error', 'user_limits table not found');
        }

        $data = $request->validate([
            'daily_limit' => 'nullable|integer|min:0|max:1000000',
            'is_enabled'  => 'nullable|boolean',
            'reason'      => 'nullable|string|max:255',
        ]);

        $current = UserLimit::where('user_id', $user->id)->first();

        $newDaily   = array_key_exists('daily_limit', $data) ? (int) $data['daily_limit'] : ($current->daily_limit ?? 200);
        $newEnabled = array_key_exists('is_enabled', $data)  ? (bool) $data['is_enabled']  : ($current->is_enabled ?? true);
        $newReason  = array_key_exists('reason', $data)      ? ($data['reason'] ?? null)   : ($current->reason ?? null);

        $limit = UserLimit::updateOrCreate(
            ['user_id' => $user->id],
            ['daily_limit' => $newDaily, 'is_enabled' => $newEnabled, 'reason' => $newReason]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'ok'    => true,
                'user'  => $user->only(['id','email']),
                'limit' => [
                    'daily_limit' => (int) $limit->daily_limit,
                    'is_enabled'  => (bool) $limit->is_enabled,
                    'reason'      => $limit->reason,
                ],
                'message' => 'User limit updated.',
            ]);
        }

        return back()->with('status', 'Limit updated');
    }

    /**
     * Toggle ban/unban (requires users.is_banned boolean column).
     * Route: PATCH /admin/users/{user}/ban
     */
    public function toggleBan(Request $request, User $user)
    {
        if (!Schema::hasColumn('users', 'is_banned')) {
            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => 'users.is_banned column not found'], 422)
                : back()->with('error', 'users.is_banned column not found');
        }

        $user->is_banned = ! (bool) $user->is_banned;
        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'ok'        => true,
                'user_id'   => $user->id,
                'is_banned' => (bool) $user->is_banned,
                'message'   => $user->is_banned ? 'User banned.' : 'User unbanned.',
            ]);
        }

        return back()->with('status', $user->is_banned ? 'User banned.' : 'User unbanned.');
    }

    /**
     * Recent sessions for a user (best-effort; uses 'sessions' table if present).
     * Route: GET /admin/users/{user}/sessions
     */
    public function sessions(User $user)
    {
        if (!Schema::hasTable('sessions')) {
            return response()->json(['rows' => []]);
        }

        // Default Laravel 'sessions' table: user_id (string), ip_address, last_activity (int timestamp)
        $rows = DB::table('sessions')
            ->where('user_id', (string) $user->getAuthIdentifier())
            ->orderByDesc('last_activity')
            ->limit(20)
            ->get(['ip_address', 'last_activity', 'user_agent'])
            ->map(function ($s) {
                $dt = $s->last_activity ? now()->createFromTimestamp($s->last_activity) : null;
                return [
                    'login_at'  => optional($dt)->format('Y-m-d H:i'),
                    'logout_at' => null, // not tracked in default sessions
                    'ip'        => $s->ip_address,
                    'ua'        => $s->user_agent,
                    'country'   => null,
                ];
            });

        return response()->json(['rows' => $rows]);
    }
}
