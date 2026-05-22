<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.pilih');
    }
    public function toggleAccountStatus(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // Hanya izinkan aksi nonaktifkan jika status saat ini 'active'
        if ($user->status_akun !== 'active') {
             return redirect()->back()->with('info', 'Akun Anda tidak dapat dinonaktifkan secara mandiri karena status saat ini adalah ' . $user->status_akun);
        }

        // Set status ke 'rejected' (dianggap nonaktif mandiri)
        $user->status_akun = 'rejected'; 
        $user->deactivated_by_admin = false; // tandai nonaktif oleh user (bukan admin)
        $user->save();

        // Logout user setelah nonaktif
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.pilih')->with('success', 'Akun Anda telah dinonaktifkan.');
    }
}
