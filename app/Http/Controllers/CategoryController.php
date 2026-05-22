<?php
namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        // diurutkan berdasarkan nama
        $categories = Category::orderBy('name')->paginate(10);
        return view('platform.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories,name|max:255',
            'icon' => 'nullable|image|max:2048',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('category_icons', 'public');
            $iconPath = Storage::url($iconPath); // simpan sebagai URL publik
        }

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'icon_path' => $iconPath,
        ]);
        
        return redirect()->route('platform.categories.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $category->id,
            'icon' => 'nullable|image|max:2048',
        ]);
        
        $data = ['name' => $validated['name'], 'slug' => Str::slug($validated['name'])];

        if ($request->hasFile('icon')) {
            // // Hapus ikon lama jika ada
            // if ($category->icon_path) {
            //     $pathToDelete = str_replace('/storage/', '', $category->icon_path);
            //     Storage::disk('public')->delete($pathToDelete);
            // }
            // upload ikon baru
            $iconPath = $request->file('icon')->store('category_icons', 'public');
            $data['icon_path'] = Storage::url($iconPath);
        } else {
            // jaga ikon lama jika tidak ada upload baru
            $data['icon_path'] = $category->icon_path;
        }

        $category->update($data);
        return redirect()->route('platform.categories.index')->with('success', 'Kategori berhasil diperbarui!'); // Rute diubah dari platform.categories ke index
    }

    // hapus
    public function destroy(Category $category)
    {
        // hapus ikon dari storage
        if ($category->icon_path) {
            $pathToDelete = str_replace('/storage/', '', $category->icon_path);
            Storage::disk('public')->delete($pathToDelete);
        }
        
        $category->delete();
        return redirect()->route('platform.categories.index')->with('success', 'Kategori berhasil dihapus.'); // Rute diubah dari platform.categories ke index
    }

    public function showProducts($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            abort(404, 'Kategori tidak ditemukan');
        }

        $filters = $request->all();

        // Base query: produk di kategori ini dari penjual yang aktif dan tidak dideaktivasi oleh admin
        $query = \App\Models\Product::with('user')
            ->where('category_id', $category->id)
            ->whereHas('user', function ($q) {
                $q->where('status_akun', 'active')
                  ->where('deactivated_by_admin', false);
            });

        // Lokasi filter
        if ($request->filled('lokasi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->whereIn('kabupaten', (array) $request->lokasi);
            });
        }

        // Harga filter (opsional)
        if ($request->filled('harga_min') || $request->filled('harga_max')) {
            $min = is_numeric($request->get('harga_min')) ? (float) $request->get('harga_min') : 0;
            $max = is_numeric($request->get('harga_max')) ? (float) $request->get('harga_max') : 50000000;
            $query->whereBetween('price', [$min, $max]);
        }

        // Rating filter
        if ($request->filled('rating')) {
            $query->where('rating', '>=', $request->get('rating'));
        }

        $products = $query->paginate(12)->withQueryString();

        $totalProducts = $products->total();
        $currentCategory = $category->name;

        // Data pendukung untuk sidebar dan filter
        $categories = Category::orderBy('name')->get();

        $locations = \App\Models\Product::select('users.kabupaten')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->where('users.status_akun', 'active')
            ->where('users.deactivated_by_admin', false)
            ->distinct()
            ->pluck('users.kabupaten')
            ->sort()
            ->values();

        $priceStats = \App\Models\Product::whereHas('user', function ($userQuery) {
            $userQuery->where('status_akun', 'active')
                      ->where('deactivated_by_admin', false);
        })->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

        $minPriceDb = $priceStats->min_price ?? 0;
        $maxPriceDb = $priceStats->max_price ?? 50000000;

        return view('katalog', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'currentCategory' => $currentCategory,
            'filters' => $filters,
            'categories' => $categories,
            'locations' => $locations,
            'minPriceDb' => $minPriceDb,
            'maxPriceDb' => $maxPriceDb,
        ]);
    }


}