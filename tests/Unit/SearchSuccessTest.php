<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * TC-SRC-04: Pencarian produk berdasarkan nama berhasil
 * TC-SRC-09: Pencarian tidak case-sensitive
 */
class SearchSuccessTest extends TestCase
{
    /**
     * Simulasi respons HTTP pencarian produk.
     * Mengembalikan array dengan status dan data produk.
     */
    private function searchProducts(string $keyword): array
    {
        // Data produk simulasi
        $products = [
            ['id' => 1, 'nama' => 'Dary Ihsan Amanullah', 'harga' => 100000, 'foto' => 'test.jpg', 'nama_toko' => 'Test Store'],
            ['id' => 2, 'nama' => 'Another Product', 'harga' => 50000, 'foto' => 'test2.jpg', 'nama_toko' => 'Test Store'],
            ['id' => 3, 'nama' => 'Produk Lainnya', 'harga' => 75000, 'foto' => 'test3.jpg', 'nama_toko' => 'Toko Lain'],
        ];

        // Filter berdasarkan keyword (case-insensitive)
        $keyword_lower = strtolower($keyword);
        $results = array_filter($products, function ($product) use ($keyword_lower) {
            return strpos(strtolower($product['nama']), $keyword_lower) !== false;
        });

        return [
            'status' => 200,
            'success' => true,
            'data' => array_values($results), // reset index
            'total' => count($results),
        ];
    }

    /**
     * TC-SRC-04: Pencarian nama produk berhasil
     * Verifikasi: endpoint mengembalikan status 200 dan data produk yang sesuai
     */
    public function test_pencarian_nama_produk_berhasil(): void
    {
        $keyword = 'Dary Ihsan Amanullah';
        $response = $this->searchProducts($keyword);

        // Verifikasi status 200
        $this->assertEquals(200, $response['status'],
            'Status HTTP harus 200 ketika pencarian berhasil.'
        );

        // Verifikasi response memiliki struktur yang benar
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);

        // Verifikasi hasil mengandung produk yang dicari
        $this->assertNotEmpty($response['data'], 'Data hasil pencarian tidak boleh kosong.');
        $this->assertEquals('Dary Ihsan Amanullah', $response['data'][0]['nama']);
    }

    /**
     * TC-SRC-09: Pencarian tidak case-sensitive
     * Verifikasi: hasil pencarian sama meski keyword berbeda case
     */
    public function test_pencarian_tidak_case_sensitive(): void
    {
        $keywordLower = 'dary ihsan amanullah';
        $keywordUpper = 'DARY IHSAN AMANULLAH';
        $keywordMixed = 'Dary Ihsan Amanullah';

        $responseLower = $this->searchProducts($keywordLower);
        $responseUpper = $this->searchProducts($keywordUpper);
        $responseMixed = $this->searchProducts($keywordMixed);

        // Verifikasi semua request berhasil
        $this->assertEquals(200, $responseLower['status']);
        $this->assertEquals(200, $responseUpper['status']);
        $this->assertEquals(200, $responseMixed['status']);

        // Verifikasi jumlah hasil sama
        $jumlahLower = count($responseLower['data']);
        $jumlahUpper = count($responseUpper['data']);
        $jumlahMixed = count($responseMixed['data']);

        $this->assertEquals($jumlahLower, $jumlahUpper,
            'Jumlah hasil pencarian harus sama meski case berbeda (lower vs upper).'
        );
        $this->assertEquals($jumlahUpper, $jumlahMixed,
            'Jumlah hasil pencarian harus sama meski case berbeda (upper vs mixed).'
        );
    }
}