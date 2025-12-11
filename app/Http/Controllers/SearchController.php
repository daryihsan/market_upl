<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = Product::query()->with('user');

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
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
            ->appends(['q' => $q]);

        return view('products.search_results', [
            'products' => $products,
            'q'        => $q,
        ]);
    }
}