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
    /**
     * PATCH /admin/users/{user}/limits
     * Body: { daily_limit:int, is_enabled:int|bool, reason?:string }
     * Returns JSON and never throws to the UI.
     */
    public function update(Request $request, User $user)
    {
        // Validate but allow partials so the UI can send only what changed
        $data = $request->validate([
            'daily_limit' => 'nullable|integer|min:0|max:1000000',
            'is_enabled'  => 'nullable|in:0,1,true,false',
            'reason'      => 'nullable|string|max:255',
        ]);

        // Normalize booleans/ints
        $newDaily   = array_key_exists('daily_limit', $data) ? (int) $data['daily_limit'] : null;
        $newEnabled = array_key_exists('is_enabled',  $data)
            ? (int) filter_var($data['is_enabled'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : null;
        $newReason  = array_key_exists('reason', $data) ? ($data['reason'] ?? null) : null;

        try {
            // Preferred: upsert into user_limits
            if (Schema::hasTable('user_limits')) {
                $current = UserLimit::where('user_id', $user->id)->first();

                $payload = [
                    'daily_limit' => $newDaily   ?? ($current?->daily_limit ?? 200),
                    'is_enabled'  => $newEnabled ?? ($current?->is_enabled  ?? 1),
                    'reason'      => $newReason  ?? ($current?->reason      ?? null),
                ];

                $limit = UserLimit::updateOrCreate(
                    ['user_id' => $user->id],
                    $payload
                );

                return response()->json([
                    'ok'    => true,
                    'user'  => $user->only(['id','email']),
                    'limit' => [
                        'daily_limit' => (int) $limit->daily_limit,
                        'is_enabled'  => (bool) $limit->is_enabled,
                        'reason'      => $limit->reason,
                    ],
                    'message' => 'Limit saved.',
                ]);
            }

            // Fallback: update columns on users table if they exist
            $updates = [];
            if ($newDaily !== null && Schema::hasColumn('users', 'daily_limit')) {
                $updates['daily_limit'] = $newDaily;
            }
            if ($newEnabled !== null && Schema::hasColumn('users', 'is_enabled')) {
                $updates['is_enabled'] = $newEnabled;
            }
            // Optional: if you have a reason column on users
            if ($newReason !== null && Schema::hasColumn('users', 'limit_reason')) {
                $updates['limit_reason'] = $newReason;
            }

            if (!empty($updates)) {
                DB::table('users')->where('id', $user->id)->update($updates);
                return response()->json([
                    'ok'      => true,
                    'user'    => $user->only(['id','email']),
                    'limit'   => [
                        'daily_limit' => $updates['daily_limit'] ?? null,
                        'is_enabled'  => isset($updates['is_enabled']) ? (bool) $updates['is_enabled'] : null,
                        'reason'      => $updates['limit_reason'] ?? null,
                    ],
                    'message' => 'Limit saved (fallback on users table).',
                ]);
            }

            // If neither table/columns exist, still return ok to avoid UI error
            return response()->json([
                'ok'      => true,
                'user'    => $user->only(['id','email']),
                'limit'   => null,
                'message' => 'No limit table/columns found; nothing changed.',
            ]);
        } catch (\Throwable $e) {
            // Return structured error so the UI can show something useful if needed
            return response()->json([
                'ok'      => false,
                'message' => 'Failed to save limit.',
                'error'   => app()->hasDebugModeEnabled() ? $e->getMessage() : null,
            ], 500);
        }
    }
}
