<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function index() {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request) {

        $request->validate([
            'name' => 'string|required',
            'email' => 'email|required|unique:users,email',
            'password' => 'min:8|required|confirmed',
            'no_hp' => 'string|required',
            'alamat' => 'string|required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'pelanggan',
        ]);

        Customer::create([
            'user_id' => $user->id,
            'nama' => $request->name,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Registrasi berhasil.');
    }
}
