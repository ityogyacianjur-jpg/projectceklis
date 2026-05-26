<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Pastikan Anda membuat file blade resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'id_number' => ['required'],
            'password' => ['required'],
        ]);

        // Coba melakukan login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Jika berhasil, arahkan langsung ke halaman checklist
            return redirect()->intended('/');
        }

        // Jika gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'id_number' => 'Nomor ID atau password salah.',
        ])->onlyInput('id_number');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}