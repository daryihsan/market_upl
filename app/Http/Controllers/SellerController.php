<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; 
use App\Models\Category; 
use App\Models\Review;   
use App\Models\User;     
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login.login'); 
        }
        // Blokir hanya jika pending atau dinonaktifkan oleh admin
        if ($user->status_akun === 'pending' || ($user->status_akun === 'rejected' && ($user->deactivated_by_admin ?? false))) {
            return Redirect::back()->with('error', 'Toko Anda saat ini tidak aktif. Silakan hubungi admin untuk informasi lebih lanjut.');
        }

        $userId = $user->id; 
        $activeTab = $request->query('tab', 'overview');

        // --- 1. Data Dashboard Dinamis ---
        $totalProducts = Product::where('user_id', $userId)->count();
        $averageRating = Product::where('user_id', $userId)->avg('rating');
        
        // ========================================================
        // Poin 4 & 8: LOGIKA DATA DINAMIS UNTUK CHART
        // ========================================================
        
        $sellerProductIds = Product::where('user_id', $userId)->pluck('id');
        $reviews = Review::whereIn('product_id', $sellerProductIds)->get();
        $totalReviewsCount = $reviews->count();
        // Gunakan field 'province' dari tabel reviews (kolom migrasi dan model menggunakan 'province')
        $reviewsByProvince = $reviews->groupBy('province')->map->count();

        $distributionData = [];
        $sortedProvinces = $reviewsByProvince->sortDesc()->take(3); // Ambil 3 provinsi teratas
        $otherReviewsCount = $totalReviewsCount - $sortedProvinces->sum();

        // 1. Olah data 3 provinsi teratas
        foreach ($sortedProvinces as $province => $count) {
            $percentage = $totalReviewsCount > 0 ? round(($count / $totalReviewsCount) * 100) : 0;
            $distributionData[] = (object)['Lokasi' => $province, 'Persentase' => $percentage];
        }

        // 2. Olah data 'Lainnya'
        if ($otherReviewsCount > 0) {
            $otherPercentage = $totalReviewsCount > 0 ? round(($otherReviewsCount / $totalReviewsCount) * 100) : 0;
            $distributionData[] = (object)['Lokasi' => 'Lainnya', 'Persentase' => $otherPercentage];
        }
        
        // Susun array lokasi untuk Blade (Poin 4 FIX)
        $locationData = [
            'TotalOrders' => $totalReviewsCount, // Total Rating dinamis
            (object)['Lokasi' => $distributionData[0]->Lokasi ?? 'N/A', 'Persentase' => $distributionData[0]->Persentase ?? 0],
            (object)['Lokasi' => $distributionData[1]->Lokasi ?? 'N/A', 'Persentase' => $distributionData[1]->Persentase ?? 0],
            (object)['Lokasi' => $distributionData[2]->Lokasi ?? 'N/A', 'Persentase' => $distributionData[2]->Persentase ?? 0],
        ];

        // Poin 8: Data Chart Rating Per Produk (Top 5)
        $topRatedProducts = Product::where('user_id', $userId)
            ->whereNotNull('rating')
            ->orderByDesc('rating')
            ->take(5)
            ->get();

        // Data Stok per Kategori (diambil dari database)
        $productCountsByCategory = Product::where('user_id', $userId)
            ->with('category')
            ->get()
            // Mengelompokkan berdasarkan nama kategori produk yang valid
            ->groupBy(function($product) {
                return $product->category->name ?? 'Tanpa Kategori';
            })
            // Menghitung total stok untuk setiap kelompok kategori
            ->map(function($group) {
                return $group->sum('stock');
            });

        // Konversi ke format array of objects untuk chart JS (PENTING UNTUK JS)
        $salesByCategory = $productCountsByCategory->map(function($stock, $name) {
            return (object) [
                'Kategori' => $name, 
                'Penjualan' => $stock // Nilai yang digunakan untuk tinggi bar
            ];
        })->values()->toArray();
        
        // --- Produk Terbaru
        $latestProducts = Product::where('user_id', $userId)
                                 ->with('category')
                                 ->orderByDesc('created_at')
                                 ->take(4)
                                 ->get();


        // --- Data Kategori & Statistik
        $allCategories = Category::all();
        $productStats = $this->getProductStatistics($userId);
        $products = Product::where('user_id', $userId)->with('category')->latest()->paginate(10); 
        
        $editProduct = null;
        if ($activeTab === 'addProduct' && $request->query('mode') === 'edit') {
            $productId = $request->query('id'); 
            $editProduct = Product::where('user_id', $userId)
                                         ->where('id', $productId)
                                        //  ->with('variants')
                                         ->first();
        }
        
        // Data yang dikirim ke View
        $data = [
            'totalProducts' => $totalProducts, 
            'averageRating' => round($averageRating ?? 0, 1),
            'salesByCategory' => $salesByCategory, 
            'locationData' => $locationData, 
            'latestProducts' => $latestProducts,
            'productStats' => $productStats, 
            'products' => $products, 
            'allCategories' => $allCategories,
            'editProduct' => $editProduct,
            'topRatedProducts' => $topRatedProducts, 
        ];
        
        // Mengirim $user agar data toko dapat diakses di Blade (PENTING)
        return view('seller.dashboard', compact('activeTab', 'data', 'user'));
    }
    
    /**
     * Helper: Menghitung statistik ringkasan produk.
     */
    private function getProductStatistics($userId)
    {
        $total = Product::where('user_id', $userId)->count();
        $aktif = Product::where('user_id', $userId)->where('status', 'Aktif')->count();
        $habis = Product::where('user_id', $userId)->where('stock', 0)->count();
        $tidakAktif = Product::where('user_id', $userId)->where('status', 'NonAktif')->count();

        return [
            'total_produk' => $total,
            'produk_aktif' => $aktif,
            'stok_habis'   => $habis,
            'tidak_aktif'  => $tidakAktif,
        ];
    }



    /**
     * Menyimpan produk baru (Poin 6, 7).
     */
    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        if ($user->status_akun !== 'active') {
            return Redirect::back()->with('error', 'Akun Anda saat ini tidak aktif. Anda tidak dapat menambahkan produk.');
        }

        $userId = $user->id; 

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0', 
            'category_id' => 'required|exists:categories,id',
            'min_order' => 'required|integer|min:1',
            'condition' => 'required|string|in:baru,bekas', 
            'foto_produk' => 'required|image|max:5120', 
            'status_override' => 'nullable|in:Aktif,NonAktif', 
            
            'variants' => 'nullable|array',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
        ]);

        $productData = $validated;
        $productData['user_id'] = $userId; 
        
        $productData['status'] = $request->filled('status_override') ? 
                                    $request->status_override : 
                                    ($validated['stock'] > 0 ? 'Aktif' : 'NonAktif');
                                
        $productData['rating'] = 0;
        $productData['total_ulasan'] = 0;
        
        if ($request->hasFile('foto_produk')) {
            $fotoPath = $request->file('foto_produk')->store('public/product_images'); 
            $productData['image_path'] = Storage::url($fotoPath);
        }

        unset($productData['foto_produk'], $productData['status_override']); 
        
        $product = Product::create($productData); 

        if (isset($validated['variants'])) {
            $variantsToCreate = [];
            foreach ($validated['variants'] as $variant) {
                $variantsToCreate[] = [
                    'name' => $variant['name'],
                    'stock' => $variant['stock'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $product->variants()->createMany($variantsToCreate);
        }

        return Redirect::route('seller.dashboard', ['tab' => 'products'])->with('success', 'Produk berhasil ditambahkan!');
    }
    
    /**
     * Memperbarui produk (Poin 6, 7).
     */
    public function updateProduct(Request $request, Product $product)
    {
        $user = Auth::user();
        if ($user->status_akun !== 'active') {
            return Redirect::back()->with('error', 'Akun Anda saat ini tidak aktif. Anda tidak dapat mengedit produk.');
        }

        $userId = $user->id;
        if ($product->user_id !== $userId) {
            return Redirect::back()->with('error', 'Anda tidak berhak mengedit produk ini.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'min_order' => 'required|integer|min:1',
            'condition' => 'required|string|in:baru,bekas',
            'foto_produk' => 'nullable|image|max:5120',
            'status_override' => 'nullable|in:Aktif,NonAktif',
            
            'variants' => 'nullable|array',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
        ]);

        $productData = $validated;
        
        $productData['status'] = $request->filled('status_override') ? 
                                    $request->status_override : 
                                    ($validated['stock'] > 0 ? 'Aktif' : 'NonAktif');
        
        if ($request->hasFile('foto_produk')) {
            if ($product->image_path) {
                $path = str_replace(config('app.url') . '/storage', 'public', $product->image_path);
                Storage::delete($path);
            }
            $fotoPath = $request->file('foto_produk')->store('public/product_images');
            $productData['image_path'] = Storage::url($fotoPath);
        }

        unset($productData['foto_produk'], $productData['status_override']); 
        
        $product->update($productData);
        
        // $product->variants()->delete();

        if (isset($validated['variants'])) {
            $variantsToCreate = [];
            foreach ($validated['variants'] as $variant) {
                $variantsToCreate[] = [
                    'name' => $variant['name'],
                    'stock' => $variant['stock'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $product->variants()->createMany($variantsToCreate);
        }

        return Redirect::route('seller.dashboard', ['tab' => 'products'])->with('success', 'Produk berhasil diperbarui!');
    }
    
    public function deleteProduct(Product $product)
    {
        $user = Auth::user();
        if ($user->status_akun !== 'active') {
            return Redirect::back()->with('error', 'Akun Anda saat ini tidak aktif. Anda tidak dapat menghapus produk.');
        }

        $userId = $user->id;
        if ($product->user_id !== $userId) {
            return Redirect::back()->with('error', 'Anda tidak berhak menghapus produk ini.');
        }
        
        if (method_exists($product, 'variants')) {
            $product->variants()->delete();
        }
        
        if ($product->image_path) {
            $path = str_replace(config('app.url') . '/storage', 'public', $product->image_path);
            Storage::delete($path);
        }

        $product->delete();
        
        return Redirect::route('seller.dashboard', ['tab' => 'products'])->with('success', 'Produk berhasil dihapus.');
    }
    
    /**
     * Mengganti status akun penjual (Aktif/Nonaktif) tanpa logout (Poin 5).
     */
    public function toggleStatus(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return Redirect::to('/login'); 
        }

        $newStatus = $request->input('status'); 
        
        if ($user->status_akun === 'active' && $newStatus === 'rejected') {
            $user->status_akun = 'rejected';
            $user->deactivated_by_admin = false;
            $user->save();
            return Redirect::back()->with('success', 'Toko Anda berhasil dinonaktifkan. Produk tidak akan muncul di katalog.');
        } 
        
        elseif ($user->status_akun === 'rejected' && $newStatus === 'active') {
            $user->status_akun = 'active';
            $user->deactivated_by_admin = false;
            $user->save();
            return Redirect::back()->with('success', 'Toko Anda berhasil diaktifkan kembali. Produk Anda kini tampil di katalog.');
        }
        
        return Redirect::back()->with('error', 'Gagal mengubah status toko.');
    }
    
}