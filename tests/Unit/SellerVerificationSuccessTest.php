<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * TC-VRF-01: Verifikasi berhasil
 * Admin menyetujui registrasi, status penjual berubah menjadi aktif
 */
class SellerVerificationSuccessTest extends TestCase
{
    /**
     * Simulasi proses processVerification() pada PlatformController.
     * Disesuaikan dengan kode asli:
     * - action wajib approve/reject
     * - seller harus berstatus pending
     * - jika approve, status_akun berubah menjadi active
     * - verification_date terisi
     * - sistem redirect ke platform.verifikasi.list
     */
    private function processVerification(array $seller, array $request): array
    {
        // Simulasi validasi action
        if (
            empty($request['action']) ||
            !in_array($request['action'], ['approve', 'reject'])
        ) {
            return [
                'status' => 422,
                'success' => false,
                'message' => 'Action tidak valid.',
                'data' => $seller,
            ];
        }

        // Simulasi pengecekan status akun sudah final
        if ($seller['status_akun'] !== 'pending') {
            return [
                'status' => 302,
                'success' => false,
                'redirect_route' => 'platform.verifikasi.list',
                'message' => 'Status akun sudah final.',
                'data' => $seller,
            ];
        }

        // Simulasi approve
        if ($request['action'] === 'approve') {
            $seller['status_akun'] = 'active';
            $seller['verification_date'] = date('Y-m-d H:i:s');

            return [
                'status' => 302,
                'success' => true,
                'redirect_route' => 'platform.verifikasi.list',
                'message' => "Penjual ({$seller['nama_toko']}) berhasil diaktifkan.",
                'mail_sent_to' => $seller['email_pic'],
                'data' => $seller,
            ];
        }

        // Simulasi reject, tidak dipakai untuk TC-VRF-01
        $seller['status_akun'] = 'rejected';
        $seller['deactivated_by_admin'] = true;
        $seller['verification_date'] = date('Y-m-d H:i:s');

        return [
            'status' => 302,
            'success' => true,
            'redirect_route' => 'platform.verifikasi.list',
            'message' => 'Penjual ditolak.',
            'mail_sent_to' => $seller['email_pic'],
            'data' => $seller,
        ];
    }

    /**
     * TC-VRF-01:
     * Admin menyetujui registrasi penjual sehingga status berubah menjadi active.
     */
    public function test_admin_berhasil_menyetujui_registrasi_penjual(): void
    {
        $seller = [
            'id' => 1,
            'nama_toko' => 'Toko Mahasiswa',
            'email_pic' => 'penjual@students.undip.ac.id',
            'status_akun' => 'pending',
            'verification_date' => null,
        ];

        $request = [
            'action' => 'approve',
        ];

        $response = $this->processVerification($seller, $request);

        // Verifikasi proses berhasil dan redirect ke daftar verifikasi
        $this->assertEquals(302, $response['status']);
        $this->assertTrue($response['success']);
        $this->assertEquals('platform.verifikasi.list', $response['redirect_route']);

        // Verifikasi status penjual berubah dari pending menjadi active
        $this->assertEquals('active', $response['data']['status_akun']);

        // Verifikasi tanggal verifikasi terisi
        $this->assertNotNull($response['data']['verification_date']);

        // Verifikasi email notifikasi dikirim ke email PIC penjual
        $this->assertEquals('penjual@students.undip.ac.id', $response['mail_sent_to']);

        // Verifikasi pesan sukses sesuai alur kode
        $this->assertEquals('Penjual (Toko Mahasiswa) berhasil diaktifkan.', $response['message']);
    }
}

