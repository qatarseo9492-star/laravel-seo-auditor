<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\UserLimit;

class UserLimitsController extends Controller
{
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'daily_limit' => ['nullable','integer','min:0'],
            'is_enabled'  => ['nullable','boolean'],
            'reason'      => ['nullable','string','max:255'],
        ]);

        if (!Schema::hasTable('user_limits')) {
            return back()->with('error', 'user_limits table not found');
        }

        $limit = UserLimit::firstOrCreate(['user_id'=>$user->id], ['daily_limit'=>200,'is_enabled'=>true]);
        if (array_key_exists('daily_limit',$validated) && $validated['daily_limit'] !== null) {
            $limit->daily_limit = (int)$validated['daily_limit'];
        }
        if (array_key_exists('is_enabled',$validated) && $validated['is_enabled'] !== null) {
            $limit->is_enabled = (bool)$validated['is_enabled'];
        }
        if (array_key_exists('reason',$validated)) {
            $limit->reason = $validated['reason'];
        }
        $limit->save();

        return back()->with('status','User limits updated.');
    }
}
