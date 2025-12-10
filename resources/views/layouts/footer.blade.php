<footer class="bg-white border-t border-gray-200 pt-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="col-span-2 md:col-span-1">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="QuadMarket Logo" class="h-20">
                </div>
                <p class="text-xs text-gray-500">
                    Kami hadir untuk memberikan solusi belanja dengan gaya dan kemudahan yang Anda butuhkan.
                </p>
                <!-- <div class="flex space-x-3 mt-4">
                    <a href="#" class="text-gray-400 hover:text-blue-600"><img src="{{ asset('assets/images/tiktok.png') }}" alt="Tiktok" class="h-4"></a>
                    <a href="#" class="text-gray-400 hover:text-blue-600"><img src="{{ asset('assets/images/facebook.png') }}" alt="Facebook" class="h-4"></a>
                    <a href="#" class="text-gray-400 hover:text-blue-600"><img src="{{ asset('assets/images/twitter.png') }}" alt="Twitter" class="h-4"></a>
                    <a href="#" class="text-gray-400 hover:text-blue-600"><img src="{{ asset('assets/images/instagram.jpg') }}" alt="Instagram" class="h-4"></a>
                </div> -->
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-3">COMPANY</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('aboutus') }}" class="text-xs text-gray-600 hover:text-blue-600">About Us</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-3">HELP</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('customerservice') }}" class="text-xs text-gray-600 hover:text-blue-600">Customer Support</a></li>
                    <!-- <li><a href="#" class="text-xs text-gray-600 hover:text-blue-600">Privacy Policy</a></li> -->
                </ul>
            </div>
            <!-- <div>
                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-3">FAQ</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-xs text-gray-600 hover:text-blue-600">Account</a></li>
                    <li><a href="#" class="text-xs text-gray-600 hover:text-blue-600">Manage Deliveries</a></li>
                    <li><a href="#" class="text-xs text-gray-600 hover:text-blue-600">Orders</a></li>
                    <li><a href="#" class="text-xs text-gray-600 hover:text-blue-600">Payments</a></li>
                </ul>
            </div> -->
            <!-- <div>
                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-3">RESOURCES</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-xs text-gray-600 hover:text-blue-600">Free Ebooks</a></li>
                    <li><a href="#" class="text-xs text-gray-600 hover:text-blue-600">Development Tutorial</a></li>
                    <li><a href="#" class="text-xs text-gray-600 hover:text-blue-600">How to Blog</a></li>
                    <li><a href="#" class="text-xs text-gray-600 hover:text-blue-600">YouTube Playlist</a></li>
                </ul>
            </div> -->
        </div>

        <div class="mt-5 mb-2 border-t border-gray-200 pt-6 flex flex-col md:flex-row justify-between items-center">
            <p class="text-xs text-gray-400 mb-4 md:mb-0">
                © Copyright 2025, All Rights Reserved
            </p>
            <div class="flex space-x-2 items-center">
                <img src="{{ asset('assets/images/visa-logo.png') }}" alt="VISA" class="h-4">
                <img src="{{ asset('assets/images/mastercard-logo.jpg') }}" alt="MasterCard" class="h-4">
                <img src="{{ asset('assets/images/other-cards.png') }}" alt="Other Cards" class="h-4">
            </div>
        </div>
    </div>
</footer>
