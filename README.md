# QuadMarket

![QuadMarket](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)
![Vite](https://img.shields.io/badge/Vite-7.0+-646CFF?style=flat&logo=vite)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-4.0+-06B6D4?style=flat&logo=tailwindcss)
![License](https://img.shields.io/badge/License-MIT-green)

Platform marketplace modern yang menghubungkan pembeli dan penjual dengan sistem verifikasi berlapis dan manajemen produk yang komprehensif.

## 📋 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Teknologi](#teknologi)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Struktur Proyek](#struktur-proyek)
- [Database](#database)
- [API & Rute](#api--rute)
- [Panduan Penggunaan](#panduan-penggunaan)
- [Migrasi & Seeding](#migrasi--seeding)
- [Troubleshooting](#troubleshooting)
- [Lisensi](#lisensi)

---

## 🚀 Fitur Utama

### Untuk Pengunjung
- ✅ **Browsing Katalog**: Jelajahi produk dengan sistem kategori yang terstruktur
- 🔍 **Pencarian Produk**: Fitur pencarian responsif
- ⭐ **Review & Rating**: Buat ulasan dan rating untuk produk
- 📱 **Interface Responsif**: Desain modern dengan Tailwind CSS

### Untuk Penjual
- 📦 **Manajemen Produk**: CRUD lengkap untuk produk
- 🏪 **Dashboard Penjual**: Overview produk dan rating
- 📊 **Laporan Penjualan**: Generate laporan dalam format PDF
- ✔️ **Verifikasi Akun**: Proses registrasi bertahap dengan verifikasi email
- 📄 **Upload Dokumen**: Upload KTP dan dokumen verifikasi

### Sistem Admin
- 👥 **Manajemen Pengguna**: Kelola penjual
- 🔐 **Kontrol Akun**: Deaktivasi akun untuk penjual yang tidak memenuhi kriteria
- 📋 **Monitoring Status**: Tracking status verifikasi dan aktivasi penjual

### Sistem Keamanan & Verifikasi
- 🔐 **Autentikasi Tertingkat**: Sistem login terpisah untuk pengunjung, penjual, dan admin
- 📧 **Verifikasi Email**: Token-based email verification untuk keamanan
- 🔑 **OTP Verification**: Sistem OTP untuk verifikasi tambahan
- 📄 **Document Verification**: Upload dan verifikasi KTP untuk penjual

---

## 🛠️ Teknologi

**Backend:**
- **Laravel 12** - Framework PHP modern
- **PHP 8.2+** - Bahasa pemrograman
- **MySQL** - Database management system
- **Composer** - PHP dependency manager

**Frontend:**
- **Vite 7** - Modern build tool
- **Tailwind CSS 4** - Utility-first CSS framework
- **Axios** - HTTP client
- **JavaScript (ES Module)**

**Development Tools:**
- **PHPUnit** - Testing framework
- **Laravel Pest** - Testing utility
- **Mockery** - Mock object framework
- **Laravel Pint** - Code style fixer
- **Faker** - Fake data generator
- **dompdf** - PDF generation

**Additional Packages:**
- **Laravel Tinker** - Interactive shell
- **Laravel Sail** - Docker-based development
- **Laravel Pail** - Log viewer
- **barryvdh/laravel-ide-helper** - IDE auto-completion

---

## 💾 Instalasi

### Prasyarat
- PHP 8.2 atau lebih tinggi
- Composer 2.x
- Node.js 18+ & npm
- MySQL 8.0+
- Git

### Langkah-langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/yourusername/market_upl.git
   cd market_upl
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Node Dependencies**
   ```bash
   npm install
   ```

4. **Setup Environment File**
   ```bash
   # Copy file .env.example ke .env
   cp .env.example .env
   
   # Generate APP_KEY
   php artisan key:generate
   ```

5. **Database Migration & Seeding**
   ```bash
   # Jalankan migration
   php artisan migrate
   
   # Jalankan seeding (optional)
   php artisan db:seed
   ```

6. **Generate IDE Helper** (optional untuk development)
   ```bash
   php artisan ide-helper:generate
   php artisan ide-helper:models
   ```

---

## ⚙️ Konfigurasi

### Konfigurasi Filesystem
Untuk production, gunakan storage disk yang tepat:
```bash
php artisan storage:link
```

### Membuat Admin User (Manual)
```bash
php artisan tinker

# Dalam tinker shell:
>>> $admin = new \App\Models\User();
>>> $admin->email = 'admin@domain.co';
>>> $admin->password = bcrypt('password');
>>> $admin->role = 'admin';
>>> $admin->save();
```

---

## 📁 Struktur Proyek

```
market_upl/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controller untuk business logic
│   │   │   ├── AuthController.php
│   │   │   ├── RegisterController.php
│   │   │   ├── LoginController.php
│   │   │   ├── SellerController.php
│   │   │   ├── ProductController.php
│   │   │   ├── SearchController.php
│   │   │   ├── ReviewController.php
│   │   │   └── ...
│   │   └── Middleware/           # HTTP Middleware
│   ├── Models/                   # Eloquent Models
│   │   ├── User.php              # Model user (penjual)
│   │   ├── Seller.php            # Model seller khusus
│   │   ├── Product.php           # Model produk
│   │   ├── Category.php          # Kategori produk
│   │   ├── Review.php            # Review produk
│   │   ├── OTPVerification.php    # OTP verification
│   │   ├── UserDocument.php       # Dokumen pengguna
│   │   └── ...
│   ├── Notifications/            # Notifikasi sistem
│   │   ├── VerifyUserEmail.php
│   │   ├── SellerVerificationResult.php
│   │   └── ...
│   ├── Mail/                     # Mailable classes
│   │   ├── ThankYouMail.php
│   │   ├── SellerVerificationMail.php
│   │   └── SellerRejectionMail.php
│   ├── Services/                 # Business services
│   │   └── OTPService.php
│   └── Providers/                # Service providers
│       ├── AppServiceProvider.php
│       └── ...
├── bootstrap/
│   ├── app.php                   # Application bootstrap
│   └── providers.php
├── config/                       # Konfigurasi aplikasi
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── mail.php
│   ├── filesystems.php
│   └── ...
├── database/
│   ├── migrations/               # Database migrations
│   │   ├── create_users_table.php
│   │   ├── create_sellers_table.php
│   │   ├── create_products_table.php
│   │   ├── create_categories_table.php
│   │   ├── create_reviews_table.php
│   │   ├── create_o_t_p_verifications_table.php
│   │   ├── create_user_documents_table.php
│   │   └── ...
│   ├── seeders/                  # Database seeders
│   │   └── DatabaseSeeder.php
│   └── factories/                # Model factories
│       └── UserFactory.php
├── public/
│   ├── index.php                 # Entry point aplikasi
│   ├── assets/                   # Static assets
│   ├── images/                   # Image storage
│   └── build/                    # Vite build output
├── resources/
│   ├── views/                    # Blade templates
│   ├── css/                      # CSS files (app.css)
│   └── js/                       # JavaScript files (app.js)
├── routes/
│   ├── web.php                   # Web routes
│   └── console.php               # Console commands
├── storage/
│   ├── app/                      # File uploads
│   ├── framework/                # Framework cache
│   └── logs/                     # Application logs
├── tests/
│   ├── Unit/                     # Unit tests
│   ├── Feature/                  # Feature tests
│   └── TestCase.php
├── vendor/                       # Composer dependencies
├── .env.example                  # Environment template
├── .gitignore
├── artisan                       # Artisan CLI
├── composer.json                 # PHP dependencies
├── package.json                  # Node.js dependencies
├── vite.config.js                # Vite configuration
└── phpunit.xml                   # PHPUnit configuration
```

---

## 🗄️ Database

### Skema Database Utama

**Users Table**
- Primary user authentication untuk penjual
- Menyimpan: email, password, nama toko, alamat, dokumen
- Status verifikasi email dan deaktivasi admin

**Products Table**
- Detail produk: nama, deskripsi, harga, stok
- Relasi ke kategori dan seller
- Status produk (aktif, pending, dihapus)

**Categories Table**
- Kategori produk yang tersedia
- Support untuk kategori bersarang

**Reviews Table**
- Rating dan ulasan dari pengunjung
- Relasi ke produk dan user

**OTP Verifications Table**
- Menyimpan kode OTP untuk verifikasi
- Tracking otentikasi 2-faktor

**User Documents Table**
- Menyimpan dokumen pengguna (KTP dll)
- Verifikasi status dokumen

**Sessions Table**
- Laravel session management
- Database-driven session storage

---

## 🛣️ API & Rute

### Rute Publik (Untuk Pengunjung)

| Method | Route | Controller | Deskripsi |
|--------|-------|-----------|-----------|
| GET | `/` | CatalogController@home | Halaman utama |
| GET | `/katalog` | CatalogController@index | Daftar semua produk |
| GET | `/product/{id}/detail` | ProductController@show | Detail produk |
| GET | `/search` | SearchController@index | Halaman pencarian produk |
| POST | `/product/{id}/review` | ReviewController@store | Buat review produk |

### Rute Registrasi Penjual

| Method | Route | Controller | Deskripsi |
|--------|-------|-----------|-----------|
| GET | `/register/step1` | RegisterController@showStep1 | Form step 1 registrasi |
| POST | `/register/step1` | RegisterController@processStep1 | Proses step 1 |
| GET | `/register/step2` | RegisterController@showStep2 | Form step 2 registrasi |
| POST | `/register/step2` | RegisterController@processStep2 | Proses step 2 |
| GET | `/register/step3` | RegisterController@showStep3 | Form step 3 registrasi |
| POST | `/register/step3` | RegisterController@processStep3 | Proses step 3 |
| GET | `/register/success` | RegisterController@showSuccess | Sukses registrasi |
| GET | `/get-kabupaten/{provinsi}` | RegisterController@getKabupaten | Get kabupaten via AJAX |

### Rute Email Verification

| Method | Route | Controller | Deskripsi |
|--------|-------|-----------|-----------|
| GET | `/email/verify/{token}/{email}` | VerificationController@verify | Verifikasi email |

### Rute Login

| Method | Route | Controller | Deskripsi |
|--------|-------|-----------|-----------|
| GET | `/login/pilih` | LoginController@showPilih | Pilih tipe login |
| GET | `/login/login` | LoginController@showLogin | Form login penjual |
| POST | `/login/login` | LoginController@processLogin | Proses login penjual |
| GET | `/login/admin` | LoginController@showAdmin | Form login admin |
| POST | `/login/admin` | LoginController@processAdmin | Proses login admin |

### Rute Dashboard Penjual (Protected)

| Method | Route | Controller | Deskripsi |
|--------|-------|-----------|-----------|
| GET | `/seller/dashboard` | SellerController@dashboard | Dashboard penjual |
| GET | `/seller/products` | SellerController@listProducts | Daftar produk penjual |
| GET | `/seller/products/create` | SellerController@showCreateForm | Form buat produk |
| POST | `/seller/products/store` | SellerController@storeProduct | Simpan produk baru |
| PUT | `/seller/products/{id}` | SellerController@updateProduct | Update produk |
| DELETE | `/seller/products/{id}` | SellerController@deleteProduct | Hapus produk |
| GET | `/seller/reports` | ReportController@index | Laporan penjualan |
| GET | `/seller/reports/download` | ReportController@downloadPdf | Download laporan PDF |
| GET | `/seller/categories` | CategoryController@index | Daftar kategori |
| POST | `/seller/categories` | CategoryController@store | Buat kategori |
| PUT | `/seller/categories/{id}` | CategoryController@update | Update kategori |
| DELETE | `/seller/categories/{id}` | CategoryController@destroy | Hapus kategori |

---

## 🚀 Panduan Penggunaan

### Menjalankan Development Server

**Terminal 1 - PHP Server:**
```bash
php artisan serve
# Berjalan di http://localhost:8000
```

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
# Berjalan di http://localhost:5173
```

**Terminal 3 - Queue Listener:**
```bash
php artisan queue:listen --tries=1
```

**Terminal 4 - Log Viewer:**
```bash
php artisan pail --timeout=0
```

**Atau Jalankan Semua Sekaligus:**
```bash
composer run dev
```

### Membuat Fitur Baru

#### 1. Buat Migration
```bash
php artisan make:migration create_new_table_table
```

#### 2. Buat Model
```bash
php artisan make:model NewModel -m
```

#### 3. Buat Controller
```bash
php artisan make:controller NewModelController
```

#### 4. Buat Request (Form Validation)
```bash
php artisan make:request StoreNewModelRequest
```

#### 5. Register Route
Edit `routes/web.php` dan tambahkan route baru

### Testing
```bash
# Jalankan semua test
php artisan test

# Jalankan test spesifik
php artisan test tests/Feature/SomeFeatureTest.php

# Jalankan dengan coverage
php artisan test --coverage
```

### Membersihkan Cache
```bash
# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Atau sekali jalan
php artisan optimize:clear
```

### Membuat Seeder Custom
```bash
php artisan make:seeder CategorySeeder

# Jalankan seeder
php artisan db:seed --class=CategorySeeder
```

---

## 📦 Migrasi & Seeding

### Menjalankan Migration
```bash
# Jalankan semua pending migrations
php artisan migrate

# Rollback migration terbaru
php artisan migrate:rollback

# Rollback semua migrations
php artisan migrate:reset

# Refresh database
php artisan migrate:refresh

# Refresh dan seed
php artisan migrate:refresh --seed

# Jalankan fresh migration (drop all tables)
php artisan migrate:fresh
```

### Struktur Migration

**Create Users Table**
- email (unique)
- password (hashed)
- nama_toko, deskripsi
- Dokumen: file_ktp, foto_pic
- Status verifikasi: email_verified_at, status_akun

**Create Products Table**
- user_id (FK to users)
- category_id (FK to categories)
- name, description, price, stock
- condition (baru/bekas)
- min_order, rating, total_ulasan

**Create Categories Table**
- name, description
- Support parent category

**Create Reviews Table**
- user_id, product_id
- rating, review_text
- timestamps

**Create OTP Verifications Table**
- user_id
- otp_code, otp_type
- expires_at, verified_at

**Create User Documents Table**
- seller_id
- document_type, file_path
- verification_status

---

## 🐛 Troubleshooting

### 1. Error: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### 2. Error: "SQLSTATE[HY000] [2002] No such file or directory"
- Pastikan MySQL berjalan
- Check DB_HOST dan DB_PORT di `.env`

### 3. Error: "Class not found"
```bash
composer dump-autoload
# atau
composer install
```

### 4. Permission Denied pada storage
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Vite tidak compile CSS/JS
```bash
npm run dev
# atau untuk production
npm run build
```

### 6. Mail tidak terkirim
- Check konfigurasi di `.env` untuk MAIL_*
- Gunakan Mailtrap atau service email lain
- Test dengan: `php artisan tinker` → `Mail::send(...)`

### 7. Database migration failed
```bash
# Refresh semua (hati-hati, hapus semua data)
php artisan migrate:fresh

# atau rollback satu per satu
php artisan migrate:rollback --step=1
```

### 8. Upload file tidak berfungsi
```bash
# Create symbolic link untuk public storage
php artisan storage:link

# Check permissions
chmod -R 755 storage/app/public
```

### 9. Session not working
- Check SESSION_DRIVER di `.env` (gunakan database)
- Pastikan sessions table sudah dibuat: `php artisan migrate`

---

## 📝 Development Guidelines

### Code Style
```bash
# Format code dengan Pint
./vendor/bin/pint

# atau
php artisan pint
```

### Best Practices
1. **Model**: Gunakan accessors dan mutators untuk data transformation
2. **Controller**: Gunakan request classes untuk validation
3. **Validation**: Selalu validate input dari user
4. **Database**: Selalu gunakan migration untuk schema changes
5. **Security**: Hindari SQL injection, gunakan Eloquent ORM
6. **Performance**: Gunakan eager loading untuk relationships

### Naming Conventions
- **Table Names**: Plural snake_case (e.g., `products`, `user_documents`)
- **Model Names**: Singular PascalCase (e.g., `Product`, `UserDocument`)
- **Controller Names**: PascalCase + Controller (e.g., `ProductController`)
- **Migration Names**: Descriptive snake_case (e.g., `create_products_table`)
- **Route Names**: Dot-notation kebab-case (e.g., `product.index`, `seller.products.create`)

---

## 🔐 Security Considerations

1. **Environment Variables**: Jangan commit `.env` ke repository
2. **CSRF Protection**: Laravel otomatis melindungi POST requests
3. **CORS**: Configure di app.php jika ada API
4. **Password Hashing**: Selalu gunakan `bcrypt()` saat hash password
5. **Input Validation**: Validasi semua user input
6. **SQL Injection**: Gunakan prepared statements (Eloquent)
7. **XSS Protection**: Escape output dengan {{ }}
8. **Authentication**: Gunakan middleware `auth` untuk protected routes
9. **Rate Limiting**: Implementasi rate limiting untuk login routes

---

## 📊 Performance Tips

1. **Database Queries**: Gunakan select() untuk ambil field tertentu
2. **Eager Loading**: Gunakan with() untuk menghindari N+1 queries
3. **Caching**: Cache query results yang jarang berubah
4. **Pagination**: Selalu paginate hasil query besar
5. **Indexes**: Tambah index ke kolom yang sering di-query
6. **Asset Optimization**: Compress images, minify CSS/JS

---

## 🤝 Kontribusi

Kami menerima kontribusi! Silakan:

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 Lisensi

Project ini dilisensikan di bawah MIT License - lihat file [LICENSE](LICENSE) untuk details.

---

## 📞 Dukungan

Jika ada pertanyaan atau issue:
- Buat issue di GitHub Repository
- Email: support@quadmarket.local
- Discord: [QuadMarket Community](https://discord.gg/quadmarket)

---

## 📚 Dokumentasi Tambahan

- [Laravel Documentation](https://laravel.com/docs)
- [Vite Documentation](https://vitejs.dev/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Laravel Validation](https://laravel.com/docs/validation)

---

**Last Updated**: June 4, 2026  
**Version**: 1.0.0  
**Maintained By**: QuadMarket Team
