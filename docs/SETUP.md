# ⚙️ Panduan Setup Lokal — Toko DS

Panduan lengkap untuk menjalankan proyek Toko DS di komputer lokal menggunakan XAMPP.

---

## 📋 Prasyarat

Pastikan software berikut sudah terinstall:

| Software | Versi | Link |
|----------|-------|------|
| XAMPP | 8.x (PHP 8.x + MySQL + Apache) | [apachefriends.org](https://www.apachefriends.org) |
| Git | Latest | [git-scm.com](https://git-scm.com) |
| Browser | Chrome / Firefox / Edge | — |

---

## 🚀 Langkah Setup

### Step 1 — Clone Repository

Buka Git Bash / Terminal, lalu jalankan:

```bash
git clone https://github.com/username/toko-ds.git
```

### Step 2 — Pindahkan ke htdocs

Salin (atau pindahkan) folder `toko-ds` ke dalam direktori XAMPP:

```
C:/xampp/htdocs/UAS-RBPL/toko-ds
```

> Pastikan struktur path-nya: `htdocs/UAS-RBPL/toko-ds/index.php`

### Step 3 — Jalankan XAMPP

1. Buka **XAMPP Control Panel**
2. Klik **Start** pada **Apache**
3. Klik **Start** pada **MySQL**

### Step 4 — Import Database

1. Buka browser, akses: `http://localhost/phpmyadmin`
2. Klik **New** di sidebar kiri
3. Buat database baru dengan nama: `toko_ds`
4. Klik tab **Import**
5. Pilih file `toko_ds.sql` dari folder root proyek
6. Klik **Import** / **Go**

### Step 5 — Konfigurasi Database

Buka file `includes/db.php`, sesuaikan dengan konfigurasi MySQL kamu:

```php
$host     = "localhost";
$user     = "root";
$password = "";        // ← kosongkan jika tidak ada password
$database = "toko_ds";
```

### Step 6 — Konfigurasi BASE_URL

Buka file `includes/config.php`:

```php
define('BASE_URL', '/UAS-RBPL/toko-ds');
```

> Sesuaikan dengan path yang muncul di address bar browser-mu.

### Step 7 — Akses Aplikasi

Buka browser dan akses:

```
http://localhost/UAS-RBPL/toko-ds
```

---

## 🔑 Akun Login Default

| Role | Username | Password |
|------|----------|----------|
| Admin / Owner | `admin` | `admin123` |
| Kasir | `kasir1` | `kasir123` |
| Gudang | `gudang1` | `gudang123` |

---

## ❗ Troubleshooting

### Halaman tidak muncul / 404
- Pastikan Apache sudah **Start** di XAMPP
- Cek path folder sudah benar: `htdocs/UAS-RBPL/toko-ds`
- Cek `BASE_URL` di `config.php` sudah sesuai

### Koneksi database gagal
- Pastikan MySQL sudah **Start** di XAMPP
- Cek nama database sudah `toko_ds` (huruf kecil semua)
- Cek password di `db.php` sudah sesuai

### Login gagal / token error
- Buka DevTools browser (F12) → tab Console → cek pesan error
- Pastikan `api/auth.php` bisa diakses
- Coba clear localStorage browser: DevTools → Application → Local Storage → Clear

### CSS tidak ter-load
- Pastikan urutan import CSS di header sudah: `global.css` → `kasir.css` / `owner.css`
- Cek path file CSS di `includes/header_*.php` sudah sesuai `BASE_URL`
