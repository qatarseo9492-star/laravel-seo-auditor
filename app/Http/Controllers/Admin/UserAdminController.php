<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\UserLimit;

class UserAdminController extends Controller
{
    /**
     * “Users — Live” page.
     */
    public function index()
    {
        return view('admin.users');
    }

    /**
     * JSON: Top 20 users by last activity/seen with optional ?q= search.
     * Returns rows shaped for the UI:
     *  id, user, email, last_seen, ip, country, limit, enabled, banned
     */
    public function table(Request $request)
    {
        try {
            if (!Schema::hasTable('users')) {
                return response()->json(['rows' => []]);
            }

            $q = trim((string) $request->get('q', ''));

            // Discover optional columns/tables
            $hasAnalyze     = Schema::hasTable('analyze_logs');
            $hasLimits      = Schema::hasTable('user_limits');
            $hasLastSeen    = Schema::hasColumn('users', 'last_seen_at');
            $hasLastLogin   = Schema::hasColumn('users', 'last_login_at');
            $hasIp          = Schema::hasColumn('users', 'last_ip');
            $hasCountry     = Schema::hasColumn('users', 'country');
            $hasLastCountry = Schema::hasColumn('users', 'last_country'); // alternative name
            $hasBanned      = Schema::hasColumn('users', 'is_banned');
            $hasCreated     = Schema::hasColumn('users', 'created_at');

            // Base select — only include columns that exist
            $query = DB::table('users as u')->select('u.id', 'u.name', 'u.email');
            if ($hasCreated)   $query->addSelect('u.created_at');
            if ($hasLastSeen)  $query->addSelect('u.last_seen_at');
            if ($hasLastLogin) $query->addSelect('u.last_login_at');
            if ($hasIp)        $query->addSelect('u.last_ip');
            if ($hasCountry)   $query->addSelect('u.country');
            if (!$hasCountry && $hasLastCountry) {
                $query->addSelect(DB::raw('u.last_country as country'));
            }
            if ($hasBanned)    $query->addSelect('u.is_banned');

            // Latest analysis per user (subquery -> last_activity)
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

            // Limits
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

            // Order by best available timestamp (fallback to id desc)
            $orderParts = [];
            if ($hasLastSeen)  $orderParts[] = 'u.last_seen_at';
            if ($hasAnalyze)   $orderParts[] = 'a.last_activity';
            if ($hasLastLogin) $orderParts[] = 'u.last_login_at';
            if ($hasCreated)   $orderParts[] = 'u.created_at';

            if ($orderParts) {
                $query->orderByRaw('COALESCE(' . implode(',', $orderParts) . ') DESC');
            } else {
                $query->orderBy('u.id', 'desc');
            }

            $rows = $query->limit(20)->get();

            $out = $rows->map(function ($r) {
                // Fetch fields defensively; stdClass may not have the property
                $last = null;
                if (property_exists($r, 'last_seen_at')   && $r->last_seen_at)   $last = $r->last_seen_at;
                if (!$last && property_exists($r, 'last_login_at') && $r->last_login_at) $last = $r->last_login_at;
                if (!$last && property_exists($r, 'last_activity') && $r->last_activity) $last = $r->last_activity;
                if (!$last && property_exists($r, 'created_at')    && $r->created_at)    $last = $r->created_at;

                $ip      = property_exists($r, 'last_ip') ? $r->last_ip : null;
                $country = property_exists($r, 'country') ? $r->country : null;
                $limit   = property_exists($r, 'daily_limit') ? $r->daily_limit : null;
                $enabled = property_exists($r, 'is_enabled') ? (int) $r->is_enabled : 1;
                $banned  = property_exists($r, 'is_banned')  ? (bool) $r->is_banned : false;

                return [
                    'id'        => (int) $r->id,
                    'user'      => $r->name ?: $r->email,
                    'email'     => $r->email,
                    'last_seen' => $this->fmt($last),
                    'ip'        => $ip ?: '—',
                    'country'   => $country ?: null,
                    'limit'     => $limit,
                    'enabled'   => $enabled,
                    'banned'    => $banned,
                ];
            });

            return response()->json(['rows' => $out]);
        } catch (\Throwable $e) {
            // Never 500 the UI; send empty set + hint so you can see the issue in Network → Preview
            return response()->json([
                'rows'  => [],
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Drawer JSON for a single user (user + limit + latest activity).
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
                        'created_at' => $this->fmt($r->created_at),
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
                'last_seen_at'  => Schema::hasColumn('users', 'last_seen_at') ? $this->fmt($user->last_seen_at) : null,
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
     * PATCH /admin/users/{user}/limits
     * Accepts: daily_limit (int), is_enabled (bool), reason (string|null)
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

        $newDaily   = array_key_exists('daily_limit', $data) ? (int) $data['daily_limit'] : ($current->daily_limit ?? 1000);
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
     * PATCH /admin/users/{user}/ban
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
     * GET /admin/users/{user}/sessions
     * Prefers app-level user_sessions (TouchPresence), falls back to Laravel's sessions table.
     * This version detects available columns dynamically to avoid SQL errors.
     */
    public function sessions(User $user)
    {
        try {
            // Prefer app-level table populated by TouchPresence middleware
            if (Schema::hasTable('user_sessions')) {
                $cols = $this->existingColumns('user_sessions', [
                    'login_at','logout_at','ip','ip_address','country','country_code','updated_at','created_at'
                ]);

                // Build SELECT list from available columns (use aliases for alternates)
                $select = [];
                if (isset($cols['login_at']))   $select[] = 's.login_at';
                if (isset($cols['logout_at']))  $select[] = 's.logout_at';
                if (isset($cols['ip']))         $select[] = 's.ip';
                if (!isset($cols['ip']) && isset($cols['ip_address'])) $select[] = DB::raw('s.ip_address as ip');
                if (isset($cols['country']))    $select[] = 's.country';
                if (!isset($cols['country']) && isset($cols['country_code'])) $select[] = DB::raw('s.country_code as country');
                if (isset($cols['updated_at'])) $select[] = 's.updated_at';
                if (isset($cols['created_at'])) $select[] = 's.created_at';

                if (empty($select)) {
                    // If schema is very custom, at least select everything
                    $select = [DB::raw('s.*')];
                }

                // Order by best timestamp we can find
                $orderParts = [];
                if (isset($cols['updated_at']))  $orderParts[] = 's.updated_at';
                if (isset($cols['login_at']))    $orderParts[] = 's.login_at';
                if (isset($cols['created_at']))  $orderParts[] = 's.created_at';

                $query = DB::table('user_sessions as s')->where('s.user_id', $user->id);

                if ($orderParts) {
                    $query->orderByRaw('COALESCE(' . implode(',', $orderParts) . ') DESC');
                } else {
                    $query->orderByDesc('s.id');
                }

                $rows = $query->limit(20)->get($select)->map(function ($s) {
                    // Normalize fields defensively
                    $login   = $this->prop($s, 'login_at') ?? $this->prop($s, 'created_at') ?? $this->prop($s, 'updated_at');
                    $logout  = $this->prop($s, 'logout_at');
                    $ip      = $this->prop($s, 'ip') ?? $this->prop($s, 'ip_address');
                    $country = $this->prop($s, 'country') ?? $this->prop($s, 'country_code');

                    return [
                        'login_at'  => $this->fmt($login),
                        'logout_at' => $this->fmt($logout),
                        'ip'        => $ip,
                        'country'   => $country,
                    ];
                });

                return response()->json(['rows' => $rows]);
            }

            // Fallback: Laravel database-backed sessions (SESSION_DRIVER=database)
            if (Schema::hasTable('sessions')) {
                $rows = DB::table('sessions')
                    ->where('user_id', (string) $user->getAuthIdentifier())
                    ->orderByDesc('last_activity')
                    ->limit(20)
                    ->get(['ip_address', 'last_activity', 'user_agent'])
                    ->map(function ($s) {
                        $dt = $s->last_activity ? Carbon::createFromTimestamp($s->last_activity) : null;
                        return [
                            'login_at'  => $this->fmt($dt),
                            'logout_at' => null, // not tracked in default sessions table
                            'ip'        => $s->ip_address,
                            'country'   => null,
                        ];
                    });

                return response()->json(['rows' => $rows]);
            }

            // No session tables available
            return response()->json(['rows' => []]);
        } catch (\Throwable $e) {
            // Never 500 the UI
            return response()->json(['rows' => [], 'error' => $e->getMessage()], 200);
        }
    }

    /**
     * PATCH /admin/users/{user}/upgrade
     * Upgrade a user's plan (works with subscriptions table or users.plan fallback).
     */
    public function upgrade(Request $request, User $user)
    {
        $data = $request->validate([
            'plan'      => 'required|string|max:100',
            'provider'  => 'nullable|string|max:50',
            'mrr_cents' => 'nullable|integer|min:0',
            'reason'    => 'nullable|string|max:255',
        ]);

        $plan     = $data['plan'];
        $provider = $data['provider'] ?? 'local';
        $mrrCents = $data['mrr_cents'] ?? null;
        $reason   = $data['reason'] ?? null;

        $persisted = false;
        $payload   = ['plan' => $plan];

        if (Schema::hasTable('subscriptions')) {
            $cols = [
                'user_id'   => Schema::hasColumn('subscriptions','user_id'),
                'plan'      => Schema::hasColumn('subscriptions','plan'),
                'status'    => Schema::hasColumn('subscriptions','status'),
                'provider'  => Schema::hasColumn('subscriptions','provider'),
                'mrr_cents' => Schema::hasColumn('subscriptions','mrr_cents'),
                'reason'    => Schema::hasColumn('subscriptions','reason'),
                'updated_at'=> Schema::hasColumn('subscriptions','updated_at'),
                'created_at'=> Schema::hasColumn('subscriptions','created_at'),
            ];

            if ($cols['user_id'] && $cols['plan']) {
                $upd = ['plan' => $plan];
                if ($cols['status'])    $upd['status']    = 'active';
                if ($cols['provider'])  $upd['provider']  = $provider;
                if ($cols['mrr_cents'] && $mrrCents !== null) $upd['mrr_cents'] = (int) $mrrCents;
                if ($cols['reason'])    $upd['reason']    = $reason;

                $query = DB::table('subscriptions')->where('user_id', $user->id);
                if ($cols['status']) $query->where('status', 'active');
                $existing = $query->first();

                if ($existing) {
                    DB::table('subscriptions')->where('id', $existing->id)
                        ->update($upd + ($cols['updated_at'] ? ['updated_at' => now()] : []));
                } else {
                    $ins = ['user_id' => $user->id] + $upd;
                    if ($cols['created_at']) $ins['created_at'] = now();
                    if ($cols['updated_at']) $ins['updated_at'] = now();
                    DB::table('subscriptions')->insert($ins);
                }

                $persisted = true;
                $payload['provider'] = $provider;
                if ($mrrCents !== null) $payload['mrr_cents'] = (int) $mrrCents;
            }
        }

        if (!$persisted) {
            $col = null;
            if (Schema::hasColumn('users', 'plan')) {
                $col = 'plan';
            } elseif (Schema::hasColumn('users', 'subscription_plan')) {
                $col = 'subscription_plan';
            }

            if ($col) {
                $user->{$col} = $plan;
                $user->save();
                $persisted = true;
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok'       => (bool) $persisted,
                'user_id'  => $user->id,
                'plan'     => $plan,
                'details'  => $payload,
                'message'  => $persisted ? "User upgraded to {$plan}." : 'No subscription storage available to persist upgrade.',
            ], $persisted ? 200 : 422);
        }

        return back()->with($persisted ? 'status' : 'error', $persisted ? "User upgraded to {$plan}." : 'No subscription storage available to persist upgrade.');
    }

    /**
     * Safe formatter for timestamps that may be strings, ints, or Carbon.
     */
    private function fmt($ts): ?string
    {
        if (!$ts) return null;
        try {
            if ($ts instanceof \DateTimeInterface) {
                return Carbon::instance($ts)->format('Y-m-d H:i');
            }
            if (is_numeric($ts) && strlen((string) $ts) <= 10) {
                return Carbon::createFromTimestamp((int) $ts)->format('Y-m-d H:i');
            }
            return Carbon::parse((string) $ts)->format('Y-m-d H:i');
        } catch (\Throwable $e) {
            return is_string($ts) ? $ts : null;
        }
    }

    /**
     * Get a map of which of the given columns exist on $table.
     * @return array<string,bool>
     */
    private function existingColumns(string $table, array $candidates): array
    {
        $out = [];
        foreach ($candidates as $c) {
            if (Schema::hasColumn($table, $c)) $out[$c] = true;
        }
        return $out;
    }

    /**
     * Safe property accessor on stdClass.
     */
    private function prop(object $o, string $key)
    {
        return property_exists($o, $key) ? $o->{$key} : null;
    }
}
