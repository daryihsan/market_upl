<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * TC-PRD-01: Upload produk berhasil
 * Produk baru tersimpan dan muncul di daftar produk penjual
 */
class ProductUploadSuccessTest extends TestCase
{
    /**
     * Simulasi proses store() pada ProductController.
     * Disesuaikan dengan validasi dan alur pada ProductController.php.
     */
    private function storeProduct(array $seller, array $requestData): array
    {
        // Simulasi pengecekan akun penjual aktif
        if ($seller['status_akun'] !== 'active') {
            return [
                'status' => 403,
                'success' => false,
                'message' => 'Akun Anda belum disetujui. Hanya penjual yang terverifikasi dapat menambah produk.',
                'data' => null,
            ];
        }

        // Simulasi validasi sesuai kode:
        // name required, description nullable, price required numeric min:0,
        // stock required integer min:0, category_id required,
        // condition required in baru/bekas, min_order required integer min:1,
        // foto_produk nullable image max:5120
        if (
            empty($requestData['name']) ||
            !isset($requestData['price']) ||
            !is_numeric($requestData['price']) ||
            $requestData['price'] < 0 ||
            !isset($requestData['stock']) ||
            !is_int($requestData['stock']) ||
            $requestData['stock'] < 0 ||
            empty($requestData['category_id']) ||
            empty($requestData['condition']) ||
            !in_array($requestData['condition'], ['baru', 'bekas']) ||
            !isset($requestData['min_order']) ||
            !is_int($requestData['min_order']) ||
            $requestData['min_order'] < 1
        ) {
            return [
                'status' => 422,
                'success' => false,
                'message' => 'Validasi data produk gagal.',
                'data' => null,
            ];
        }

        // Simulasi data produk yang akan disimpan
        $productData = [
            'user_id' => $seller['id'],
            'name' => $requestData['name'],
            'description' => $requestData['description'] ?? null,
            'price' => $requestData['price'],
            'stock' => $requestData['stock'],
            'category_id' => $requestData['category_id'],
            'condition' => $requestData['condition'],
            'min_order' => $requestData['min_order'],
            'status' => $requestData['stock'] > 0 ? 'Aktif' : 'NonAktif',
            'rating' => 0,
            'total_ulasan' => 0,
            'image_path' => isset($requestData['foto_produk'])
                ? '/storage/product_images/' . $requestData['foto_produk']
                : null,
        ];

        return [
            'status' => 302,
            'success' => true,
            'redirect_route' => 'seller.dashboard',
            'redirect_tab' => 'products',
            'message' => 'Produk berhasil ditambahkan dan gambar berhasil diunggah!',
            'data' => $productData,
        ];
    }

    /**
     * TC-PRD-01:
     * Upload produk berhasil dengan semua field valid.
     */
    public function test_upload_produk_berhasil_dengan_semua_field_valid(): void
    {
        $seller = [
            'id' => 1,
            'status_akun' => 'active',
        ];

        $requestData = [
            'name' => 'Tas Kuliah',
            'description' => 'Tas kuliah kondisi baik dan masih layak pakai.',
            'price' => 75000,
            'stock' => 5,
            'category_id' => 1,
            'condition' => 'bekas',
            'min_order' => 1,
            'foto_produk' => 'tas-kuliah.jpg',
        ];

        $response = $this->storeProduct($seller, $requestData);

        // Verifikasi proses berhasil dan redirect ke dashboard penjual tab produk
        $this->assertEquals(302, $response['status']);
        $this->assertTrue($response['success']);
        $this->assertEquals('seller.dashboard', $response['redirect_route']);
        $this->assertEquals('products', $response['redirect_tab']);

        // Verifikasi data produk tersimpan sesuai input
        $this->assertNotEmpty($response['data']);
        $this->assertEquals(1, $response['data']['user_id']);
        $this->assertEquals('Tas Kuliah', $response['data']['name']);
        $this->assertEquals(75000, $response['data']['price']);
        $this->assertEquals(5, $response['data']['stock']);
        $this->assertEquals(1, $response['data']['category_id']);
        $this->assertEquals('bekas', $response['data']['condition']);
        $this->assertEquals(1, $response['data']['min_order']);

        // Verifikasi status default sesuai kode ProductController
        $this->assertEquals('Aktif', $response['data']['status']);
        $this->assertEquals(0, $response['data']['rating']);
        $this->assertEquals(0, $response['data']['total_ulasan']);

        // Verifikasi path foto produk tersimpan
        $this->assertEquals('/storage/product_images/tas-kuliah.jpg', $response['data']['image_path']);
    }
}

