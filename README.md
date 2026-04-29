# 🛒 Sistem Kasir Toko DS

> Aplikasi Point of Sale (POS) berbasis web untuk Toko DS — dikembangkan sebagai Tugas Besar UAS mata kuliah Rancang Bangun Perangkat Lunak, Program Studi Sistem Informasi, Universitas Siliwangi.

---

## 👥 Tim Pengembang — Kelompok 3

| Nama | NIM | Peran |
|------|-----|-------|
| Nandika Aditia | 247007111032 | Frontend Developer |
| Zaki Baharuna Ilmi Hdiana | 247007111041 | Documentation |
| Muhammad Nur Robbany | 247007111046 | Documentation |
| Muhamad Rafly Affansyah | 247007111049 | Backend Developer |
| Muhammad Rofik Aprizani | 247007111057 | Backend Developer |

---

## 📋 Deskripsi Proyek

Sistem Kasir Toko DS adalah aplikasi POS berbasis web yang dirancang untuk menggantikan sistem pencatatan manual pada Toko DS. Sistem ini mendukung operasional kasir harian, manajemen stok, pemantauan laporan keuangan, serta pengelolaan data toko secara terpusat dengan tiga role pengguna yang berbeda.

### Permasalahan yang Diselesaikan
- ❌ Kesalahan dalam perhitungan transaksi manual
- ❌ Proses transaksi yang lambat dan tidak efisien
- ❌ Pencatatan stok dan keuangan yang tidak akurat

---

## ✨ Fitur Utama

### 🧾 Kasir
- Transaksi penjualan real-time dengan input manual atau scan barcode
- Dukungan 3 metode pembayaran: **Tunai**, **QRIS**, dan **Debit/EDC**
- Kalkulasi kembalian otomatis untuk pembayaran tunai
- Cetak struk transaksi

### 📦 Gudang
- Manajemen data barang (tambah, edit, hapus)
- Pencatatan barang masuk dari supplier
- Monitoring stok dengan notifikasi stok menipis
- Manajemen kategori, satuan, dan supplier
- Riwayat inbound barang

### 📊 Owner / Admin
- Dashboard ringkasan performa toko
- Laporan penjualan harian, mingguan, dan bulanan
- Rekonsiliasi kas (tunai vs non-tunai)
- Manajemen pengguna dan hak akses
- Data pelanggan dan supplier
- Manajemen stok dan barang

### 🔐 Sistem
- Autentikasi berbasis **JWT Token**
- Role-based access control (Kasir, Gudang, Admin/Owner)
- Dark mode & Light mode
- Redirect otomatis berdasarkan role setelah login

---

## 🗂️ Struktur Proyek

```
toko-ds/
├── api/                        # Backend API (PHP, return JSON)
│   ├── auth.php                # Login & JWT
│   ├── barang.php              # CRUD barang
│   ├── barang_masuk.php        # Pencatatan barang masuk
│   ├── kategori.php            # Manajemen kategori
│   ├── laporan.php             # Laporan penjualan
│   ├── notifikasi.php          # Notifikasi stok menipis
│   ├── pelanggan.php           # Data pelanggan
│   ├── pengeluaran.php         # Data pengeluaran
│   ├── pengguna.php            # Manajemen user
│   ├── satuan.php              # Manajemen satuan
│   ├── shift.php               # Manajemen shift kasir
│   ├── supplier.php            # Data supplier
│   └── transaksi.php           # Proses transaksi
│
├── assets/
│   ├── css/
│   │   ├── global.css          # Shared styles & CSS variables
│   │   ├── kasir.css           # Styles khusus halaman kasir
│   │   └── owner.css           # Styles khusus halaman owner/gudang
│   ├── fonts/                  # Font Inter (lokal)
│   ├── img/                    # Aset gambar (logo, bg-login)
│   └── js/
│       ├── kasir.js            # Logika transaksi kasir
│       ├── owner.js            # Logika dashboard owner
│       ├── laporan.js          # Chart & filter laporan
│       ├── barang.js           # CRUD barang
│       ├── barang_masuk.js     # Form barang masuk
│       ├── stok.js             # Monitoring stok
│       ├── supplier.js         # Data supplier
│       ├── kategori.js         # Data kategori
│       ├── pelanggan.js        # Data pelanggan
│       ├── pengguna.js         # Manajemen user
│       ├── riwayat_inbound.js  # Riwayat barang masuk
│       ├── chart.js            # Konfigurasi Chart.js
│       └── lucide.min.js       # Icon library (lokal)
│
├── includes/
│   ├── config.php              # Konfigurasi BASE_URL
│   ├── db.php                  # Koneksi database MySQL
│   ├── auth.php                # Helper autentikasi
│   ├── header.php              # Base template umum
│   ├── header_kasir.php        # Header khusus kasir
│   ├── header_owner.php        # Header & sidebar owner
│   ├── header_gudang.php       # Header & sidebar gudang
│   └── footer.php              # Penutup HTML
│
├── pages/
│   ├── kasir/
│   │   └── transaksi.php       # Halaman terminal kasir
│   ├── owner/
│   │   ├── dashboard.php       # Dashboard owner
│   │   ├── laporan.php         # Laporan keuangan
│   │   ├── stok.php            # Monitoring stok
│   │   ├── tambah_barang.php   # Form tambah barang
│   │   ├── edit_barang.php     # Form edit barang
│   │   ├── kategori.php        # Manajemen kategori
│   │   ├── pelanggan.php       # Data pelanggan
│   │   ├── pengguna.php        # Manajemen pengguna
│   │   ├── supplier.php        # Data supplier
│   │   └── riwayat_inbound.php # Riwayat barang masuk
│   └── gudang/
│       ├── dashboard.php       # Dashboard gudang
│       ├── barang.php          # Daftar barang
│       ├── barang_masuk.php    # Input barang masuk
│       ├── stok.php            # Status stok
│       ├── kategori.php        # Kategori barang
│       ├── pembelian.php       # Data pembelian
│       ├── supplier.php        # Data supplier
│       └── riwayat_inbound.php # Riwayat inbound
│
├── index.php                   # Halaman login
├── logout.php                  # Proses logout
├── .htaccess                   # Konfigurasi Apache
└── README.md
```

---

## 🛠️ Teknologi yang Digunakan

| Kategori | Teknologi |
|----------|-----------|
| Frontend | HTML5, CSS3, JavaScript (Vanilla) |
| Backend | PHP 8.x |
| Database | MySQL |
| Auth | JWT (JSON Web Token) |
| Icon | Lucide Icons |
| Chart | Chart.js |
| Server | Apache (XAMPP) |
| Version Control | Git & GitHub |

---

## ⚙️ Cara Instalasi & Menjalankan

### Prasyarat
- [XAMPP](https://www.apachefriends.org/) (PHP 8.x + MySQL + Apache)
- Git

### Langkah Instalasi

**1. Clone repository**
```bash
git clone https://github.com/username/toko-ds.git
```

**2. Pindahkan ke folder htdocs**
```
Salin folder toko-ds ke: C:/xampp/htdocs/UAS-RBPL/
```

**3. Import database**
- Buka `http://localhost/phpmyadmin`
- Buat database baru bernama `toko_ds`
- Import file `toko_ds.sql` dari folder root proyek

**4. Konfigurasi koneksi database**

Edit file `includes/db.php`:
```php
$host     = "localhost";
$user     = "root";
$password = ""; // sesuaikan password MySQL kamu
$database = "toko_ds";
```

**5. Konfigurasi BASE_URL**

Edit file `includes/config.php`:
```php
define('BASE_URL', '/UAS-RBPL/toko-ds');
```
> Sesuaikan dengan path di address bar browser kamu.

**6. Jalankan aplikasi**
- Pastikan Apache & MySQL di XAMPP sudah **Start**
- Buka browser dan akses: `http://localhost/UAS-RBPL/toko-ds`

---

## 🗃️ Skema Database

Database `toko_ds` terdiri dari tabel-tabel berikut:

| Tabel | Fungsi |
|-------|--------|
| `pengguna` | Data user & role (Admin, Kasir, Gudang) |
| `barang` | Master data produk |
| `kategori` | Kategori barang |
| `satuan` | Satuan barang (pcs, kg, dll) |
| `stok_barang` | Jumlah stok real-time |
| `penjualan` | Header transaksi penjualan |
| `detail_penjualan` | Detail item per transaksi |
| `pembelian` | Pembelian dari supplier |
| `detail_pembelian` | Detail item pembelian |
| `supplier` | Data supplier |
| `notifikasi_stok` | Auto-notifikasi stok < 10 |
| `stok_opname` | Penyesuaian stok manual |
| `detail_stok_opname` | Detail stok opname |

**Views yang tersedia:**
- `v_penjualan_harian` — Rekap penjualan harian
- `v_laporan_stok` — Laporan status stok
- `v_riwayat_pembelian` — Riwayat pembelian
- `v_detail_stok_opname` — Detail stok opname

---

**Konvensi commit:**
```
feat: tambah fitur baru
fix: perbaiki bug
style: perubahan tampilan/CSS
refactor: refaktor kode
docs: update dokumentasi
```

---

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan akademik — Tugas Besar UAS Semester Genap 2025/2026.  
**Program Studi S1 Sistem Informasi, Fakultas Teknik, Universitas Siliwangi.**

---

<p align="center">Dibuat dengan ❤️ oleh Kelompok 3 — Kelas B</p>
