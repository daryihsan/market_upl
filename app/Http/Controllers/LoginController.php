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

        if (! $user) {
            return back()->withErrors(['email_pic' => 'Email atau Nama Toko tidak ditemukan'])->withInput();
        }

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password salah'])->withInput();
        }

        // Jika masih pending, tolak
        if ($user->status_akun === 'pending') {
            return back()->withErrors(['email_pic' => 'Akun Anda belum diverifikasi oleh admin. Silakan tunggu verifikasi.'])->withInput();
        }

        // Jika status 'rejected' dan dinonaktifkan oleh admin -> tolak
        if ($user->status_akun === 'rejected' && ($user->deactivated_by_admin ?? false)) {
            return back()->withErrors(['email_pic' => 'Akun Anda dinonaktifkan oleh admin. Hubungi admin untuk bantuan.'])->withInput();
        }

        Auth::login($user);
        return redirect()->route('seller.dashboard');
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

        if (! $admin) {
            return back()->withErrors(['email_pic' => 'Email Admin tidak ditemukan'])->withInput();
        }

        if (! Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['password' => 'Password Admin salah'])->withInput();
        }

        Auth::login($admin);
        return redirect()->route('platform.dashboard')->with('success','Login admin berhasil!');
    }
}