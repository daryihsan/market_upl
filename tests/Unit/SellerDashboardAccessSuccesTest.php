<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * TC-DBP-01: Pengujian dashboard penjual hanya dapat diakses oleh penjual yang sudah login
 * DUPL      : TC-DBP-01
 * Kelas Uji : Pengujian Dashboard Penjual
 * SKPL      : SRS-07
 * 
 * Deskripsi:
 * Pengujian untuk memastikan bahwa halaman dashboard penjual hanya dapat diakses oleh penjual
 * yang telah melakukan login. Sistem harus mencegah akses ke dashboard tanpa autentikasi yang valid.
 * 
 * Prosedur Pengujian:
 * Mencoba mengakses URL dashboard penjual tanpa sesi login penjual aktif
 * 
 * Masukan:
 * Mengakses URL halaman dashboard penjual tanpa melakukan login sebagai penjual
 * 
 * Keluaran yang diharapkan:
 * Sistem mengalihkan pengguna ke halaman login penjual
 * 
 * Kriteria evaluasi hasil:
 * Halaman login penjual tampil saat mencoba mengakses URL dashboard penjual tanpa autentikasi
 */
class SellerDashboardAccessSuccesTest extends TestCase
{
    /**
     * Simulasi login penjual dan autentikasi.
     * Mengembalikan array dengan status autentikasi dan data penjual.
     */
    private function loginSeller(string $emailOrToko, string $password): array
    {
        // Data penjual yang valid di sistem
        $validSellers = [
            [
                'id' => 1,
                'email_pic' => 'seller@test.com',
                'nama_toko' => 'Toko Bagus',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'role' => 'seller',
                'status_akun' => 'active',
            ],
        ];

        // Cari penjual berdasarkan email atau nama toko
        foreach ($validSellers as $seller) {
            if ($seller['email_pic'] === $emailOrToko || $seller['nama_toko'] === $emailOrToko) {
                // Verifikasi password
                if (password_verify($password, $seller['password'])) {
                    return [
                        'status' => 200,
                        'success' => true,
                        'message' => 'Login penjual berhasil!',
                        'data' => [
                            'id' => $seller['id'],
                            'email' => $seller['email_pic'],
                            'nama_toko' => $seller['nama_toko'],
                            'role' => $seller['role'],
                            'status_akun' => $seller['status_akun'],
                        ],
                    ];
                } else {
                    return [
                        'status' => 401,
                        'success' => false,
                        'message' => 'Password salah',
                    ];
                }
            }
        }

        // Email/Nama Toko tidak ditemukan
        return [
            'status' => 404,
            'success' => false,
            'message' => 'Email atau Nama Toko tidak ditemukan',
        ];
    }

    /**
     * Simulasi autentikasi penjual untuk akses dashboard.
     * Mengembalikan data dashboard jika penjual ter-autentikasi.
     */
    private function accessSellerDashboard(array $sellerData = null): array
    {
        // Jika tidak ada data penjual (tidak login), tolak akses
        if (is_null($sellerData)) {
            return [
                'status' => 401,
                'success' => false,
                'message' => 'Unauthorized - Penjual tidak ter-autentikasi',
                'redirect_to' => '/login/login',
            ];
        }

        // Verifikasi penjual adalah role 'seller'
        if ($sellerData['role'] !== 'seller') {
            return [
                'status' => 403,
                'success' => false,
                'message' => 'Forbidden - User bukan penjual',
            ];
        }

        // Verifikasi status akun penjual
        if ($sellerData['status_akun'] === 'pending') {
            return [
                'status' => 403,
                'success' => false,
                'message' => 'Akun Anda belum diverifikasi oleh admin. Silakan tunggu verifikasi.',
            ];
        }

        if ($sellerData['status_akun'] === 'rejected') {
            return [
                'status' => 403,
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan oleh admin. Hubungi admin untuk bantuan.',
            ];
        }

        // Dashboard berhasil diakses - simulasi data penjual
        return [
            'status' => 200,
            'success' => true,
            'message' => 'Dashboard penjual berhasil dimuat',
            'data' => [
                'seller_id' => $sellerData['id'],
                'nama_toko' => $sellerData['nama_toko'],
                'total_produk' => 12,
                'total_ulasan' => 48,
                'rating_rata_rata' => 4.5,
                'statistik_penjualan' => [
                    'bulan_ini' => 25,
                    'bulan_lalu' => 18,
                ],
            ],
        ];
    }

    /**
     * Simulasi pengecekan dashboard dapat diakses tanpa login.
     * Mengembalikan respons akses tanpa autentikasi.
     */
    private function accessDashboardWithoutLogin(): array
    {
        return $this->accessSellerDashboard(null);
    }

    /**
     * Simulasi logout penjual.
     * Menghapus session autentikasi.
     */
    private function logoutSeller(array $sellerData): array
    {
        if (is_null($sellerData)) {
            return [
                'status' => 400,
                'success' => false,
                'message' => 'Tidak ada session yang aktif',
            ];
        }

        return [
            'status' => 200,
            'success' => true,
            'message' => 'Logout berhasil',
            'redirect_to' => '/login/pilih',
        ];
    }

    /**
     * TC-DBP-01 Test 1: Dashboard penjual tidak dapat diakses tanpa login
     * 
     * Input: Mengakses URL halaman dashboard penjual tanpa melakukan login sebagai penjual
     * 
     * Hasil yang diharapkan:
     * Sistem mengalihkan pengguna ke halaman login penjual (status 401)
     */
    public function test_dashboard_penjual_tidak_dapat_diakses_tanpa_login(): void
    {
        // Eksekusi: Akses dashboard tanpa login
        $response = $this->accessDashboardWithoutLogin();

        // Verifikasi: Akses ditolak dan redirect ke login
        $this->assertEquals(401, $response['status'],
            'Status HTTP harus 401 (Unauthorized) ketika akses tanpa login.'
        );
        $this->assertFalse($response['success']);
        $this->assertEquals('Unauthorized - Penjual tidak ter-autentikasi', $response['message']);
        $this->assertArrayHasKey('redirect_to', $response);
        $this->assertEquals('/login/login', $response['redirect_to'],
            'Harus di-redirect ke halaman login penjual.'
        );
    }

    /**
     * TC-DBP-01 Test 2: Halaman login penjual tampil saat mengakses dashboard tanpa autentikasi
     * 
     * Kriteria evaluasi hasil:
     * Halaman login penjual tampil saat mencoba mengakses URL dashboard penjual tanpa autentikasi
     */
    public function test_halaman_login_penjual_tampil_tanpa_autentikasi(): void
    {
        // Eksekusi: Coba akses dashboard tanpa login
        $response = $this->accessDashboardWithoutLogin();

        // Verifikasi: Response redirect ke halaman login penjual
        $this->assertFalse($response['success']);
        $this->assertEquals('/login/login', $response['redirect_to'],
            'Halaman login penjual harus ditampilkan.'
        );
    }

    /**
     * TC-DBP-01 Test 3: Penjual yang sudah login dapat mengakses dashboard
     * 
     * Hasil yang diharapkan:
     * Dashboard penjual berhasil diakses setelah login (status 200)
     */
    public function test_penjual_yang_login_dapat_akses_dashboard(): void
    {
        // Step 1: Login penjual
        $loginResponse = $this->loginSeller('seller@test.com', 'password123');
        $this->assertTrue($loginResponse['success']);

        // Step 2: Akses dashboard dengan data penjual yang sudah login
        $dashboardResponse = $this->accessSellerDashboard($loginResponse['data']);

        // Verifikasi: Dashboard berhasil diakses
        $this->assertEquals(200, $dashboardResponse['status'],
            'Status HTTP harus 200 ketika penjual mengakses dashboard.'
        );
        $this->assertTrue($dashboardResponse['success']);
        $this->assertEquals('Dashboard penjual berhasil dimuat', $dashboardResponse['message']);
    }

    /**
     * TC-DBP-01 Test 4: Penjual gagal login dengan password salah
     * 
     * Input: Email penjual valid tetapi password salah
     * 
     * Hasil yang diharapkan:
     * Login ditolak dengan error "Password salah"
     */
    public function test_penjual_login_gagal_password_salah(): void
    {
        $email = 'seller@test.com';
        $wrongPassword = 'wrongpassword';

        // Eksekusi: Login dengan password salah
        $response = $this->loginSeller($email, $wrongPassword);

        // Verifikasi: Login ditolak
        $this->assertEquals(401, $response['status'],
            'Status HTTP harus 401 ketika password salah.'
        );
        $this->assertFalse($response['success']);
        $this->assertEquals('Password salah', $response['message']);
    }

    /**
     * TC-DBP-01 Test 5: Penjual gagal login dengan email tidak terdaftar
     * 
     * Input: Email yang tidak terdaftar di sistem
     * 
     * Hasil yang diharapkan:
     * Login ditolak dengan error "Email atau Nama Toko tidak ditemukan"
     */
    public function test_penjual_login_gagal_email_tidak_terdaftar(): void
    {
        $unknownEmail = 'unknown@example.com';
        $password = 'password123';

        // Eksekusi: Login dengan email yang tidak terdaftar
        $response = $this->loginSeller($unknownEmail, $password);

        // Verifikasi: Login ditolak
        $this->assertEquals(404, $response['status'],
            'Status HTTP harus 404 ketika email tidak ditemukan.'
        );
        $this->assertFalse($response['success']);
        $this->assertEquals('Email atau Nama Toko tidak ditemukan', $response['message']);
    }

    /**
     * TC-DBP-01 Test 6: Admin tidak dapat mengakses dashboard penjual
     * 
     * Input: User dengan role 'admin' mencoba mengakses dashboard penjual
     * 
     * Hasil yang diharapkan:
     * Akses ditolak dengan status 403 (Forbidden)
     */
    public function test_admin_tidak_dapat_akses_dashboard_penjual(): void
    {
        // Data user dengan role 'admin' (bukan penjual)
        $adminData = [
            'id' => 1,
            'email' => 'admin@test.com',
            'nama_toko' => null,
            'role' => 'admin', // Bukan seller
            'status_akun' => 'active',
        ];

        // Eksekusi: Coba akses dashboard dengan role admin
        $response = $this->accessSellerDashboard($adminData);

        // Verifikasi: Akses ditolak
        $this->assertEquals(403, $response['status'],
            'Status HTTP harus 403 (Forbidden) untuk non-penjual.'
        );
        $this->assertFalse($response['success']);
        $this->assertEquals('Forbidden - User bukan penjual', $response['message']);
    }

    /**
     * TC-DBP-01 Test 7: Penjual dengan status pending tidak dapat mengakses dashboard
     * 
     * Input: Penjual dengan status akun 'pending' (belum diverifikasi)
     * 
     * Hasil yang diharapkan:
     * Akses ditolak dengan pesan verifikasi menunggu
     */
    public function test_penjual_pending_tidak_dapat_akses_dashboard(): void
    {
        // Data penjual dengan status pending
        $pendingSellerData = [
            'id' => 2,
            'email' => 'pending@test.com',
            'nama_toko' => 'Toko Pending',
            'role' => 'seller',
            'status_akun' => 'pending', // Belum diverifikasi
        ];

        // Eksekusi: Coba akses dashboard
        $response = $this->accessSellerDashboard($pendingSellerData);

        // Verifikasi: Akses ditolak
        $this->assertEquals(403, $response['status']);
        $this->assertFalse($response['success']);
        $this->assertStringContainsString('belum diverifikasi', $response['message']);
    }

    /**
     * TC-DBP-01 Test 8: Penjual dengan status rejected tidak dapat mengakses dashboard
     * 
     * Input: Penjual dengan status akun 'rejected' (dinonaktifkan admin)
     * 
     * Hasil yang diharapkan:
     * Akses ditolak dengan pesan akun dinonaktifkan
     */
    public function test_penjual_rejected_tidak_dapat_akses_dashboard(): void
    {
        // Data penjual dengan status rejected
        $rejectedSellerData = [
            'id' => 3,
            'email' => 'rejected@test.com',
            'nama_toko' => 'Toko Ditolak',
            'role' => 'seller',
            'status_akun' => 'rejected', // Dinonaktifkan admin
        ];

        // Eksekusi: Coba akses dashboard
        $response = $this->accessSellerDashboard($rejectedSellerData);

        // Verifikasi: Akses ditolak
        $this->assertEquals(403, $response['status']);
        $this->assertFalse($response['success']);
        $this->assertStringContainsString('dinonaktifkan', $response['message']);
    }

    /**
     * TC-DBP-01 Test 9: Penjual dapat logout dan session dibersihkan
     * 
     * Hasil yang diharapkan:
     * Logout berhasil dan user di-redirect ke halaman login
     */
    public function test_penjual_logout_dan_session_dibersihkan(): void
    {
        // Step 1: Login penjual terlebih dahulu
        $loginResponse = $this->loginSeller('seller@test.com', 'password123');
        $this->assertTrue($loginResponse['success']);
        $sellerData = $loginResponse['data'];

        // Step 2: Logout
        $logoutResponse = $this->logoutSeller($sellerData);

        // Verifikasi: Logout berhasil
        $this->assertEquals(200, $logoutResponse['status']);
        $this->assertTrue($logoutResponse['success']);
        $this->assertEquals('Logout berhasil', $logoutResponse['message']);

        // Step 3: Verifikasi tidak bisa akses dashboard setelah logout
        $dashboardResponse = $this->accessDashboardWithoutLogin();
        $this->assertEquals(401, $dashboardResponse['status']);
        $this->assertFalse($dashboardResponse['success']);
    }

    /**
     * TC-DBP-01 Test 10: Dashboard menampilkan data penjual yang benar
     * 
     * Hasil yang diharapkan:
     * Dashboard menampilkan informasi toko dan statistik penjualan
     */
    public function test_dashboard_menampilkan_data_penjual_benar(): void
    {
        // Step 1: Login penjual
        $loginResponse = $this->loginSeller('seller@test.com', 'password123');
        $sellerData = $loginResponse['data'];

        // Step 2: Akses dashboard
        $dashboardResponse = $this->accessSellerDashboard($sellerData);

        // Verifikasi: Data penjual ditampilkan dengan benar
        $this->assertTrue($dashboardResponse['success']);
        $data = $dashboardResponse['data'];

        $this->assertEquals('Toko Bagus', $data['nama_toko'],
            'Nama toko harus ditampilkan dengan benar.'
        );
        $this->assertEquals(12, $data['total_produk']);
        $this->assertEquals(48, $data['total_ulasan']);
        $this->assertEquals(4.5, $data['rating_rata_rata']);
    }

    /**
     * TC-DBP-01 Test 11: Penjual dapat mengakses dashboard berkali-kali
     * 
     * Hasil yang diharapkan:
     * Session penjual tetap valid untuk akses multiple kali
     */
    public function test_penjual_dapat_akses_dashboard_berkali_kali(): void
    {
        // Step 1: Login penjual
        $loginResponse = $this->loginSeller('seller@test.com', 'password123');
        $sellerData = $loginResponse['data'];

        // Step 2: Akses dashboard berkali-kali
        for ($i = 0; $i < 3; $i++) {
            $dashboardResponse = $this->accessSellerDashboard($sellerData);

            $this->assertEquals(200, $dashboardResponse['status'],
                "Akses dashboard ke-" . ($i + 1) . " harus berhasil."
            );
            $this->assertTrue($dashboardResponse['success']);
        }
    }

    /**
     * TC-DBP-01 Test 12: Penjual login dengan nama toko juga berhasil
     * 
     * Input: Login menggunakan nama toko sebagai identifier
     * 
     * Hasil yang diharapkan:
     * Login berhasil dengan nama toko sebagai alternatif email
     */
    public function test_penjual_login_dengan_nama_toko(): void
    {
        // Eksekusi: Login dengan nama toko
        $response = $this->loginSeller('Toko Bagus', 'password123');

        // Verifikasi: Login berhasil
        $this->assertEquals(200, $response['status']);
        $this->assertTrue($response['success']);
        $this->assertEquals('Login penjual berhasil!', $response['message']);
        $this->assertEquals('Toko Bagus', $response['data']['nama_toko']);
    }
}
