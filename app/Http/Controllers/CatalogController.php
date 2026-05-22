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
        // mengambil 5 produk dengan total ulasan terbanyak dan relasi user (hanya dari penjual active)
        $trendingProducts = Product::with('user')
            ->whereHas('user', function ($userQuery) {
                $userQuery->where('status_akun', 'active');
            })
            ->orderByDesc('total_ulasan')
            ->take(5)
            ->get();
        $categories = Category::all();
        return view('home', compact('trendingProducts', 'categories'));
    }

    public function index(Request $request)
    {
        $categoryInput = $request->get('kategori');
        $location = (array) $request->get('lokasi');
        $minPrice = $request->get('harga_min', 0);
        $maxPrice = $request->get('harga_max', 50000000);
        $rating = $request->get('rating');

        // ambil query dasar + relasi user (lokasi dan nama toko)
        // FILTER: Hanya tampilkan produk dari penjual yang status_akun = 'active'
        $query = Product::query()
            ->with('user')
            ->whereHas('user', function ($userQuery) {
                $userQuery->where('status_akun', 'active');
            });

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
        // if (!empty($location)) {
        //     $query->whereHas('user', function ($q) use ($location) {
        //         $q->whereIn('kabupaten', $location);
        //         // ->orWhereIn('kota', $location);
        //     });
        // }

        if ($request->filled('lokasi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->whereIn('kabupaten', $request->lokasi);
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
        $totalQuery = Product::query()
            ->whereHas('user', function ($userQuery) {
                $userQuery->where('status_akun', 'active');
            });

        if ($categoryInput) {
            if (is_numeric($categoryInput)) {
                $totalQuery->where('category_id', (int) $categoryInput);
            } else {
                if ($categoryModel) {
                    $totalQuery->where('category_id', $categoryModel->id);
                }
            }
        }

        if (!empty($location)) {
            $totalQuery->whereHas('user', function ($q) use ($location) {
                $q->whereIn('kabupaten', $location);
                // ->orWhereIn('kota', $location);
            });
        }

        $totalQuery->whereBetween('price', [(float)$minPrice, (float)$maxPrice]);

        if ($rating) {
            $totalQuery->where('rating', '>=', $rating);
        }

        $totalProducts = $totalQuery->count();

        // kategori untuk sidebar
        $categories = Category::orderBy('name')->get();

        // lokasi dari penjual yang active
        $locations = Product::select('users.kabupaten')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->where('users.status_akun', 'active')
            ->distinct()
            ->pluck('users.kabupaten')
            ->sort()
            ->values();

        // harga min-max dari produk active
        $priceStats = Product::whereHas('user', function ($userQuery) {
            $userQuery->where('status_akun', 'active');
        })->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

        $minPriceDb = $priceStats->min_price ?? 0;
        $maxPriceDb = $priceStats->max_price ?? 50000000;

        return view('katalog', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'currentCategory' => $categoryModel ? $categoryModel->name : ($categoryInput ?: 'Semua Produk'),
            'filters' => $request->all(),
            'categories' => $categories,
            'locations' => $locations,
            'minPriceDb' => $minPriceDb,
            'maxPriceDb' => $maxPriceDb,
        ]);
    }


}