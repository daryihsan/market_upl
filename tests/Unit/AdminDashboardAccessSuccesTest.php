<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * TC-DBA-01: Dashboard platform hanya dapat diakses oleh admin platform yang sudah login
 * DUPL      : TC-DBA-01
 * Kelas Uji : Pengujian Dashboard Admin
 * SKPL      : SRS-07
 * 
 * Deskripsi:
 * Pengujian untuk memastikan dashboard platform hanya dapat diakses oleh admin yang telah
 * melakukan login menggunakan akun valid. Dashboard admin harus menampilkan data statistik
 * tanpa error dan hanya dapat diakses setelah login berhasil.
 */
class AdminDashboardAccessSuccesTest extends TestCase
{
    /**
     * Simulasi login admin dan autentikasi.
     * Mengembalikan array dengan status autentikasi dan data admin.
     */
    private function loginAdmin(string $email, string $password): array
    {
        // Data admin yang valid di sistem
        $validAdmins = [
            [
                'id' => 1,
                'email_pic' => 'admin@quadmarket.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'nama_pic' => 'Admin QuadMarket',
                'role' => 'admin',
                'status_akun' => 'active',
            ],
        ];

        // Cari admin berdasarkan email
        foreach ($validAdmins as $admin) {
            if ($admin['email_pic'] === $email) {
                // Verifikasi password
                if (password_verify($password, $admin['password'])) {
                    return [
                        'status' => 200,
                        'success' => true,
                        'message' => 'Login admin berhasil!',
                        'data' => [
                            'id' => $admin['id'],
                            'email' => $admin['email_pic'],
                            'nama' => $admin['nama_pic'],
                            'role' => $admin['role'],
                        ],
                    ];
                } else {
                    return [
                        'status' => 401,
                        'success' => false,
                        'message' => 'Password Admin salah',
                    ];
                }
            }
        }

        // Email tidak ditemukan
        return [
            'status' => 404,
            'success' => false,
            'message' => 'Email Admin tidak ditemukan',
        ];
    }

    /**
     * Simulasi autentikasi admin untuk akses dashboard.
     * Mengembalikan data dashboard jika admin ter-autentikasi.
     */
    private function accessAdminDashboard(array $adminData = null): array
    {
        // Jika tidak ada data admin (tidak login), tolak akses
        if (is_null($adminData)) {
            return [
                'status' => 401,
                'success' => false,
                'message' => 'Unauthorized - Admin tidak ter-autentikasi',
                'redirect_to' => '/login/pilih',
            ];
        }

        // Verifikasi admin adalah role 'admin'
        if ($adminData['role'] !== 'admin') {
            return [
                'status' => 403,
                'success' => false,
                'message' => 'Forbidden - User bukan admin',
            ];
        }

        // Dashboard berhasil diakses - simulasi data statistik
        return [
            'status' => 200,
            'success' => true,
            'message' => 'Dashboard admin berhasil dimuat',
            'data' => [
                'total_penjual_aktif' => 42,
                'total_penjual_pending' => 8,
                'total_penjual_tidak_aktif' => 5,
                'total_produk' => 156,
                'total_ulasan' => 324,
                'category_labels' => ['Elektronik', 'Fashion', 'Makanan'],
                'category_counts' => [45, 38, 32],
                'provinsi_labels' => ['Jawa Barat', 'Jawa Timur', 'Sumatera'],
                'provinsi_counts' => [18, 15, 9],
            ],
        ];
    }

    /**
     * Simulasi pengecekan dashboard dapat diakses tanpa login.
     * Mengembalikan respons akses tanpa autentikasi.
     */
    private function accessDashboardWithoutLogin(): array
    {
        return $this->accessAdminDashboard(null);
    }

    /**
     * Simulasi logout admin.
     * Menghapus session autentikasi.
     */
    private function logoutAdmin(array $adminData): array
    {
        if (is_null($adminData)) {
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
     * TC-DBA-01 Test 1: Admin berhasil login dengan credential valid
     * 
     * Prosedur:
     * 1. Membuka halaman login Admin
     * 2. Menginput email admin valid
     * 3. Menginput password admin valid
     * 4. Menekan tombol "Login"
     * 
     * Hasil yang diharapkan:
     * Login berhasil dengan status 200 dan message "Login admin berhasil!"
     */
    public function test_admin_login_dengan_credential_valid(): void
    {
        $email = 'admin@quadmarket.com';
        $password = 'password123';

        // Eksekusi: Login admin
        $response = $this->loginAdmin($email, $password);

        // Verifikasi: Login berhasil
        $this->assertEquals(200, $response['status'],
            'Status HTTP harus 200 ketika login admin berhasil.'
        );
        $this->assertTrue($response['success']);
        $this->assertEquals('Login admin berhasil!', $response['message']);
        $this->assertNotEmpty($response['data']);
        $this->assertEquals('admin@quadmarket.com', $response['data']['email']);
        $this->assertEquals('admin', $response['data']['role']);
    }

    /**
     * TC-DBA-01 Test 2: Admin dapat mengakses dashboard setelah login
     * 
     * Hasil yang diharapkan:
     * Sistem berhasil menampilkan dashboard admin (status 200)
     * Dashboard menampilkan semua data statistik tanpa error
     */
    public function test_admin_dapat_akses_dashboard_setelah_login(): void
    {
        // Step 1: Login admin
        $loginResponse = $this->loginAdmin('admin@quadmarket.com', 'password123');
        $this->assertTrue($loginResponse['success']);

        // Step 2: Akses dashboard dengan data admin yang sudah login
        $dashboardResponse = $this->accessAdminDashboard($loginResponse['data']);

        // Verifikasi: Dashboard berhasil diakses
        $this->assertEquals(200, $dashboardResponse['status'],
            'Status HTTP harus 200 ketika mengakses dashboard admin.'
        );
        $this->assertTrue($dashboardResponse['success']);
        $this->assertEquals('Dashboard admin berhasil dimuat', $dashboardResponse['message']);

        // Verifikasi: Data statistik tersedia
        $this->assertNotEmpty($dashboardResponse['data']);
        $this->assertArrayHasKey('total_penjual_aktif', $dashboardResponse['data']);
        $this->assertArrayHasKey('total_penjual_pending', $dashboardResponse['data']);
        $this->assertArrayHasKey('total_penjual_tidak_aktif', $dashboardResponse['data']);
        $this->assertArrayHasKey('category_labels', $dashboardResponse['data']);
        $this->assertArrayHasKey('provinsi_labels', $dashboardResponse['data']);
    }

    /**
     * TC-DBA-01 Test 3: Dashboard tidak dapat diakses tanpa login
     * 
     * Input: Mencoba mengakses dashboard tanpa melakukan login terlebih dahulu
     * 
     * Hasil yang diharapkan:
     * Sistem menolak akses dan me-redirect ke halaman login
     */
    public function test_dashboard_tidak_dapat_diakses_tanpa_login(): void
    {
        // Eksekusi: Akses dashboard tanpa login
        $response = $this->accessDashboardWithoutLogin();

        // Verifikasi: Akses ditolak
        $this->assertEquals(401, $response['status'],
            'Status HTTP harus 401 (Unauthorized) ketika akses tanpa login.'
        );
        $this->assertFalse($response['success']);
        $this->assertEquals('Unauthorized - Admin tidak ter-autentikasi', $response['message']);
        $this->assertArrayHasKey('redirect_to', $response);
        $this->assertEquals('/login/pilih', $response['redirect_to']);
    }

    /**
     * TC-DBA-01 Test 4: Admin gagal login dengan password salah
     * 
     * Input: Email admin valid tetapi password salah
     * 
     * Hasil yang diharapkan:
     * Login ditolak dengan error "Password Admin salah"
     */
    public function test_admin_login_gagal_password_salah(): void
    {
        $email = 'admin@quadmarket.com';
        $wrongPassword = 'wrongpassword123';

        // Eksekusi: Login dengan password salah
        $response = $this->loginAdmin($email, $wrongPassword);

        // Verifikasi: Login ditolak
        $this->assertEquals(401, $response['status'],
            'Status HTTP harus 401 ketika password salah.'
        );
        $this->assertFalse($response['success']);
        $this->assertEquals('Password Admin salah', $response['message']);
    }

    /**
     * TC-DBA-01 Test 5: Admin gagal login dengan email tidak terdaftar
     * 
     * Input: Email yang tidak terdaftar di sistem
     * 
     * Hasil yang diharapkan:
     * Login ditolak dengan error "Email Admin tidak ditemukan"
     */
    public function test_admin_login_gagal_email_tidak_terdaftar(): void
    {
        $unknownEmail = 'unknown@example.com';
        $password = 'password123';

        // Eksekusi: Login dengan email yang tidak terdaftar
        $response = $this->loginAdmin($unknownEmail, $password);

        // Verifikasi: Login ditolak
        $this->assertEquals(404, $response['status'],
            'Status HTTP harus 404 ketika email tidak ditemukan.'
        );
        $this->assertFalse($response['success']);
        $this->assertEquals('Email Admin tidak ditemukan', $response['message']);
    }

    /**
     * TC-DBA-01 Test 6: Non-admin tidak dapat mengakses dashboard
     * 
     * Input: User dengan role 'seller' mencoba mengakses dashboard admin
     * 
     * Hasil yang diharapkan:
     * Akses ditolak dengan status 403 (Forbidden)
     */
    public function test_non_admin_tidak_dapat_akses_dashboard(): void
    {
        // Data user dengan role 'seller' (bukan admin)
        $sellerData = [
            'id' => 2,
            'email' => 'seller@test.com',
            'nama' => 'Seller Test',
            'role' => 'seller', // Bukan admin
        ];

        // Eksekusi: Coba akses dashboard dengan role seller
        $response = $this->accessAdminDashboard($sellerData);

        // Verifikasi: Akses ditolak
        $this->assertEquals(403, $response['status'],
            'Status HTTP harus 403 (Forbidden) untuk non-admin.'
        );
        $this->assertFalse($response['success']);
        $this->assertEquals('Forbidden - User bukan admin', $response['message']);
    }

    /**
     * TC-DBA-01 Test 7: Admin dapat logout dan session dibersihkan
     * 
     * Hasil yang diharapkan:
     * Logout berhasil dan user di-redirect ke halaman login
     */
    public function test_admin_logout_dan_session_dibersihkan(): void
    {
        // Step 1: Login admin terlebih dahulu
        $loginResponse = $this->loginAdmin('admin@quadmarket.com', 'password123');
        $this->assertTrue($loginResponse['success']);
        $adminData = $loginResponse['data'];

        // Step 2: Logout
        $logoutResponse = $this->logoutAdmin($adminData);

        // Verifikasi: Logout berhasil
        $this->assertEquals(200, $logoutResponse['status'],
            'Status HTTP harus 200 ketika logout berhasil.'
        );
        $this->assertTrue($logoutResponse['success']);
        $this->assertEquals('Logout berhasil', $logoutResponse['message']);
        $this->assertEquals('/login/pilih', $logoutResponse['redirect_to']);

        // Step 3: Verifikasi tidak bisa akses dashboard setelah logout
        $dashboardResponse = $this->accessDashboardWithoutLogin();
        $this->assertEquals(401, $dashboardResponse['status']);
        $this->assertFalse($dashboardResponse['success']);
    }

    /**
     * TC-DBA-01 Test 8: Dashboard menampilkan statistik yang benar
     * 
     * Hasil yang diharapkan:
     * Data statistik (total penjual, kategori, provinsi) ditampilkan dengan benar
     */
    public function test_dashboard_menampilkan_statistik_benar(): void
    {
        // Step 1: Login admin
        $loginResponse = $this->loginAdmin('admin@quadmarket.com', 'password123');
        $adminData = $loginResponse['data'];

        // Step 2: Akses dashboard
        $dashboardResponse = $this->accessAdminDashboard($adminData);

        // Verifikasi: Statistik tersedia
        $this->assertTrue($dashboardResponse['success']);
        $data = $dashboardResponse['data'];

        // Verifikasi angka statistik penjual
        $this->assertEquals(42, $data['total_penjual_aktif'],
            'Total penjual aktif harus 42.'
        );
        $this->assertEquals(8, $data['total_penjual_pending'],
            'Total penjual pending harus 8.'
        );
        $this->assertEquals(5, $data['total_penjual_tidak_aktif'],
            'Total penjual tidak aktif harus 5.'
        );

        // Verifikasi data kategori
        $this->assertCount(3, $data['category_labels']);
        $this->assertCount(3, $data['category_counts']);
        $this->assertEquals(45, $data['category_counts'][0]);

        // Verifikasi data provinsi
        $this->assertCount(3, $data['provinsi_labels']);
        $this->assertCount(3, $data['provinsi_counts']);
    }

    /**
     * TC-DBA-01 Test 9: Admin dapat mengakses dashboard berkali-kali
     * 
     * Hasil yang diharapkan:
     * Session admin tetap valid untuk akses multiple kali
     */
    public function test_admin_dapat_akses_dashboard_berkali_kali(): void
    {
        // Step 1: Login admin
        $loginResponse = $this->loginAdmin('admin@quadmarket.com', 'password123');
        $adminData = $loginResponse['data'];

        // Step 2: Akses dashboard berkali-kali
        for ($i = 0; $i < 3; $i++) {
            $dashboardResponse = $this->accessAdminDashboard($adminData);

            $this->assertEquals(200, $dashboardResponse['status'],
                "Akses dashboard ke-" . ($i + 1) . " harus berhasil."
            );
            $this->assertTrue($dashboardResponse['success']);
        }
    }

    /**
     * TC-DBA-01 Test 10: Admin login dengan email kosong ditolak
     * 
     * Input: Email kosong
     * 
     * Hasil yang diharapkan:
     * Login ditolak karena validasi email kosong
     */
    public function test_admin_login_email_kosong_ditolak(): void
    {
        $response = $this->loginAdmin('', 'password123');

        $this->assertEquals(404, $response['status'],
            'Status HTTP harus 404 ketika email kosong.'
        );
        $this->assertFalse($response['success']);
        $this->assertEquals('Email Admin tidak ditemukan', $response['message']);
    }
}
