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
     * Kept for backward compatibility.
     */
    public function updateUserLimit(Request $request, User $user)
    {
        $data = $request->validate([
            'daily_limit' => 'nullable|integer|min:0',
            'is_enabled'  => 'nullable|boolean',
            'reason'      => 'nullable|string|max:255',
        ]);

        $limit = $user->limit()->first() ?: new UserLimit(['user_id' => $user->id]);

        if (array_key_exists('daily_limit', $data)) $limit->daily_limit = (int) $data['daily_limit'];
        if (array_key_exists('is_enabled', $data))  $limit->is_enabled  = (bool) $data['is_enabled'];
        if (array_key_exists('reason', $data))      $limit->reason      = $data['reason'];

        $limit->save();

        return request()->expectsJson()
            ? response()->json(['ok' => true])
            : back()->with('status', 'Limit updated');
    }

    /**
     * Toggle ban/unban: PATCH /admin/users/{user}/ban
     */
    public function toggleBan(Request $request, User $user)
    {
        $user->is_banned = !$user->is_banned;
        $user->save();

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'banned' => $user->is_banned])
            : back()->with('status', 'Ban status changed');
    }
}
