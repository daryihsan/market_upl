<div id="reviewModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto p-8" style="font-family: 'Inter', sans-serif;">
        
        {{-- Menggunakan $product dari View Induk --}}
        <h2 class="text-xl font-bold text-gray-900 mb-2">Beri Ulasan untuk Produk "{{ $product->name ?? 'Produk' }}"</h2>
        <p class="text-sm text-gray-500 mb-6">Isi detail di bawah ini untuk mengirimkan ulasan Anda.</p>

        {{-- FORM AKSI KE REVIEW CONTROLLER --}}
        <form id="reviewForm" action="{{ route('review.store', $product->id ?? 0) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="fullName" name="fullName" placeholder="Masukkan nama lengkap Anda" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm 
                                    focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="Contoh: 081234567890" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm 
                                    focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="emailAddress" name="emailAddress" placeholder="Contoh: email@example.com" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm 
                                    focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                    <input type="text" id="province" name="province" placeholder="Masukkan provinsi Anda" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm 
                                    focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>
            
            {{-- Lokasi Propinsi --}}
            <div class="mb-6">
                <label for="provinsi_pemberi_rating" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Provinsi</label>
                <select id="provinsi_pemberi_rating" name="provinsi_pemberi_rating" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm 
                            focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Pilih Provinsi Anda</option>
                    <option value="Aceh">Aceh</option>
                    <option value="Bali">Bali</option>
                    <option value="Banten">Banten</option>
                    <option value="DKI Jakarta">DKI Jakarta</option>
                    <option value="Jawa Barat">Jawa Barat</option>
                    <option value="Jawa Tengah">Jawa Tengah</option>
                    <option value="Jawa Timur">Jawa Timur</option>
                    <option value="Sumatra Utara">Sumatra Utara</option>
                    <option value="Lainnya">Lainnya (Provinsi Lain)</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bagaimana penilaian Anda terhadap produk ini?</label>
                <div id="starContainer" class="flex items-center text-gray-300 text-3xl space-x-0.5">
                    <svg class="rating-star w-8 h-8 fill-current text-gray-300" data-rating="1" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.27 5.82 21 7 14.14l-5-4.87 6.91-.01L12 2z"/></svg>
                    <svg class="rating-star w-8 h-8 fill-current text-gray-300" data-rating="2" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.27 5.82 21 7 14.14l-5-4.87 6.91-.01L12 2z"/></svg>
                    <svg class="rating-star w-8 h-8 fill-current text-gray-300" data-rating="3" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.27 5.82 21 7 14.14l-5-4.87 6.91-.01L12 2z"/></svg>
                    <svg class="rating-star w-8 h-8 fill-current text-gray-300" data-rating="4" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.27 5.82 21 7 14.14l-5-4.87 6.91-.01L12 2z"/></svg>
                    <svg class="rating-star w-8 h-8 fill-current text-gray-300" data-rating="5" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.27 5.82 21 7 14.14l-5-4.87 6.91-.01L12 2z"/></svg>
                    <input type="hidden" id="productRating" name="productRating" value="0">
                </div>
            </div>

            <div class="mb-8">
                <label for="reviewText" class="block text-sm font-medium text-gray-700 mb-1">Tulis Ulasan Anda</label>
                <div class="relative">
                    <textarea id="reviewText" name="reviewText" rows="4" maxlength="500" placeholder="Bagikan pengalaman Anda mengenai produk ini" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm resize-none 
                                    focus:outline-none focus:ring-blue-500 focus:border-blue-500" required></textarea>
                    <span id="charCount" class="absolute bottom-2 right-3 text-xs text-gray-400">0/500</span>
                </div>
            </div>

            <div class="flex justify-end items-center space-x-6">
                <button type="button" id="cancelReview" 
                        class="py-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
                    Batalkan
                </button>
                <button type="submit" 
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-md 
                                shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Kirim Ulasan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reviewModal = document.getElementById('reviewModal');
        // Mendapatkan semua tombol pemicu modal
        const openReviewModalBtns = document.querySelectorAll('#openReviewModal, #openReviewModalUlasan'); 
        const cancelReviewBtn = document.getElementById('cancelReview');
        const reviewText = document.getElementById('reviewText');
        const charCount = document.getElementById('charCount');
        const ratingStars = document.querySelectorAll('.rating-star');
        const productRatingInput = document.getElementById('productRating');
        const reviewForm = document.getElementById('reviewForm');
        const yellowColor = '#FBBF24'; 

        const openModal = () => {
            reviewModal.classList.add('flex');
            reviewModal.classList.remove('hidden');
        };

        openReviewModalBtns.forEach(btn => btn.addEventListener('click', openModal));

        const closeModal = () => {
            reviewModal.classList.add('hidden');
            reviewModal.classList.remove('flex');
            
            reviewForm.reset();
            charCount.textContent = '0/500';
            
            productRatingInput.value = '0';
            updateStarColors(0, false); 
        };
        
        cancelReviewBtn.addEventListener('click', closeModal);
        
        reviewModal.addEventListener('click', function(e) {
            if (e.target.id === 'reviewModal') {
                closeModal();
            }
        });

        reviewText.addEventListener('input', function() {
            const currentLength = reviewText.value.length;
            charCount.textContent = `${currentLength}/500`;
        });

        function updateStarColors(rating, isHover = false) {
            ratingStars.forEach((star, index) => {
                const shouldBeYellow = (index < rating);
                if (shouldBeYellow) {
                    star.style.color = yellowColor;
                    star.querySelector('path').setAttribute('d', 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z');
                } else {
                    star.style.color = 'currentColor';
                    star.querySelector('path').setAttribute('d', 'M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.27 5.82 21 7 14.14l-5-4.87 6.91-.01L12 2z');
                }
            });
        }
        
        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                productRatingInput.value = rating;
                updateStarColors(rating, false);
            });
            star.addEventListener('mouseover', function() {
                const hoverRating = parseInt(this.dataset.rating);
                updateStarColors(hoverRating, true);
            });
            star.addEventListener('mouseout', function() {
                const currentRating = parseInt(productRatingInput.value);
                updateStarColors(currentRating, false);
            });
        });

        // Form Validation/Submission (Cek Rating)
        reviewForm.addEventListener('submit', function(event) {
            const ratingValue = productRatingInput.value;
            if (ratingValue == 0) {
                alert('Mohon berikan penilaian bintang terlebih dahulu (1-5).');
                event.preventDefault();
            }
        });
        
        updateStarColors(parseInt(productRatingInput.value), false); 
    });
</script>