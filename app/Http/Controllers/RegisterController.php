<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\UserDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    // step 1
    public function showStep1(Request $request)
    {
        $registrationData = $request->session()->get('registration_data', []);
        return view('auth.register.step1', compact('registrationData'));
    }

    public function processStep1(Request $request)
    {
        // Custom validation untuk No HP
        $hp = trim($request->input('hp_pic', ''));
        
        $validator = Validator::make($request->all(), [
            'nama_toko' => 'required|string|max:255|unique:users,nama_toko',
            'deskripsi' => 'required|string|max:500',
            'nama_pic' => 'required|string|max:255',
            'hp_pic' => 'required|string',
            'email_pic' => 'required|email|unique:users,email_pic',
        ], [
            'nama_toko.required' => 'Nama Toko wajib diisi',
            'nama_toko.unique' => 'Nama toko sudah digunakan, silakan gunakan nama lain',
            'deskripsi.required' => 'Deskripsi Singkat wajib diisi',
            'nama_pic.required' => 'Nama PIC wajib diisi',
            'hp_pic.required' => 'No Handphone PIC wajib diisi',
            'email_pic.required' => 'Email PIC wajib diisi',
            'email_pic.email' => 'Format email tidak valid',
            'email_pic.unique' => 'Email sudah terdaftar, silakan gunakan email lain',
        ]);

        // Custom validation untuk No HP
        $validator->after(function ($validator) use ($hp) {
            if (!empty($hp)) {
                // Check if not numeric
                if (!is_numeric($hp)) {
                    $validator->errors()->add('hp_pic', 'No HP hanya boleh berisi angka');
                    return;
                }
                
                $length = strlen($hp);
                
                // Check length
                if ($length < 10) {
                    $validator->errors()->add('hp_pic', 'No HP terlalu pendek, minimal 10 digit');
                    return;
                }
                if ($length > 13) {
                    $validator->errors()->add('hp_pic', 'No HP terlalu panjang, maksimal 13 digit');
                    return;
                }
                
                // Check if starts with 08
                if (!str_starts_with($hp, '08')) {
                    $validator->errors()->add('hp_pic', 'No HP tidak sesuai, harus dimulai dengan 08xx');
                    return;
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        // MERGE dengan existing session data, jangan overwrite
        $request->session()->put('registration_data', array_merge(
            $request->session()->get('registration_data', []),
            $validated
        ));

        return redirect()->route('register.step2');
    }

    // step 2
    public function showStep2(Request $request)
    {
        if (!$request->session()->has('registration_data')) {
            return redirect()->route('register.step1')
                ->with('error', 'Silakan isi langkah 1 terlebih dahulu.');
        }
        $registrationData = $request->session()->get('registration_data', []);
        return view('auth.register.step2', compact('registrationData'));
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
        ], [
            'alamat_pic.required' => 'Alamat PIC wajib diisi',
            'rt.required' => 'RT wajib diisi',
            'rw.required' => 'RW wajib diisi',
            'kelurahan.required' => 'Nama Kelurahan wajib diisi',
            'kecamatan.required' => 'Nama Kecamatan wajib diisi',
            'kabupaten.required' => 'Kabupaten/Kota wajib diisi',
            'provinsi.required' => 'Propinsi wajib diisi',
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
        $registrationData = $request->session()->get('registration_data', []);
        return view('auth.register.step3', compact('registrationData'));
    }

    public function processStep3(Request $request)
    {
        if (!$request->session()->has('registration_data')) {
            return redirect()->route('register.step1')
                ->with('error', 'Sesi pendaftaran hilang. Silakan mulai kembali.');
        }

        $nik = $request->input('nik', '');
        
        $validator = Validator::make($request->all(), [
            'nik' => ['required', 'string', Rule::unique('users', 'nik')],
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'foto_pic' => 'required|mimes:jpg,jpeg,png|max:5120',
            'foto_ktp' => 'required|mimes:jpg,jpeg,png|max:5120',
        ], [
            'nik.required' => 'No KTP PIC wajib diisi',
            'nik.unique' => 'No KTP sudah terdaftar',
            'password.required' => 'Kata Sandi wajib diisi',
            'password.min' => 'Kata Sandi minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
            'password_confirmation.required' => 'Konfirmasi Kata Sandi wajib diisi',
            'foto_pic.required' => 'Foto PIC wajib di-upload',
            'foto_pic.mimes' => 'Format foto harus JPG atau PNG atau JPEG',
            'foto_pic.max' => 'Ukuran foto melebihi batas maksimal',
            'foto_ktp.required' => 'File KTP PIC wajib di-upload',
            'foto_ktp.mimes' => 'Format file KTP harus JPG, PNG, atau JPEG',
            'foto_ktp.max' => 'Ukuran file KTP melebihi batas maksimal',
        ]);

        // Custom validation untuk NIK
        $validator->after(function ($validator) use ($nik) {
            if (!empty($nik)) {
                if (!is_numeric($nik)) {
                    $validator->errors()->add('nik', 'No KTP hanya boleh berisi angka');
                    return;
                }

                $length = strlen($nik);

                // Check length
                if ($length < 16) {
                    $validator->errors()->add('nik', 'No KTP terlalu pendek, minimal 16 digit');
                    return;
                }
                if ($length > 16) {
                    $validator->errors()->add('nik', 'No KTP terlalu panjang, maksimal 16 digit');
                    return;
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
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

    // File: app/Http/Controllers/RegisterController.php

    public function getKabupaten($provinsi)
    {
        $data = [
            'Aceh' => [
                ['id' => 1, 'nama' => 'Kota Banda Aceh'],
                ['id' => 2, 'nama' => 'Kota Langsa'],
                ['id' => 3, 'nama' => 'Kota Lhokseumawe'],
                ['id' => 4, 'nama' => 'Kota Sabang'],
                ['id' => 5, 'nama' => 'Kota Subulussalam'],
                ['id' => 6, 'nama' => 'Kabupaten Aceh Barat'],
                ['id' => 7, 'nama' => 'Kabupaten Aceh Barat Daya'],
                ['id' => 8, 'nama' => 'Kabupaten Aceh Besar'],
                ['id' => 9, 'nama' => 'Kabupaten Aceh Jaya'],
                ['id' => 10, 'nama' => 'Kabupaten Aceh Selatan'],
                ['id' => 11, 'nama' => 'Kabupaten Aceh Singkil'],
                ['id' => 12, 'nama' => 'Kabupaten Aceh Tamiang'],
                ['id' => 13, 'nama' => 'Kabupaten Aceh Tengah'],
                ['id' => 14, 'nama' => 'Kabupaten Aceh Tenggara'],
                ['id' => 15, 'nama' => 'Kabupaten Aceh Timur'],
                ['id' => 16, 'nama' => 'Kabupaten Aceh Utara'],
                ['id' => 17, 'nama' => 'Kabupaten Bener Meriah'],
                ['id' => 18, 'nama' => 'Kabupaten Bireuen'],
                ['id' => 19, 'nama' => 'Kabupaten Gayo Lues'],
                ['id' => 20, 'nama' => 'Kabupaten Nagan Raya'],
                ['id' => 21, 'nama' => 'Kabupaten Pidie'],
                ['id' => 22, 'nama' => 'Kabupaten Pidie Jaya'],
                ['id' => 23, 'nama' => 'Kabupaten Simeulue'],
            ],
            'Sumatera Utara' => [
                ['id' => 24, 'nama' => 'Kota Binjai'],
                ['id' => 25, 'nama' => 'Kota Medan'],
                ['id' => 26, 'nama' => 'Kota Gunungsitoli'],
                ['id' => 27, 'nama' => 'Kota Padangsidempuan'],
                ['id' => 28, 'nama' => 'Kota Pematangsiantar'],
                ['id' => 29, 'nama' => 'Kota Sibolga'],
                ['id' => 30, 'nama' => 'Kota Tanjungbalai'],
                ['id' => 31, 'nama' => 'Kota Tebing Tinggi'],
                ['id' => 32, 'nama' => 'Kabupaten Asahan'],
                ['id' => 33, 'nama' => 'Kabupaten Batubara'],
                ['id' => 34, 'nama' => 'Kabupaten Dairi'],
                ['id' => 35, 'nama' => 'Kabupaten Deliserdang'],
                ['id' => 36, 'nama' => 'Kabupaten Humbang Hasundutan'],
                ['id' => 37, 'nama' => 'Kabupaten Karo'],
                ['id' => 38, 'nama' => 'Kabupaten Labuhanbatu'],
                ['id' => 39, 'nama' => 'Kabupaten Langkat'],
                ['id' => 40, 'nama' => 'Kabupaten Mandailing Natal'],
                ['id' => 41, 'nama' => 'Kabupaten Nias'],
                ['id' => 42, 'nama' => 'Kabupaten Nias Barat'],
                ['id' => 43, 'nama' => 'Kabupaten Nias Selatan'],
                ['id' => 44, 'nama' => 'Kabupaten Pakpak Bharat'],
                ['id' => 45, 'nama' => 'Kabupaten Samosir'],
                ['id' => 46, 'nama' => 'Kabupaten Serdang Bedagai'],
                ['id' => 47, 'nama' => 'Kabupaten Simalungun'],
                ['id' => 48, 'nama' => 'Kabupaten Tapanuli Selatan'],
                ['id' => 49, 'nama' => 'Kabupaten Tapanuli Tengah'],
                ['id' => 50, 'nama' => 'Kabupaten Tapanuli Utara'],
                ['id' => 51, 'nama' => 'Kabupaten Toba'],
            ],
            'Sumatera Barat' => [
                ['id' => 52, 'nama' => 'Kota Bukittinggi'],
                ['id' => 53, 'nama' => 'Kota Padang'],
                ['id' => 54, 'nama' => 'Kota Padangpanjang'],
                ['id' => 55, 'nama' => 'Kota Pariaman'],
                ['id' => 56, 'nama' => 'Kota Payakumbuh'],
                ['id' => 57, 'nama' => 'Kota Sawahlunto'],
                ['id' => 58, 'nama' => 'Kota Solok'],
                ['id' => 59, 'nama' => 'Kota Solok Selatan'],
                ['id' => 60, 'nama' => 'Kota Sungai Penuh'],
                ['id' => 61, 'nama' => 'Kabupaten Agam'],
                ['id' => 62, 'nama' => 'Kabupaten Dharmasraya'],
                ['id' => 63, 'nama' => 'Kabupaten Kepulauan Mentawai'],
                ['id' => 64, 'nama' => 'Kabupaten Lima Puluh Kota'],
                ['id' => 65, 'nama' => 'Kabupaten Padang Pariaman'],
                ['id' => 66, 'nama' => 'Kabupaten Pasaman'],
                ['id' => 67, 'nama' => 'Kabupaten Pasaman Barat'],
                ['id' => 68, 'nama' => 'Kabupaten Pesisir Selatan'],
                ['id' => 69, 'nama' => 'Kabupaten Sijunjung'],
                ['id' => 70, 'nama' => 'Kabupaten Solok'],
                ['id' => 71, 'nama' => 'Kabupaten Tanah Datar'],
            ],
            'Sumatera Selatan' => [
                ['id' => 72, 'nama' => 'Kota Pagar Alam'],
                ['id' => 73, 'nama' => 'Kota Lubuklinggau'],
                ['id' => 74, 'nama' => 'Kota Palembang'],
                ['id' => 75, 'nama' => 'Kota Prabumulih'],
                ['id' => 76, 'nama' => 'Kota Kayuagung'],
                ['id' => 77, 'nama' => 'Kota Martapura'],
                ['id' => 78, 'nama' => 'Kota Sekayu'],
                ['id' => 79, 'nama' => 'Kabupaten Muara Enim'],
                ['id' => 80, 'nama' => 'Kabupaten Banyuasin'],
                ['id' => 81, 'nama' => 'Kabupaten Empat Lawang'],
                ['id' => 82, 'nama' => 'Kabupaten Lahat'],
                ['id' => 83, 'nama' => 'Kabupaten Musi Banyuasin'],
                ['id' => 84, 'nama' => 'Kabupaten Musi Rawas'],
                ['id' => 85, 'nama' => 'Kabupaten Ogan Ilir'],
                ['id' => 86, 'nama' => 'Kabupaten Ogan Komering Ilir'],
                ['id' => 87, 'nama' => 'Kabupaten Ogan Komering Ulu'],
                ['id' => 88, 'nama' => 'Kabupaten Ogan Komering Ulu Selatan'],
                ['id' => 89, 'nama' => 'Kabupaten Ogan Komering Ulu Timur'],
            ],
            'Riau' => [
                ['id' => 90, 'nama' => 'Kota Dumai'],
                ['id' => 91, 'nama' => 'Kota Pekanbaru'],
                ['id' => 92, 'nama' => 'Kabupaten Bengkalis'],
                ['id' => 93, 'nama' => 'Kabupaten Indragiri Hilir'],
                ['id' => 94, 'nama' => 'Kabupaten Indragiri Hulu'],
                ['id' => 95, 'nama' => 'Kabupaten Kampar'],
                ['id' => 96, 'nama' => 'Kabupaten Kepulauan Meranti'],
                ['id' => 97, 'nama' => 'Kabupaten Kuantan Singingi'],
                ['id' => 98, 'nama' => 'Kabupaten Pelalawan'],
                ['id' => 99, 'nama' => 'Kabupaten Rokan Hilir'],
                ['id' => 100, 'nama' => 'Kabupaten Rokan Hulu'],
                ['id' => 101, 'nama' => 'Kabupaten Siak'],
            ],
            'Kepulauan Riau' => [
                ['id' => 102, 'nama' => 'Kota Batam'],
                ['id' => 103, 'nama' => 'Kota Tanjung Pinang'],
                ['id' => 103, 'nama' => 'Kabupaten Bintan'],
                ['id' => 104, 'nama' => 'Kabupaten Karimun'],
                ['id' => 105, 'nama' => 'Kabupaten Lingga'],
                ['id' => 106, 'nama' => 'Kabupaten Natuna'],
                ['id' => 107, 'nama' => 'Kabupaten Anambas'],
            ],
            'Jambi' => [
                ['id' => 108, 'nama' => 'Kota Jambi'],
                ['id' => 109, 'nama' => 'Kota Sungai Penuh'],
                ['id' => 109, 'nama' => 'Kota Sungai Penuh'],
                ['id' => 110, 'nama' => 'Kabupaten Batanghari'],
                ['id' => 111, 'nama' => 'Kabupaten Bungo'],
                ['id' => 112, 'nama' => 'Kabupaten Kerinci'],
                ['id' => 113, 'nama' => 'Kabupaten Merangin'],
                ['id' => 114, 'nama' => 'Kabupaten Muaro Jambi'],
                ['id' => 115, 'nama' => 'Kabupaten Sarolangun'],
                ['id' => 116, 'nama' => 'Kabupaten Tanjung Jabung Barat'],
                ['id' => 117, 'nama' => 'Kabupaten Tanjung Jabung Timur'],
            ],
            'Bengkulu' => [
                ['id' => 118, 'nama' => 'Kota Bengkulu'],
                ['id' => 119, 'nama' => 'Kabupaten Bengkulu Selatan'],
                ['id' => 120, 'nama' => 'Kabupaten Bengkulu Tengah'],
                ['id' => 121, 'nama' => 'Kabupaten Bengkulu Utara'],
                ['id' => 122, 'nama' => 'Kabupaten Kaur'],
                ['id' => 123, 'nama' => 'Kabupaten Kepahiang'],
                ['id' => 124, 'nama' => 'Kabupaten Lebong'],
                ['id' => 125, 'nama' => 'Kabupaten Mukomuko'],
                ['id' => 126, 'nama' => 'Kabupaten Rejang Lebong'],
                ['id' => 127, 'nama' => 'Kabupaten Seluma'],
            ],
            'Lampung' => [
                ['id' => 128, 'nama' => 'Kota Bandar Lampung'],
                ['id' => 129, 'nama' => 'Kota Metro'],
                ['id' => 130, 'nama' => 'Kabupaten Lampung Barat'],
                ['id' => 131, 'nama' => 'Kabupaten Lampung Selatan'],
                ['id' => 132, 'nama' => 'Kabupaten Lampung Tengah'],
                ['id' => 133, 'nama' => 'Kabupaten Lampung Timur'],
                ['id' => 134, 'nama' => 'Kabupaten Lampung Utara'],
                ['id' => 135, 'nama' => 'Kabupaten Mesuji'],
                ['id' => 136, 'nama' => 'Kabupaten Pesawaran'],
                ['id' => 137, 'nama' => 'Kabupaten Pesisir Barat'],
                ['id' => 138, 'nama' => 'Kabupaten Pringsewu'],
                ['id' => 139, 'nama' => 'Kabupaten Tanggamus'],
                ['id' => 140, 'nama' => 'Kabupaten Tulangbawang'],
                ['id' => 141, 'nama' => 'Kabupaten Tulangbawang Barat'],
                ['id' => 142, 'nama' => 'Kabupaten Way Kanan'],
            ],
            'Bangka Belitung' => [
                ['id' => 143, 'nama' => 'Kabupaten Bangka'],
                ['id' => 144, 'nama' => 'Kabupaten Belitung'],
                ['id' => 145, 'nama' => 'Kota Pangkal Pinang'],
                ['id' => 146, 'nama' => 'Kabupaten Bangka Selatan'],
                ['id' => 147, 'nama' => 'Kabupaten Bangka Tengah'],
                ['id' => 148, 'nama' => 'Kabupaten Bangka Barat'],
                ['id' => 149, 'nama' => 'Kabupaten Belitung Timur'],
            ],
            'Banten' => [
                ['id' => 150, 'nama' => 'Kota Tangerang'],
                ['id' => 151, 'nama' => 'Kota Tangerang Selatan'],
                ['id' => 152, 'nama' => 'Kota Cilegon'],
                ['id' => 153, 'nama' => 'Kota Serang'],
                ['id' => 154, 'nama' => 'Kota Pandeglang'],
                ['id' => 155, 'nama' => 'Kabupaten Lebak'],
                ['id' => 156, 'nama' => 'Kabupaten Pandeglang'],
                ['id' => 157, 'nama' => 'Kabupaten Serang'],
                ['id' => 158, 'nama' => 'Kabupaten Tangerang'],
            ],
            'Jawa Barat' => [
                ['id' => 159, 'nama' => 'Kota Bandung'],
                ['id' => 160, 'nama' => 'Kota Bekasi'],
                ['id' => 161, 'nama' => 'Kota Bogor'],
                ['id' => 162, 'nama' => 'Kota Cimahi'],
                ['id' => 163, 'nama' => 'Kota Cirebon'],
                ['id' => 164, 'nama' => 'Kota Depok'],
                ['id' => 165, 'nama' => 'Kota Sukabumi'],
                ['id' => 166, 'nama' => 'Kota Tasikmalaya'],
                ['id' => 167, 'nama' => 'Kabupaten Bandung'],
                ['id' => 168, 'nama' => 'Kabupaten Bandung Barat'],
                ['id' => 169, 'nama' => 'Kabupaten Bekasi'],
                ['id' => 170, 'nama' => 'Kabupaten Bogor'],
                ['id' => 171, 'nama' => 'Kabupaten Ciamis'],
                ['id' => 172, 'nama' => 'Kabupaten Cianjur'],
                ['id' => 173, 'nama' => 'Kabupaten Cirebon'],
                ['id' => 174, 'nama' => 'Kabupaten Garut'],
                ['id' => 175, 'nama' => 'Kabupaten Indramayu'],
                ['id' => 176, 'nama' => 'Kabupaten Karawang'],
                ['id' => 177, 'nama' => 'Kabupaten Kuningan'],
                ['id' => 178, 'nama' => 'Kabupaten Majalengka'],
                ['id' => 179, 'nama' => 'Kabupaten Pangandaran'],
                ['id' => 180, 'nama' => 'Kabupaten Purwakarta'],
                ['id' => 181, 'nama' => 'Kabupaten Subang'],
                ['id' => 182, 'nama' => 'Kabupaten Sukabumi'],
                ['id' => 183, 'nama' => 'Kabupaten Sumedang'],
                ['id' => 184, 'nama' => 'Kabupaten Tasikmalaya'],
            ],
            'Jawa Tengah' => [
                ['id' => 185, 'nama' => 'Kota Semarang'],
                ['id' => 186, 'nama' => 'Kota Surakarta'],
                ['id' => 187, 'nama' => 'Kota Salatiga'],
                ['id' => 188, 'nama' => 'Kota Magelang'],
                ['id' => 189, 'nama' => 'Kota Pekalongan'],
                ['id' => 190, 'nama' => 'Kota Tegal'], 
                ['id' => 191, 'nama' => 'Kabupaten Banjarnegara'],
                ['id' => 192, 'nama' => 'Kabupaten Banyumas'],
                ['id' => 193, 'nama' => 'Kabupaten Batang'],
                ['id' => 194, 'nama' => 'Kabupaten Blora'],
                ['id' => 195, 'nama' => 'Kabupaten Boyolali'],
                ['id' => 196, 'nama' => 'Kabupaten Brebes'],
                ['id' => 197, 'nama' => 'Kabupaten Cilacap'],
                ['id' => 198, 'nama' => 'Kabupaten Demak'],
                ['id' => 199, 'nama' => 'Kabupaten Grobogan'],
                ['id' => 200, 'nama' => 'Kabupaten Jepara'],
                ['id' => 201, 'nama' => 'Kabupaten Karanganyar'],
                ['id' => 202, 'nama' => 'Kabupaten Kebumen'],
                ['id' => 203, 'nama' => 'Kabupaten Kendal'],
                ['id' => 204, 'nama' => 'Kabupaten Klaten'],
                ['id' => 205, 'nama' => 'Kabupaten Kudus'],
                ['id' => 206, 'nama' => 'Kabupaten Magelang'],
                ['id' => 207, 'nama' => 'Kabupaten Pati'],
                ['id' => 208, 'nama' => 'Kabupaten Pekalongan'],
                ['id' => 209, 'nama' => 'Kabupaten Pemalang'],
                ['id' => 210, 'nama' => 'Kabupaten Purbalingga'],
                ['id' => 211, 'nama' => 'Kabupaten Rembang'],
                ['id' => 212, 'nama' => 'Kabupaten Semarang'],
                ['id' => 213, 'nama' => 'Kabupaten Sragen'],
                ['id' => 214, 'nama' => 'Kabupaten Sukoharjo'],
                ['id' => 215, 'nama' => 'Kabupaten Tegal'],
                ['id' => 216, 'nama' => 'Kabupaten Temanggung'],
                ['id' => 217, 'nama' => 'Kabupaten Wonogiri'],
                ['id' => 218, 'nama' => 'Kabupaten Wonosobo'],
            ],
            'DI Yogyakarta' => [
                ['id' => 219, 'nama' => 'Kota Yogyakarta'],
                ['id' => 220, 'nama' => 'Kabupaten Bantul'],
                ['id' => 221, 'nama' => 'Kabupaten Gunungkidul'],
                ['id' => 222, 'nama' => 'Kabupaten Kulon Progo'],
                ['id' => 223, 'nama' => 'Kabupaten Sleman'],
            ],
            'Bali' => [
                ['id' => 224, 'nama' => 'Kota Denpasar'],
                ['id' => 225, 'nama' => 'Kabupaten Badung'],
                ['id' => 226, 'nama' => 'Kabupaten Bangli'],
                ['id' => 227, 'nama' => 'Kabupaten Gianyar'],
                ['id' => 228, 'nama' => 'Kabupaten Jembrana'],
                ['id' => 229, 'nama' => 'Kabupaten Karangasem'],
                ['id' => 230, 'nama' => 'Kabupaten Klungkung'],
                ['id' => 231, 'nama' => 'Kabupaten Tabanan'],
            ],
            'Jawa Timur' => [
                ['id' => 232, 'nama' => 'Kota Batu'],
                ['id' => 233, 'nama' => 'Kota Blitar'],
                ['id' => 234, 'nama' => 'Kota Kediri'],
                ['id' => 235, 'nama' => 'Kota Madiun'],
                ['id' => 236, 'nama' => 'Kota Malang'],
                ['id' => 237, 'nama' => 'Kota Mojokerto'],
                ['id' => 238, 'nama' => 'Kota Pasuruan'],
                ['id' => 239, 'nama' => 'Kota Probolinggo'],
                ['id' => 240, 'nama' => 'Kota Surabaya'],
                ['id' => 241, 'nama' => 'Kabupaten Bangkalan'],
                ['id' => 242, 'nama' => 'Kabupaten Banyuwangi'],
                ['id' => 243, 'nama' => 'Kabupaten Blitar'],
                ['id' => 244, 'nama' => 'Kabupaten Bojonegoro'],
                ['id' => 245, 'nama' => 'Kabupaten Bondowoso'],
                ['id' => 246, 'nama' => 'Kabupaten Gresik'],
                ['id' => 247, 'nama' => 'Kabupaten Jember'],
                ['id' => 248, 'nama' => 'Kabupaten Jombang'],
                ['id' => 249, 'nama' => 'Kabupaten Kediri'],
                ['id' => 250, 'nama' => 'Kabupaten Lamongan'],
                ['id' => 251, 'nama' => 'Kabupaten Lumajang'],
                ['id' => 252, 'nama' => 'Kabupaten Madiun'],
                ['id' => 253, 'nama' => 'Kabupaten Magetan'],
                ['id' => 254, 'nama' => 'Kabupaten Malang'],
                ['id' => 255, 'nama' => 'Kabupaten Mojokerto'],
                ['id' => 256, 'nama' => 'Kabupaten Nganjuk'],
                ['id' => 257, 'nama' => 'Kabupaten Ngawi'],
                ['id' => 258, 'nama' => "Kabupaten Pacitan"],
                ['id' => 259, 'nama' => "Kabupaten Pasuruan"],
                ['id' => 260, 'nama' => "Kabupaten Ponorogo"],
                ['id' => 261, 'nama' => "Kabupaten Probolinggo"],
                ['id' => 262, 'nama' => "Kabupaten Sampang"],
                ['id' => 263, 'nama' => "Kabupaten Sidoarjo"],
                ['id' => 264, 'nama' => "Kabupaten Situbondo"],
                ['id' => 265, 'nama' => "Kabupaten Sumenep"],
                ['id' => 266, 'nama' => "Kabupaten Trenggalek"],
                ['id' => 267, 'nama' => "Kabupaten Tuban"],
                ['id' => 268, 'nama' => "Kabupaten Tulungagung"],   
            ],
            'DKI Jakarta' => [
                ['id' => 269, 'nama' => 'Kota Jakarta Pusat'],
                ['id' => 270, 'nama' => 'Kota Jakarta Utara'],
                ['id' => 271, 'nama' => 'Kota Jakarta Barat'],
                ['id' => 272, 'nama' => 'Kota Jakarta Selatan'],
                ['id' => 273, 'nama' => 'Kota Jakarta Timur'],
            ],
            'Nusa Tenggara Barat' => [
                ['id' => 274, 'nama' => 'Kota Mataram'],
                ['id' => 275, 'nama' => 'Kota Bima'],
                ['id' => 276, 'nama' => 'Kabupaten Bima'],
                ['id' => 277, 'nama' => 'Kabupaten Dompu'],
                ['id' => 278, 'nama' => 'Kabupaten Lombok Barat'],
                ['id' => 279, 'nama' => 'Kabupaten Lombok Tengah'],
                ['id' => 280, 'nama' => 'Kabupaten Lombok Timur'],
                ['id' => 281, 'nama' => 'Kabupaten Lombok Utara'],
                ['id' => 282, 'nama' => 'Kabupaten Sumbawa'],
                ['id' => 283, 'nama' => 'Kabupaten Sumbawa Barat'],
            ],
            'Nusa Tenggara Timur' => [
                ['id' => 284, 'nama' => 'Kota Kupang'],
                ['id' => 285, 'nama' => 'Kabupaten Alor'],
                ['id' => 286, 'nama' => 'Kabupaten Belu'],
                ['id' => 287, 'nama' => 'Kabupaten Ende'],
                ['id' => 288, 'nama' => 'Kabupaten Flores Timur'],
                ['id' => 289, 'nama' => 'Kabupaten Kupang'],
                ['id' => 290, 'nama' => 'Kabupaten Lembata'],
                ['id' => 291, 'nama' => 'Kabupaten Malaka'],
                ['id' => 292, 'nama' => 'Kabupaten Manggarai'],
                ['id' => 293, 'nama' => 'Kabupaten Manggarai Barat'],
                ['id' => 294, 'nama' => 'Kabupaten Manggarai Timur'],
                ['id' => 295, 'nama' => 'Kabupaten Ngada'],
                ['id' => 296, 'nama' => 'Kabupaten Rote Ndao'],
                ['id' => 297, 'nama' => 'Kabupaten Sabu Raija'],
                ['id' => 298, 'nama' => 'Kabupaten Sikka'],
                ['id' => 299, 'nama' => 'Kabupaten Timor Tengah Selatan'],
                ['id' => 300, 'nama' => 'Kabupaten Timor Tengah Utara'],
            ],
            'Kalimantan Barat' => [
                ['id' => 294, 'nama' => 'Kota Pontianak'],
                ['id' => 295, 'nama' => 'Kota Singkawang'],
                ['id' => 296, 'nama' => 'Kabupaten Bengkayang'],
                ['id' => 297, 'nama' => 'Kabupaten Kapuas Hulu'],
                ['id' => 298, 'nama' => 'Kabupaten Kayong Utara'],
                ['id' => 299, 'nama' => 'Kabupaten Ketapang'],
                ['id' => 300, 'nama' => 'Kabupaten Kubu Raya'],
                ['id' => 301, 'nama' => 'Kabupaten Landak'],
                ['id' => 302, 'nama' => 'Kabupaten Melawi'],
                ['id' => 303, 'nama' => 'Kabupaten Mempawah'],
                ['id' => 304, 'nama' => 'Kabupaten Sambas'],
                ['id' => 305, 'nama' => 'Kabupaten Sanggau'],
                ['id' => 306, 'nama' => 'Kabupaten Sekadau'],
                ['id' => 307, 'nama' => 'Kabupaten Sintang'],
                ['id' => 308, 'nama' => 'Kabupaten Ketapang'],
            ],
            'Kalimantan Selatan' => [
                ['id' => 309, 'nama' => 'Kota Banjarbaru'],
                ['id' => 310, 'nama' => 'Kota Banjarmasin'],
                ['id' => 311, 'nama' => 'Kabupaten Balangan'],
                ['id' => 312, 'nama' => 'Kabupaten Banjar'],
                ['id' => 313, 'nama' => 'Kabupaten Barito Kuala'],
                ['id' => 314, 'nama' => 'Kabupaten Hulu Sungai Selatan'],
                ['id' => 315, 'nama' => 'Kabupaten Hulu Sungai Tengah'],
                ['id' => 316, 'nama' => 'Kabupaten Hulu Sungai Utara'],
                ['id' => 317, 'nama' => 'Kabupaten Kotabaru'],
                ['id' => 318, 'nama' => 'Kabupaten Tabalong'],
                ['id' => 319, 'nama' => 'Kabupaten Tanah Bumbu'],
                ['id' => 320, 'nama' => 'Kabupaten Tanah Laut'],
                ['id' => 321, 'nama' => 'Kabupaten Tapin'],
            ],
            'Kalimantan Tengah' => [
                ['id' => 322, 'nama' => 'Kota Palangka Raya'],
                ['id' => 323, 'nama' => 'Kabupaten Barito Selatan'],
                ['id' => 324, 'nama' => 'Kabupaten Barito Timur'],
                ['id' => 325, 'nama' => 'Kabupaten Barito Utara'],
                ['id' => 326, 'nama' => 'Kabupaten Gunung Mas'],
                ['id' => 327, 'nama' => 'Kabupaten Kapuas'],
                ['id' => 328, 'nama' => 'Kabupaten Katingan'],
                ['id' => 329, 'nama' => 'Kabupaten Kotawaringin Barat'],
                ['id' => 330, 'nama' => 'Kabupaten Kotawaringin Timur'],
                ['id' => 331, 'nama' => 'Kabupaten Lamandau'],
                ['id' => 332, 'nama' => 'Kabupaten Murung Raya'],
                ['id' => 333, 'nama' => 'Kabupaten Pulang Pisau'],
                ['id' => 334, 'nama' => 'Kabupaten Sukamara'],
                ['id' => 335, 'nama' => 'Kabupaten Seruyan'],
            ],
            'Kalimantan Timur' => [
                ['id' => 336, 'nama' => 'Kota Balikpapan'],
                ['id' => 337, 'nama' => 'Kota Bontang'],
                ['id' => 338, 'nama' => 'Kota Samarinda'],
                ['id' => 339, 'nama' => 'Kabupaten Berau'],
                ['id' => 340, 'nama' => 'Kabupaten Kutai Barat'],
                ['id' => 341, 'nama' => 'Kabupaten Kutai Kartanegara'],
                ['id' => 342, 'nama' => 'Kabupaten Kutai Timur'],
                ['id' => 343, 'nama' => 'Kabupaten Mahakam Ulu'],
                ['id' => 344, 'nama' => 'Kabupaten Paser'],
                ['id' => 345, 'nama' => 'Kabupaten Penajam Paser Utara'],
            ],
            'Kalimantan Utara' => [
                ['id' => 346, 'nama' => 'Kota Tarakan'],
                ['id' => 347, 'nama' => 'Kabupaten Bulungan'],
                ['id' => 348, 'nama' => 'Kabupaten Malinau'],
                ['id' => 349, 'nama' => 'Kabupaten Nunukan'],
                ['id' => 350, 'nama' => 'Kabupaten Tana Tidung'],
                ['id' => 351, 'nama' => 'Kabupaten Tarakan'],  
            ],
            'Gorontalo' => [
                ['id' => 352, 'nama' => 'Kota Gorontalo'],
                ['id' => 353, 'nama' => 'Kabupaten Boalemo'],
                ['id' => 354, 'nama' => 'Kabupaten Bone Bolango'],
                ['id' => 355, 'nama' => 'Kabupaten Gorontalo'],
                ['id' => 356, 'nama' => 'Kabupaten Gorontalo Utara'],
                ['id' => 357, 'nama' => 'Kabupaten Pohuwato'],
            ],
            'Sulawesi Selatan' => [
                ['id' => 358, 'nama' => 'Kota Makassar'],
                ['id' => 359, 'nama' => 'Kota Parepare'],
                ['id' => 360, 'nama' => 'Kota Palopo'],
                ['id' => 361, 'nama' => 'Kabupaten Bantaeng'],
                ['id' => 362, 'nama' => 'Kabupaten Barru'],
                ['id' => 363, 'nama' => 'Kabupaten Bone'],
                ['id' => 364, 'nama' => 'Kabupaten Bulukumba'],
                ['id' => 365, 'nama' => 'Kabupaten Enrekang'],
                ['id' => 366, 'nama' => 'Kabupaten Gowa'],
                ['id' => 367, 'nama' => 'Kabupaten Jeneponto'],
                ['id' => 368, 'nama' => 'Kabupaten Kepulauan Selayar'],
                ['id' => 369, 'nama' => 'Kabupaten Luwu'],
                ['id' => 370, 'nama' => 'Kabupaten Luwu Timur'],
                ['id' => 371, 'nama' => 'Kabupaten Luwu Utara'],
                ['id' => 372, 'nama' => 'Kabupaten Maros'],
                ['id' => 373, 'nama' => 'Kabupaten Pangkajene dan Kepulauan'],
                ['id' => 374, 'nama' => 'Kabupaten Pinrang'],
                ['id' => 375, 'nama' => 'Kabupaten Sidenreng Rappang'],
                ['id' => 376, 'nama' => 'Kabupaten Sinjai'],
                ['id' => 377, 'nama' => 'Kabupaten Soppeng'],
                ['id' => 378, 'nama' => 'Kabupaten Takalar'],
                ['id' => 379, 'nama' => 'Kabupaten Tana Toraja'],
                ['id' => 380, 'nama' => 'Kabupaten Toraja Utara'],
                ['id' => 381, 'nama' => 'Kabupaten Wajo'],
            ],
            'Sulawesi Tenggara' => [
                ['id' => 382, 'nama' => 'Kota Bau-Bau'],
                ['id' => 383, 'nama' => 'Kota Kendari'],
                ['id' => 384, 'nama' => 'Kabupaten Bombana'],
                ['id' => 385, 'nama' => 'Kabupaten Buton'],
                ['id' => 386, 'nama' => 'Kabupaten Buton Selatan'],
                ['id' => 387, 'nama' => 'Kabupaten Buton Tengah'],
                ['id' => 388, 'nama' => 'Kabupaten Buton Utara'],
                ['id' => 389, 'nama' => 'Kabupaten Kolaka'],
                ['id' => 390, 'nama' => 'Kabupaten Kolaka Timur'],
                ['id' => 391, 'nama' => 'Kabupaten Kolaka Utara'],
                ['id' => 392, 'nama' => 'Kabupaten Konawe'],
                ['id' => 393, 'nama' => 'Kabupaten Konawe Kepulauan'],
                ['id' => 394, 'nama' => 'Kabupaten Konawe Selatan'],
                ['id' => 395, 'nama' => 'Kabupaten Konawe Utara'],
                ['id' => 396, 'nama' => 'Kabupaten Muna'],
                ['id' => 397, 'nama' => 'Kabupaten Muna Barat'],
                ['id' => 398, 'nama' => 'Kabupaten Wakatobi'],
            ],
            'Sulawesi Tengah' => [
                ['id' => 399, 'nama' => 'Kota Palu'],
                ['id' => 400, 'nama' => 'Kabupaten Banggai'],
                ['id' => 401, 'nama' => 'Kabupaten Banggai Kepulauan'],
                ['id' => 402, 'nama' => 'Kabupaten Buol'],
                ['id' => 403, 'nama' => 'Kabupaten Donggala'],
                ['id' => 404, 'nama' => 'Kabupaten Morowali'],
                ['id' => 405, 'nama' => 'Kabupaten Morowali Utara'],
                ['id' => 406, 'nama' => 'Kabupaten Parigi Moutong'],
                ['id' => 407, 'nama' => 'Kabupaten Poso'],
                ['id' => 408, 'nama' => 'Kabupaten Sigi'],
                ['id' => 409, 'nama' => 'Kabupaten Tojo Una-Una'],
                ['id' => 410, 'nama' => 'Kabupaten Tolitoli'],
            ],
            'Sulawesi Utara' => [
                ['id' => 411, 'nama' => 'Kota Manado'],
                ['id' => 412, 'nama' => 'Kota Bitung'],
                ['id' => 413, 'nama' => 'Kota Tomohon'],
                ['id' => 414, 'nama' => 'Kota Kotamobagu'],
                ['id' => 415, 'nama' => 'Kabupaten Bolaang Mongondow'],
                ['id' => 416, 'nama' => 'Kabupaten Bolaang Mongondow Selatan'],
                ['id' => 417, 'nama' => 'Kabupaten Bolaang Mongondow Timur'],
                ['id' => 418, 'nama' => 'Kabupaten Bolaang Mongondow Utara'],
                ['id' => 419, 'nama' => 'Kabupaten Kepulauan Siau Tagulandang Biaro'],
                ['id' => 420, 'nama' => 'Kabupaten Minahasa'],
                ['id' => 421, 'nama' => 'Kabupaten Minahasa Selatan'],
                ['id' => 422, 'nama' => 'Kabupaten Minahasa Tenggara'],
                ['id' => 423, 'nama' => 'Kabupaten Minahasa Utara'],
            ],
            'Sulawesi Barat' => [
                ['id' => 424, 'nama' => 'Kota Mamuju'],
                ['id' => 425, 'nama' => 'Kabupaten Majene'],
                ['id' => 426, 'nama' => 'Kabupaten Mamasa'],
                ['id' => 427, 'nama' => 'Kabupaten Mamuju'],
                ['id' => 428, 'nama' => 'Kabupaten Pasangkayu'],
            ],
            'Maluku' => [
                ['id' => 429, 'nama' => 'Kota Ambon'],
                ['id' => 430, 'nama' => 'Kota Tual'],
                ['id' => 431, 'nama' => 'Kabupaten Buru'],
                ['id' => 432, 'nama' => 'Kabupaten Buru Selatan'],
                ['id' => 433, 'nama' => 'Kabupaten Kepulauan Aru'],
                ['id' => 434, 'nama' => 'Kabupaten Maluku Barat Daya'],
                ['id' => 435, 'nama' => 'Kabupaten Maluku Tengah'],
                ['id' => 436, 'nama' => 'Kabupaten Maluku Tenggara'],
                ['id' => 437, 'nama' => 'Kabupaten Seram Bagian Barat'],
                ['id' => 438, 'nama' => 'Kabupaten Seram Bagian Timur'],
            ],
            'Maluku Utara' => [
                ['id' => 439, 'nama' => 'Kota Ternate'],
                ['id' => 440, 'nama' => 'Kota Tidore Kepulauan'],
                ['id' => 441, 'nama' => 'Kabupaten Halmahera Barat'],
                ['id' => 442, 'nama' => 'Kabupaten Halmahera Selatan'],
                ['id' => 443, 'nama' => 'Kabupaten Halmahera Tengah'],
                ['id' => 444, 'nama' => 'Kabupaten Halmahera Utara'],
                ['id' => 445, 'nama' => 'Kabupaten Halmahera Timur'],
                ['id' => 446, 'nama' => 'Kabupaten Kepulauan Sula'],
                ['id' => 447, 'nama' => 'Kabupaten Pulau Morotai'],
            ],
            'Papua' => [
                ['id' => 448, 'nama' => 'Kota Jayapura'],
                ['id' => 449, 'nama' => 'Kabupaten Asmat'],
                ['id' => 450, 'nama' => 'Kabupaten Biak Numfor'],
                ['id' => 451, 'nama' => 'Kabupaten Boven Digoel'],
                ['id' => 452, 'nama' => 'Kabupaten Deiyai'],
                ['id' => 453, 'nama' => 'Kabupaten Dogiyai'],
                ['id' => 454, 'nama' => 'Kabupaten Intan Jaya'],
                ['id' => 455, 'nama' => 'Kabupaten Jayapura'],
                ['id' => 456, 'nama' => 'Kabupaten Keerom'],
                ['id' => 457, 'nama' => 'Kabupaten Kepulauan Yapen'],
                ['id' => 458, 'nama' => 'Kabupaten Lanny Jaya'],
                ['id' => 459, 'nama' => 'Kabupaten Mamberamo Raya'],
                ['id' => 460, 'nama' => 'Kabupaten Mamberamo Tengah'],
                ['id' => 461, 'nama' => 'Kabupaten Mimika'],
                ['id' => 462, 'nama' => 'Kabupaten Nabire'],
                ['id' => 463, 'nama' => 'Kabupaten Paniai'],
                ['id' => 464, 'nama' => 'Kabupaten Pegunungan Bintang'],
                ['id' => 465, 'nama' => 'Kabupaten Puncak'],
                ['id' => 466, 'nama' => 'Kabupaten Puncak Jaya'],
                ['id' => 467, 'nama' => 'Kabupaten Sarmi'],
                ['id' => 468, 'nama' => 'Kabupaten Supiori'],
                ['id' => 469, 'nama' => 'Kabupaten Tolikara'],
                ['id' => 470, 'nama' => 'Kabupaten Waropen'],
                ['id' => 471, 'nama' => 'Kabupaten Yahukimo'],
                ['id' => 472, 'nama' => 'Kabupaten Yalimo'],
            ],
        ];

        // jika ingin mengambil dari database:
        // $kabupaten = Kabupaten::where('provinsi_nama', $provinsi)->get(['id', 'nama']);
        // return response()->json($kabupaten);

        $kabupaten = $data[$provinsi] ?? [];
        
        return response()->json($kabupaten);
    }
}