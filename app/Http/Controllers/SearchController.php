<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $provinsi = trim($request->get('provinsi', ''));
        $kabupaten = trim($request->get('kabupaten', ''));

        $query = Product::query()
            ->with('user')
            ->whereHas('user', function ($userQuery) {
                // Hanya tampilkan produk dari penjual yang status_akun = 'approved'
                $userQuery->where('status_akun', 'approved');
            });

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        if ($provinsi !== '') {
            $query->whereHas('user', function ($userQuery) use ($provinsi) {
                $userQuery->where('provinsi', $provinsi);
            });
        }

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
            ->appends(['q' => $q, 'provinsi' => $provinsi, 'kabupaten' => $kabupaten]);

        // Get all unique provinsi and kabupaten dari penjual yang approved
        $allProvinsi = Product::select('users.provinsi')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->where('users.status_akun', 'approved')
            ->distinct()
            ->pluck('users.provinsi')
            ->sort()
            ->values();

        $allKabupaten = Product::select('users.kabupaten')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->where('users.status_akun', 'approved');

        if ($provinsi !== '') {
            $allKabupaten = $allKabupaten->where('users.provinsi', $provinsi);
        }

        $allKabupaten = $allKabupaten->distinct()
            ->pluck('users.kabupaten')
            ->sort()
            ->values();

        return view('products.search_results', [
            'products' => $products,
            'q' => $q,
            'provinsi' => $provinsi,
            'kabupaten' => $kabupaten,
            'allProvinsi' => $allProvinsi,
            'allKabupaten' => $allKabupaten,
        ]);
    }
}