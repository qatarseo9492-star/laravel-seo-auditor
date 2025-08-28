<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required','string','max:120'],
        ]);

        $user->name = $data['name'];
        $user->save();

        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required','string'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('status', 'Password updated.');
    }

    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => ['required','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        // store to storage/app/public/avatars
        $path = $request->file('avatar')->store('avatars', 'public');

        // delete previous (optional)
        if ($user->avatar_path && file_exists(public_path('storage/'.$user->avatar_path))) {
            @unlink(public_path('storage/'.$user->avatar_path));
        }

        $user->avatar_path = $path;
        $user->save();

        return back()->with('status', 'Avatar updated.');
    }
}
