<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review; // Asumsikan Model Review sudah dibuat
use App\Mail\ThankYouMail; // Asumsikan Mail Class sudah dibuat
use Illuminate\Support\Facades\Mail; // Import Mail Facade
use Illuminate\Support\Facades\Cache; // Cache for invalidation

class ReviewController extends Controller
{
    /**
     * Menyimpan ulasan, memperbarui rating produk, dan mengirim notifikasi terima kasih (SRS-MartPlace-06).
     */
    public function store(Request $request, Product $product)
    {
        // 1. Validasi data (SRS-MartPlace-06)
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:15',
            'emailAddress' => 'required|email|max:255',
            'productRating' => 'required|integer|min:1|max:5',
            'reviewText' => 'required|string|max:500',
            'province' => 'required|string|max:255',
        ]);

        // 2. Simpan Ulasan ke Database
        Review::create([
            'product_id' => $product->id,
            'full_name' => $validated['fullName'],
            'email_address' => $validated['emailAddress'],
            'phone_number' => $validated['phoneNumber'],
            'province' => $validated['province'],
            'rating' => $validated['productRating'],
            'review_text' => $validated['reviewText'],
        ]);

        // 3. Hitung Ulang Rating Rata-rata & Total Ulasan (SRS-MartPlace-04)
        
        // Mengambil rating rata-rata dari SEMUA ulasan yang terkait dengan produk ini.
        $averageRating = (float) $product->reviews()->avg('rating');
        $totalReviews = (int) $product->reviews()->count();

        // Update model Product dengan nilai yang baru
        // Kita menggunakan nilai float/decimal langsung dari DB::avg() untuk menghindari masalah NULL/NaN
        $product->rating = round($averageRating, 1); // Bulatkan ke 1 desimal
        $product->total_ulasan = $totalReviews;
        $product->save();
        // Invalidate cached rating stats so product page shows fresh numbers
        Cache::forget('product_rating_stats_' . $product->id);
        
        // 4. Kirim Notifikasi Ucapan Terima Kasih via Email (SRS-MartPlace-06)
        Mail::to($validated['emailAddress'])->send(new ThankYouMail(
            $validated['fullName'],
            $product->name
        ));

        // 5. Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Ulasan Anda berhasil dikirim! Terima kasih atas kontribusi Anda.');
    }
}