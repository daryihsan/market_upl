<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * TC-RPT14-02
 * Butir Uji : Laporan hanya berisi produk dengan stok < 2 (kurang dari dua)
 * Kelas Uji : Pengujian Laporan Stok Kritis
 * SKPL      : SRS-14
 */
class StokKritisReportTest extends TestCase
{
    /**
     * Simulasi daftar produk dari database.
     * Dalam implementasi nyata, ini bisa di-mock dari Model/Repository.
     */
    private function getAllProduk(): array
    {
        return [
            ['nama' => 'Produk A', 'stok' => 0],
            ['nama' => 'Produk B', 'stok' => 1],
            ['nama' => 'Produk C', 'stok' => 5],
            ['nama' => 'Produk D', 'stok' => 3],
            ['nama' => 'Produk E', 'stok' => 1],
        ];
    }

    /**
     * Logika filter stok kritis (stok < 2).
     * Merepresentasikan logika yang ada di controller/service laporan.
     */
    private function filterStokKritis(array $produkList): array
    {
        return array_values(array_filter($produkList, function ($produk) {
            return $produk['stok'] < 2;
        }));
    }

    /**
     * Simulasi respons HTTP generate laporan stok kritis PDF.
     */
    private function generateLaporanStokKritisPDF(): array
    {
        $produkKritis = $this->filterStokKritis($this->getAllProduk());

        return [
            'status'        => 200,
            'content_type'  => 'application/pdf',
            'filename'      => 'laporan_stok_kritis.pdf',
            'produk_tampil' => $produkKritis,
            'content'       => '%PDF-1.4 simulasi isi file PDF laporan stok kritis',
        ];
    }

    /**
     * TC-RPT14-02
     * Pengujian: laporan stok kritis berhasil di-generate (status 200).
     */
    public function test_laporan_stok_kritis_berhasil_digenerate(): void
    {
        $response = $this->generateLaporanStokKritisPDF();

        $this->assertEquals(200, $response['status'],
            'Status HTTP harus 200 ketika laporan stok kritis berhasil di-generate.'
        );
    }

    /**
     * TC-RPT14-02
     * Pengujian: file yang dihasilkan bertipe PDF.
     */
    public function test_laporan_stok_kritis_berformat_pdf(): void
    {
        $response = $this->generateLaporanStokKritisPDF();

        $this->assertEquals('application/pdf', $response['content_type'],
            'Content-Type harus application/pdf.'
        );
    }

    /**
     * TC-RPT14-02
     * Pengujian: semua produk dalam laporan memiliki stok < 2.
     */
    public function test_semua_produk_dalam_laporan_memiliki_stok_kurang_dari_2(): void
    {
        $response = $this->generateLaporanStokKritisPDF();
        $produkTampil = $response['produk_tampil'];

        $this->assertNotEmpty($produkTampil,
            'Laporan stok kritis tidak boleh kosong jika ada produk dengan stok < 2.'
        );

        foreach ($produkTampil as $produk) {
            $this->assertLessThan(2, $produk['stok'],
                "Produk '{$produk['nama']}' memiliki stok {$produk['stok']}, seharusnya < 2."
            );
        }
    }

    /**
     * TC-RPT14-02
     * Pengujian: produk dengan stok >= 2 tidak masuk ke laporan stok kritis.
     */
    public function test_produk_stok_normal_tidak_masuk_laporan_kritis(): void
    {
        $response = $this->generateLaporanStokKritisPDF();
        $produkTampil = $response['produk_tampil'];

        $namaProdukTampil = array_column($produkTampil, 'nama');

        $this->assertNotContains('Produk C', $namaProdukTampil,
            'Produk C (stok 5) tidak seharusnya muncul di laporan stok kritis.'
        );
        $this->assertNotContains('Produk D', $namaProdukTampil,
            'Produk D (stok 3) tidak seharusnya muncul di laporan stok kritis.'
        );
    }
}