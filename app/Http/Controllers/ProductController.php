<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Tampilkan formulir pembuatan produk baru.
     */
    public function create()
    {
        // Check if seller is active
        $seller = Auth::user();
        if ($seller->status_akun !== 'active') {
            return redirect()->back()
                ->with('error', 'Akun Anda belum disetujui. Hanya penjual yang terverifikasi dapat menambah produk.');
        }

        $categories = Category::all();
        return view('seller.products.create', compact('categories'));
    }

    /**
     * Simpan data produk baru (Route: seller.products.store).
     */
    public function store(Request $request)
    {
        // Check if seller is active
        $seller = Auth::user();
        if ($seller->status_akun !== 'active') {
            return redirect()->back()
                ->with('error', 'Akun Anda belum disetujui. Hanya penjual yang terverifikasi dapat menambah produk.');
        }

        // 1. Validasi Data
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'condition'   => 'required|in:baru,bekas',
            'min_order'   => 'required|integer|min:1',
            'foto_produk' => 'nullable|image|max:5120', 
        ]);

        $productData = $validatedData;
        $productData['user_id'] = Auth::id();
        $productData['status'] = $validatedData['stock'] > 0 ? 'Aktif' : 'NonAktif'; 
        $productData['rating'] = 0; 
        $productData['total_ulasan'] = 0; 

        // 2. Upload Foto Produk dan Simpan Path (Perbaikan Gambar di sini)
        if ($request->hasFile('foto_produk')) {
            // Simpan file ke direktori 'product_images' di disk 'public'.
            // $fotoPath akan berisi path relatif (misal: 'product_images/abc.jpg').
            $fotoPath = $request->file('foto_produk')->store('product_images', 'public'); 
            
            // Storage::url() akan mengonversi path relatif menjadi URL publik.
            // (Hasil: /storage/product_images/abc.jpg)
            $productData['image_path'] = Storage::url($fotoPath); 
        } else {
            $productData['image_path'] = null;
        }

        // Hapus field file dari data sebelum disimpan ke database
        unset($productData['foto_produk']); 

        Product::create($productData); 

        return redirect()->route('seller.dashboard', ['tab' => 'products'])
                         ->with('success', 'Produk berhasil ditambahkan dan gambar berhasil diunggah!');
    }
    
    /**
     * Update produk yang sudah ada (Route: seller.products.update).
     */
    public function update(Request $request, Product $product)
    {
        // Model binding menjamin $product adalah instance Product
        if ($product->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak berhak mengedit produk ini.');
        }
        
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'condition'   => 'required|in:baru,bekas',
            'min_order'   => 'required|integer|min:1',
            'foto_produk' => 'nullable|image|max:5120',
        ]);
        
        $productData = $validatedData;
        $productData['status'] = $validatedData['stock'] > 0 ? 'Aktif' : 'NonAktif';

        if ($request->hasFile('foto_produk')) {
            // 1. Hapus gambar lama dari storage (Perbaikan Gambar di sini)
            if ($product->image_path) {
                // Hapus path '/storage/' dari URL untuk mendapatkan path relatif yang dapat dihapus.
                $pathToDelete = str_replace('/storage/', '', $product->image_path);
                Storage::disk('public')->delete($pathToDelete);
            }
            
            // 2. Unggah gambar baru
            $fotoPath = $request->file('foto_produk')->store('product_images', 'public');
            $productData['image_path'] = Storage::url($fotoPath);
        }

        unset($productData['foto_produk']);

        $product->update($productData);

        return redirect()->route('seller.dashboard', ['tab' => 'products'])
                         ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus produk (Route: seller.products.destroy).
     */
    public function destroy(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak berhak menghapus produk ini.');
        }
        
        // Hapus gambar dari storage (Perbaikan Gambar di sini)
        if ($product->image_path) {
            // Hapus path '/storage/' dari URL untuk mendapatkan path relatif.
            $path = str_replace('/storage/', '', $product->image_path);
            Storage::disk('public')->delete($path);
        }

        $product->delete();

        return redirect()->route('seller.dashboard', ['tab' => 'products'])
                         ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Show product detail with computed rating stats (cached).
     */
    public function show($id)
    {
        // Eager-load basic relations and reviews collection (for local aggregation)
        $product = Product::with(['user', 'category', 'reviews'])->findOrFail($id);

        $cacheKey = 'product_rating_stats_' . $product->id;

        $stats = Cache::remember($cacheKey, 300, function () use ($product) {
            $totalReviews = $product->reviews->count();
            $avg = $product->rating ?? ($totalReviews > 0 ? round($product->reviews->avg('rating'), 1) : 0);

            $starCounts = [];
            $starPercentages = [];
            for ($s = 1; $s <= 5; $s++) {
                $count = $product->reviews->where('rating', $s)->count();
                $starCounts[$s] = $count;
                $starPercentages[$s] = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
            }

            return compact('totalReviews', 'avg', 'starCounts', 'starPercentages');
        });

        // Unpack for the view
        $totalReviews = $stats['totalReviews'] ?? 0;
        $rating = $stats['avg'] ?? 0;
        $starCounts = $stats['starCounts'] ?? [];
        $starPercentages = $stats['starPercentages'] ?? [];

        // Ensure product has fields for old view fallbacks
        $product->rating = $product->rating ?? $rating;
        $product->total_ulasan = $product->total_ulasan ?? $totalReviews;

        return view('products.detail', compact('product', 'totalReviews', 'rating', 'starCounts', 'starPercentages'));
    }
}