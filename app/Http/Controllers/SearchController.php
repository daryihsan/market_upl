<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $toko = trim($request->get('toko', ''));
        $kategori = trim($request->get('kategori', ''));
        $provinsi = trim($request->get('provinsi', ''));
        $kabupaten = trim($request->get('kabupaten', ''));

        $query = Product::query()
            ->with(['user', 'category'])
            ->whereHas('user', function ($userQuery) {
                // Hanya tampilkan produk dari penjual yang status_akun = 'active'
                $userQuery->where('status_akun', 'active');
            });

        // Filter pencarian: nama produk
        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        // Filter pencarian: nama toko
        if ($toko !== '') {
            $query->whereHas('user', function ($userQuery) use ($toko) {
                $userQuery->where('nama_toko', 'like', "%{$toko}%");
            });
        }

        // Filter kategori
        if ($kategori !== '') {
            $query->where('category_id', $kategori);
        }

        // Filter provinsi
        if ($provinsi !== '') {
            $query->whereHas('user', function ($userQuery) use ($provinsi) {
                $userQuery->where('provinsi', $provinsi);
            });
        }

        // Filter kabupaten
        if ($kabupaten !== '') {
            $query->whereHas('user', function ($userQuery) use ($kabupaten) {
                $userQuery->where('kabupaten', $kabupaten);
            });
        }

        // ==== MODE AJAX (buat dropdown header + hero) ====
        // dipanggil saat URL /search?ajax=1&q=...
        if ($request->get('ajax') == 1) {
            $items = $query
                ->select('id', 'name')
                ->orderBy('name')
                ->limit(10)
                ->get();

            return response()->json($items);
        }

        // ==== MODE NORMAL (klik "Cari Sekarang") ====
        $products = $query
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends([
                'q' => $q,
                'toko' => $toko,
                'kategori' => $kategori,
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten
            ]);

        // Get all unique provinsi dari penjual yang active
        $allProvinsi = Product::select('users.provinsi')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->where('users.status_akun', 'active')
            ->distinct()
            ->pluck('users.provinsi')
            ->sort()
            ->values();

        // Get all unique kabupaten dari penjual yang active
        $allKabupaten = Product::select('users.kabupaten')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->where('users.status_akun', 'active');

        if ($provinsi !== '') {
            $allKabupaten = $allKabupaten->where('users.provinsi', $provinsi);
        }

        $allKabupaten = $allKabupaten->distinct()
            ->pluck('users.kabupaten')
            ->sort()
            ->values();

        // Get all categories yang memiliki produk
        $allCategories = Category::whereHas('products', function ($productQuery) {
            $productQuery->whereHas('user', function ($userQuery) {
                $userQuery->where('status_akun', 'active');
            });
        })->get();

        return view('products.search_results', [
            'products' => $products,
            'q' => $q,
            'toko' => $toko,
            'kategori' => $kategori,
            'provinsi' => $provinsi,
            'kabupaten' => $kabupaten,
            'allProvinsi' => $allProvinsi,
            'allKabupaten' => $allKabupaten,
            'allCategories' => $allCategories,
        ]);
    }
}