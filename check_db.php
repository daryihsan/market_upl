<?php
// Quick database check script
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use App\Models\Product;

echo "=== DATABASE STATUS CHECK ===\n\n";

// Count by status
$activeUsers = User::where('status_akun', 'active')->count();
$pendingUsers = User::where('status_akun', 'pending')->count();
$rejectedUsers = User::where('status_akun', 'rejected')->count();
$approvedCheck = User::where('status_akun', 'approved')->count();
$allUsers = User::count();

echo "Total Users: $allUsers\n";
echo "  - Status 'active': $activeUsers\n";
echo "  - Status 'pending': $pendingUsers\n";
echo "  - Status 'rejected': $rejectedUsers\n";
echo "  - Status 'approved': $approvedCheck (NON-EXISTENT)\n\n";

// Products
$allProducts = Product::count();
$productsWithActiveUsers = Product::whereHas('user', function($q) {
    $q->where('status_akun', 'active');
})->count();
$productsWithApprovedUsers = Product::whereHas('user', function($q) {
    $q->where('status_akun', 'approved');
})->count();

echo "Total Products: $allProducts\n";
echo "  - Products from users with status='active': $productsWithActiveUsers\n";
echo "  - Products from users with status='approved': $productsWithApprovedUsers (WRONG QUERY)\n\n";

// Show distinct status_akun values in database
$statusValues = User::select('status_akun')->distinct()->get();
echo "Distinct status_akun values in database:\n";
foreach ($statusValues as $u) {
    echo "  - '{$u->status_akun}'\n";
}
echo "\n";

// Check migrations
$migrations = User::all()->take(3);
if ($migrations->count() > 0) {
    echo "Sample users:\n";
    foreach ($migrations as $u) {
        echo "  - {$u->nama_toko} (status: {$u->status_akun})\n";
    }
}
