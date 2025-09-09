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
     * “Users — Live” page (table loads via admin.users.table JSON).
     */
    public function index()
    {
        return view('admin.users');
    }

    /**
     * Legacy endpoint you already had: PATCH /admin/users/{user}/limit
     * Kept for backward compatibility with old UI.
     *
     * Accepts: daily_limit (int), is_enabled (bool), reason (string|null)
     * Creates the user_limits row if it doesn't exist.
     */
    public function updateUserLimit(Request $request, User $user)
    {
        // Accept partial updates to stay backward compatible
        $data = $request->validate([
            'daily_limit' => 'nullable|integer|min:0|max:1000000',
            'is_enabled'  => 'nullable|boolean',
            'reason'      => 'nullable|string|max:255',
        ]);

        // Read current record or defaults
        $current = $user->limit()->first();

        // Compute new values using incoming data or fallbacks
        $newDaily   = array_key_exists('daily_limit', $data)
                        ? (int) $data['daily_limit']
                        : ($current?->daily_limit ?? 200);
        $newEnabled = array_key_exists('is_enabled', $data)
                        ? (bool) $data['is_enabled']
                        : ($current?->is_enabled ?? true);
        $newReason  = array_key_exists('reason', $data)
                        ? ($data['reason'] ?? null)
                        : ($current?->reason ?? null);

        // Upsert to guarantee row exists
        $limit = UserLimit::updateOrCreate(
            ['user_id' => $user->id],
            [
                'daily_limit' => $newDaily,
                'is_enabled'  => $newEnabled,
                'reason'      => $newReason,
            ]
        );

        // JSON (new UI) or redirect back (old UI)
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
     * Toggle ban/unban: PATCH /admin/users/{user}/ban
     */
    public function toggleBan(Request $request, User $user)
    {
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
}
