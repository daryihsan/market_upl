<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // <-- TAMBAHKAN INI UNTUK PENGGUNAAN HASH

class LoginController extends Controller
{
    // pilih login
    public function showPilih()
    {
        return view('auth.login.pilih');
    }

    // login penjual
    public function showLogin()
    {
        return view('auth.login.login');
    }

    public function processLogin(Request $request)
    {
        $request->validate([
            'email_pic' => 'required',
            'password' => 'required'
        ]);

        // filter role 'seller' saat mencari user
        $user = User::where('role', 'seller')
                     ->where(function($query) use ($request) {
                         $query->where('email_pic', $request->email_pic)
                               ->orWhere('nama_toko', $request->email_pic);
                     })
                     ->first();

        if ($user && Auth::attempt([
            'email_pic' => $user->email_pic, 
            'password' => $request->password
        ])) {
            // cek tambahan status akun
            if ($user->status_akun === 'active') { 
                return redirect()->route('seller.dashboard');
            } else {
                Auth::logout(); // logout jika akun belum aktif
                return back()->withErrors(['email_pic' => 'Akun Anda belum aktif. Silakan tunggu verifikasi.']);
            }
        }

        return back()->withErrors(['email_pic' => 'Email/nama toko atau password salah']);
    }

    // login admin (menggunakan data dari database yang di-seed)
    public function showAdmin()
    {
        return view('auth.login.admin');
    }

    public function processAdmin(Request $request)
    {
        $request->validate([
            'email_pic' => 'required',
            'password' => 'required'
        ]);

        // akun admin yang di-seed harus memiliki role='admin'
        $admin = User::where('role', 'admin')
                     ->where('email_pic', $request->email_pic)
                     ->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::login($admin); 
            
            return redirect()->route('platform.dashboard')->with('success','Login admin berhasil!');
        }
        // gagal
        return back()->withErrors(['email_pic' => 'Email atau password Admin salah']);
    }
}