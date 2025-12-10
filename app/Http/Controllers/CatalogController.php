<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class CatalogController extends Controller
{
    // homepage
    public function home()
    {
        // mengambil 5 produk dengan total ulasan terbanyak dan relasi user
        $trendingProducts = Product::with('user')->orderByDesc('total_ulasan')->take(5)->get();
        $categories = Category::all();
        return view('home', compact('trendingProducts', 'categories'));
    }

    public function index(Request $request)
    {
        $categoryInput = $request->get('kategori');
        $location = $request->get('lokasi');
        $minPrice = $request->get('harga_min', 0);
        $maxPrice = $request->get('harga_max', 50000000);
        $rating = $request->get('rating');

        // ambil query dasar + relasi user (lokasi dan nama toko)
        $query = Product::query()->with('user');

        // kategori
        $categoryModel = null;

        if ($categoryInput) {
            if (is_numeric($categoryInput)) {

                // cari berdasarkan ID
                $categoryModel = Category::find($categoryInput);

                if ($categoryModel) {
                    $query->where('category_id', $categoryModel->id);
                }

            } else {

                // cari berdasarkan nama
                $categoryModel = Category::where('name', $categoryInput)->first();

                if ($categoryModel) {
                    $query->where('category_id', $categoryModel->id);
                }
            }
        }


        // lokasi
        if ($location) {
            $query->whereHas('user', function ($q) use ($location) {
                $q->where('kabupaten', $location)
                ->orWhere('kota', $location);
            });
        }

        //  harga
        $query->whereBetween('price', [(float)$minPrice, (float)$maxPrice]);

        // rating
        if ($rating) {
            $query->where('rating', '>=', $rating);
        }

        // ambil produk dengan pagination
        $products = $query->paginate(12)->withQueryString();

        // total produk sesuai filter
        $totalQuery = Product::query();

        if ($categoryInput) {
            if (is_numeric($categoryInput)) {
                $totalQuery->where('category_id', (int) $categoryInput);
            } else {
                if ($categoryModel) {
                    $totalQuery->where('category_id', $categoryModel->id);
                }
            }
        }

        if ($location) {
            $totalQuery->whereHas('user', function ($q) use ($location) {
                $q->where('kabupaten', $location)
                ->orWhere('kota', $location);
            });
        }

        $totalQuery->whereBetween('price', [(float)$minPrice, (float)$maxPrice]);

        if ($rating) {
            $totalQuery->where('rating', '>=', $rating);
        }

        $totalProducts = $totalQuery->count();

        // kategori untuk sidebar
        $categories = Category::orderBy('name')->get();

        return view('katalog', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'currentCategory' => $categoryModel ? $categoryModel->name : ($categoryInput ?: 'Semua Produk'),
            'filters' => $request->all(),
            'categories' => $categories,
        ]);
    }


}