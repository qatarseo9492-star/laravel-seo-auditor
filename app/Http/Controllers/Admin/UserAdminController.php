<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLimit;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    /**
     * Update both daily and monthly limits for a user.
     */
    public function updateUserLimit(Request $request, User $user)
    {
        $validated = $request->validate([
            'daily_limit' => 'required|integer|min:0',
            'monthly_limit' => 'required|integer|min:0',
        ]);

        UserLimit::updateOrCreate(
            ['user_id' => $user->id],
            [
                'daily_limit' => $validated['daily_limit'],
                'monthly_limit' => $validated['monthly_limit']
            ]
        );

        return back()->with('success', "Limits for {$user->name} updated successfully.");
    }

    /**
     * Toggle the banned status of a user.
     */
    public function toggleBan(User $user)
    {
        $user->is_banned = !$user->is_banned;
        $user->save();
        
        $status = $user->is_banned ? 'banned' : 'unbanned';

        return back()->with('success', "User {$user->name} has been {$status}.");
    }
}
