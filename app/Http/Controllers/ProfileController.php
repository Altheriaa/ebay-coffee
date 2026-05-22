<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index() {

        $customer = Auth::user();

        $user = User::with('customer')->where('id', $customer->id)->first();

        if (!$user) {
            return abort(404);
        }

        return Inertia::render('Profile/Show', [
            'user' => $user
        ]);
    }

    public function update(Request $request) {

        $user = Auth::user();

        $request->validate([
            'name' => 'string|required',
            'email' => 'email|required|unique:users,email,'.$user->id,
            'no_hp' => 'string|required',
            'alamat' => 'string|required',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $user->customer->update([
            'nama' => $request->name,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->back()->with('success', 'Profile berhasil diperbarui');
    }

    public function password(Request $request) {

        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors([
                'old_password' => 'Password lama salah',
            ]);
        }

        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password baru tidak boleh sama dengan password lama',
            ]);
        }

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diperbarui');
    }
}
