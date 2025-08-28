<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name'  => ['required','string','max:120'],
            'email' => ['required','email','max:190','unique:users,email,'.$user->id],
        ]);
        $user->fill($data)->save();
        return back()->with('ok','Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'current_password' => ['required'],
            'password'         => ['required','confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);
        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password'=>'Current password is incorrect.']);
        }
        $user->password = Hash::make($data['password']);
        $user->save();
        return back()->with('ok','Password changed.');
    }

    public function updateAvatar(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'avatar' => ['required','image','max:2048'], // 2MB
        ]);
        $path = $request->file('avatar')->store('public/avatars');
        // store relative path so Storage::url works
        $user->avatar = $path;
        $user->save();
        return back()->with('ok','Avatar updated.');
    }
}
