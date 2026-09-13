# 🏪 POS NET — Sistem Point of Sale Enterprise

Aplikasi **Point of Sale (POS)** berbasis web yang dibangun dengan **Laravel 13** & **PHP 8.5**, dirancang untuk kebutuhan bisnis **multi-cabang** dengan sistem manajemen stok, keuangan, laporan, dan hak akses berbasis peran (RBAC).

---

## ✨ Fitur Utama

### 🛒 Kasir (Point of Sale)
- Antarmuka kasir real-time dengan Alpine.js
- Pencarian produk by nama, SKU, atau barcode
- Filter produk berdasarkan kategori
- **Pilihan tipe harga** per item: Eceran, Reseller, Teknisi, Grosir, atau Harga Custom
- Diskon per transaksi
- Metode pembayaran: Tunai, Transfer Bank, QRIS, e-Wallet, Kredit/Bon
- Perhitungan kembalian otomatis untuk transaksi tunai
- Tampilan **Sisa Piutang** untuk transaksi kredit
- Cetak struk otomatis setelah transaksi berhasil

### 📦 Manajemen Produk & Stok
- Master data: Produk, Kategori, Brand
- Multi-harga per produk (Eceran, Reseller, Teknisi, Grosir)
- Stok berbasis cabang (`product_branches`)
- Kartu Stok & riwayat mutasi stok
- Pembelian barang dari Supplier
- Pembayaran pembelian bertahap (DP & pelunasan)

### 👥 Manajemen Pelanggan & Supplier
- Data pelanggan dengan tipe (Retail, Reseller, Teknisi, Grosir)
- Histori penjualan per pelanggan
- Data supplier & riwayat pembelian

### 💰 Keuangan
- **Buku Kas**: Pencatatan pemasukan & pengeluaran
- **Laporan Laba Rugi (P&L)**: Ringkasan keuntungan berdasarkan periode

### 📊 Laporan & Analitik
- **Laporan Penjualan**: Total penjualan, uang diterima, piutang belum tertagih
- **Laporan Pembelian**: Total pembelian, sudah dibayar, hutang kepada supplier
- **Laporan Aset Stok**: Nilai total aset stok berdasarkan harga modal

### 🏢 Fitur Enterprise (Multi-Cabang)
- **Multi-Cabang**: Stok, transaksi, dan laporan dipisah per cabang
- **Role-Based Access Control (RBAC)** via Spatie Laravel Permission:
  - `superadmin` – Akses penuh ke semua fitur & semua cabang
  - `admin_cabang` – Kelola produk, pembelian, laporan, & keuangan cabang sendiri
  - `kasir` – Hanya akses kasir POS & histori penjualan
- **Manajemen Karyawan**: CRUD user, penugasan ke cabang & pemberian role
- **Pengaturan Toko**: Nama perusahaan, alamat, telepon, logo, favicon, footer struk
- **Audit Log**: Rekaman aktivitas CRUD penting (siapa, kapan, cabang apa, IP address)

---

## 🛠️ Tech Stack

| Komponen | Detail |
|---|---|
| Framework | Laravel 13 |
| Bahasa | PHP 8.5 |
| Database | MySQL / SQLite |
| UI | Blade + Tailwind CSS |
| JS | Alpine.js |
| RBAC | Spatie Laravel Permission ^8.3 |
| Build Tool | Vite |

---

## 🚀 Cara Instalasi

### Prasyarat
- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL / MariaDB

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone <url-repo> pos_laravel
cd pos_laravel

# 2. Install dependensi PHP
composer install

# 3. Install dependensi JS
npm install && npm run build

# 4. Salin file konfigurasi
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Konfigurasi database di .env
# DB_DATABASE=nama_database
# DB_USERNAME=username
# DB_PASSWORD=password

# 7. Jalankan migrasi & seeder
php artisan migrate --seed

# 8. Buat symlink storage (untuk upload logo/favicon)
php artisan storage:link

# 9. Jalankan server
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

---

## 🔐 Akun Default

Setelah seeder dijalankan, gunakan akun berikut untuk login:

| Role | Email | Password |
|---|---|---|
| Superadmin | `admin@admin.com` | `password` |

> ⚠️ Ganti password setelah login pertama.

---

## 📁 Struktur Modul

```
app/Http/Controllers/
├── PosController.php          # Kasir & transaksi penjualan
├── SaleController.php         # Histori & pelunasan piutang penjualan
├── PurchaseController.php     # Pembelian & pelunasan hutang supplier
├── ProductController.php      # Manajemen produk & stok
├── StockTransactionController # Kartu stok & mutasi
├── FinanceController.php      # Buku kas & laba rugi
├── ReportController.php       # Laporan penjualan, pembelian, stok
├── UserController.php         # Manajemen karyawan (Superadmin)
├── SettingController.php      # Pengaturan toko (Superadmin)
├── AuditLogController.php     # Log aktivitas (Superadmin)
└── DashboardController.php    # Dashboard utama
```

---

## 📝 Lisensi

Aplikasi ini dikembangkan untuk kebutuhan internal bisnis. Hak cipta sepenuhnya milik pemilik proyek.
