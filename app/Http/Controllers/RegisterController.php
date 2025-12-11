<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Models\UserDocument;
class RegisterController extends Controller
{
    // step 1
    public function showStep1()
    {
        return view('auth.register.step1');
    }

    public function processStep1(Request $request)
    {
        $validated = $request->validate([
            'nama_toko' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'nama_pic' => 'required|string|max:255',
            'hp_pic' => 'required|string|max:15',
            // 'email_pic' => 'required|email|unique:users,email',
            'email_pic' => 'required|email|unique:users,email_pic',

        ]);
        $request->session()->put('registration_data', $validated);

        return redirect()->route('register.step2');
    }

    // step 2
    public function showStep2(Request $request)
    {
        if (!$request->session()->has('registration_data')) {
            return redirect()->route('register.step1')
                ->with('error', 'Silakan isi langkah 1 terlebih dahulu.');
        }
        return view('auth.register.step2');
    }

    public function processStep2(Request $request)
    {
        $validated = $request->validate([
            'alamat_pic' => 'required|string|max:255',
            'rt' => 'required|string|max:10',
            'rw' => 'required|string|max:10',
            'kelurahan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
        ]);

        $merged = array_merge(
            $request->session()->get('registration_data'),
            $validated
        );
        $request->session()->put('registration_data', $merged);

        return redirect()->route('register.step3');
    }

    // step 3
    public function showStep3(Request $request)
    {
        if (!$request->session()->has('registration_data')) {
            return redirect()->route('register.step1');
        }

        return view('auth.register.step3');
    }

    public function processStep3(Request $request)
    {
        if (!$request->session()->has('registration_data')) {
            return redirect()->route('register.step1')
                ->with('error', 'Sesi pendaftaran hilang. Silakan mulai kembali.');
        }

        $validated = $request->validate([
            'nik' => ['required', 'string', 'digits:16', Rule::unique('users', 'nik')],
            'password' => 'required|min:8|confirmed',
            // Validasi untuk file: gambar, maks 5MB
            'foto_pic' => 'required|image|max:5120',
            'foto_ktp' => 'required|image|max:5120',
        ], [
            'nik.digits' => 'Nomor KTP harus terdiri dari 16 digit.',
            'nik.unique' => 'Nomor KTP ini sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'foto_pic.required' => 'Foto PIC wajib diunggah.',
            'foto_ktp.required' => 'File KTP PIC wajib diunggah.',
            'foto_pic.image' => 'File Foto PIC harus berupa gambar.',
            'foto_ktp.image' => 'File KTP PIC harus berupa gambar.',
            'foto_pic.max' => 'Ukuran file Foto PIC maksimal 5MB.',
            'foto_ktp.max' => 'Ukuran file KTP PIC maksimal 5MB.',
        ]);

        $registrationData = $request->session()->get('registration_data');

        // 1. Upload File
        // Catatan: Pastikan Anda telah mengkonfigurasi storage dan menjalankan php artisan storage:link
        $fotoPicFile = $request->file('foto_pic');
        $fotoKtpFile = $request->file('foto_ktp');

        // baca isi file sebagai binary dan encode ke base64
        $fotoPicBase64 = base64_encode(file_get_contents($fotoPicFile->getRealPath()));
        $fotoKtpBase64 = base64_encode(file_get_contents($fotoKtpFile->getRealPath()));

        $fotoPicMime = $fotoPicFile->getClientMimeType(); // contoh: image/jpeg
        $fotoKtpMime = $fotoKtpFile->getClientMimeType();
        // 2. Gabungkan data
        $finalData = array_merge(
            $registrationData,
            [
                'nik' => $validated['nik'],
                'password' => bcrypt($validated['password']),
                'role' => 'seller',
                'is_verified' => false,
            ]
        );

        // simpan ke db (samain sm model User)
        $user = User::create([
            'nama_toko' => $finalData['nama_toko'],
            'deskripsi' => $finalData['deskripsi'] ?? null,
            'nama_pic' => $finalData['nama_pic'],
            'no_hp' => $finalData['hp_pic'],
            'email_pic' => $finalData['email_pic'],
            'alamat_pic' => $finalData['alamat_pic'],
            'rt' => $finalData['rt'],
            'rw' => $finalData['rw'],
            'kelurahan' => $finalData['kelurahan'],
            'kecamatan' => $finalData['kecamatan'],
            'kabupaten' => $finalData['kabupaten'],
            'provinsi' => $finalData['provinsi'],
            'nik' => $finalData['nik'],
            'password' => $finalData['password'],
            // 'foto_pic_url' => $finalData['foto_pic_url'],
            // 'foto_ktp_url' => $finalData['foto_ktp_url'],
            'role' => $finalData['role'],
            'is_verified' => $finalData['is_verified'],
        ]);

        $user->documents()->create([
            'foto_pic' => $fotoPicBase64,
            'mime_pic' => $fotoPicMime,
            'foto_ktp' => $fotoKtpBase64,
            'mime_ktp' => $fotoKtpMime,
        ]);

        // bersihkan session
        $request->session()->forget('registration_data');

        return redirect()->route('register.success');
    }

    // pendaftaran berhasil
    public function showSuccess()
    {
        return view('auth.register.success');
    }
}