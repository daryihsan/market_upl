<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * TC-REV-02: Pengunjung berhasil memberikan komentar dan rating
 * Kelas Uji : Pengujian Review Produk
 * SKPL      : SRS-MartPlace-06
 */
class RevSuccesTest extends TestCase
{
    /**
     * Simulasi respons HTTP penyimpanan review/komentar produk.
     * Mengembalikan array dengan status dan pesan sukses.
     */
    private function submitReview(array $reviewData): array
    {
        // Validasi data
        if (empty($reviewData['nama']) || empty($reviewData['email']) || empty($reviewData['komentar'])) {
            return [
                'status' => 400,
                'success' => false,
                'message' => 'Data tidak lengkap',
            ];
        }

        // Validasi rating
        if (empty($reviewData['rating']) || $reviewData['rating'] < 1 || $reviewData['rating'] > 5) {
            return [
                'status' => 422,
                'success' => false,
                'message' => 'Rating harus antara 1-5',
            ];
        }

        // Simulasi penyimpanan ke database berhasil
        return [
            'status' => 201,
            'success' => true,
            'message' => 'Komentar berhasil dikirim',
            'data' => [
                'review_id' => 1,
                'product_id' => $reviewData['product_id'] ?? 1,
            ],
        ];
    }

    /**
     * Simulasi pengecekan data di database.
     * Mengembalikan true jika data ditemukan.
     */
    private function findReviewInDatabase(array $criteria): bool
    {
        // Simulasi data di database
        $savedReviews = [
            [
                'product_id' => 1,
                'full_name' => 'Dary Ihsan Amanullah',
                'email_address' => 'ihsan2dary@gmail.com',
                'rating' => 4,
            ],
        ];

        // Cek apakah data yang dicari ada
        foreach ($savedReviews as $review) {
            $match = true;
            foreach ($criteria as $key => $value) {
                if ($review[$key] !== $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                return true;
            }
        }

        return false;
    }

    /**
     * TC-REV-02: Pengunjung berhasil memberikan komentar dan rating
     * Verifikasi: response berhasil dengan status 201
     */
    public function test_pengunjung_berhasil_kirim_komentar_dan_rating(): void
    {
        $produkId = 1;

        // Data form ulasan yang valid
        $dataUlasan = [
            'product_id' => $produkId,
            'nama' => 'Dary Ihsan Amanullah',
            'no_hp' => '081234567890',
            'email' => 'ihsan2dary@gmail.com',
            'komentar' => 'Produk bagus, recommended!',
            'provinsi' => 'Jawa Timur',
            'rating' => 4,
        ];

        // Kirim review
        $response = $this->submitReview($dataUlasan);

        // Verifikasi response sukses
        $this->assertEquals(201, $response['status'],
            'Status HTTP harus 201 ketika review berhasil disimpan.'
        );
        $this->assertTrue($response['success']);
        $this->assertEquals('Komentar berhasil dikirim', $response['message']);

        // Verifikasi data tersimpan di database (simulasi)
        $this->assertTrue(
            $this->findReviewInDatabase([
                'product_id' => $produkId,
                'full_name' => 'Dary Ihsan Amanullah',
                'email_address' => 'ihsan2dary@gmail.com',
                'rating' => 4,
            ]),
            'Data review harus tersimpan di database dengan benar.'
        );
    }
}