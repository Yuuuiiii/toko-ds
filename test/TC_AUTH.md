# 🔐 Test Case — Autentikasi & Hak Akses

**Modul:** Login, Logout, JWT, Role-Based Access  
**File Terkait:** `index.php`, `api/auth.php`, `includes/auth.php`, `logout.php`  
**Prioritas:** 🔴 High

---

## TC-AUTH-01 — Login dengan kredensial valid (Role: Admin)

| Field | Detail |
|-------|--------|
| **Deskripsi** | Memastikan login berhasil dengan akun Admin yang valid |
| **Prasyarat** | Aplikasi berjalan, DB ter-import, akun admin tersedia |
| **Input** | Username: `admin` \| Password: `admin123` |
| **Langkah** | 1. Buka `http://localhost/UAS-RBPL/toko-ds` <br>2. Klik "Masuk Aplikasi" <br>3. Isi username & password <br>4. Klik "Masuk ke Sistem" |
| **Ekspektasi** | Redirect ke `pages/owner/dashboard.php` |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-AUTH-02 — Login dengan kredensial valid (Role: Kasir)

| Field | Detail |
|-------|--------|
| **Deskripsi** | Memastikan login berhasil dengan akun Kasir |
| **Input** | Username: `kasir1` \| Password: `kasir123` |
| **Langkah** | Sama seperti TC-AUTH-01 |
| **Ekspektasi** | Redirect ke `pages/kasir/transaksi.php` |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-AUTH-03 — Login dengan kredensial valid (Role: Gudang)

| Field | Detail |
|-------|--------|
| **Deskripsi** | Memastikan login berhasil dengan akun Gudang |
| **Input** | Username: `gudang1` \| Password: `gudang123` |
| **Langkah** | Sama seperti TC-AUTH-01 |
| **Ekspektasi** | Redirect ke `pages/gudang/dashboard.php` |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-AUTH-04 — Login dengan password salah

| Field | Detail |
|-------|--------|
| **Deskripsi** | Sistem harus menolak login dengan password yang salah |
| **Input** | Username: `admin` \| Password: `salah123` |
| **Ekspektasi** | Muncul pesan error: "Username atau password salah" |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-AUTH-05 — Login dengan username tidak terdaftar

| Field | Detail |
|-------|--------|
| **Deskripsi** | Sistem harus menolak username yang tidak ada di database |
| **Input** | Username: `hacker` \| Password: `apasaja` |
| **Ekspektasi** | Muncul pesan error: "Username atau password salah" |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-AUTH-06 — Login dengan field kosong

| Field | Detail |
|-------|--------|
| **Deskripsi** | Sistem harus menolak form login yang tidak diisi lengkap |
| **Input** | Username: *(kosong)* \| Password: *(kosong)* |
| **Ekspektasi** | Muncul pesan error atau field required browser |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-AUTH-07 — Logout berhasil

| Field | Detail |
|-------|--------|
| **Deskripsi** | Memastikan logout menghapus token dan redirect ke login |
| **Prasyarat** | Sudah login sebagai admin |
| **Langkah** | 1. Klik tombol "Log Out" di sidebar <br>2. Konfirmasi logout |
| **Ekspektasi** | Token JWT dihapus dari localStorage, redirect ke `index.php` |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-AUTH-08 — Akses halaman kasir tanpa login

| Field | Detail |
|-------|--------|
| **Deskripsi** | Halaman kasir tidak bisa diakses tanpa token JWT |
| **Langkah** | 1. Buka browser baru (tanpa login) <br>2. Akses langsung `pages/kasir/transaksi.php` |
| **Ekspektasi** | Redirect ke `index.php` (halaman login) |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-AUTH-09 — Kasir tidak bisa akses halaman owner

| Field | Detail |
|-------|--------|
| **Deskripsi** | Role Kasir tidak boleh mengakses halaman milik Owner |
| **Prasyarat** | Login sebagai Kasir |
| **Langkah** | 1. Login sebagai kasir <br>2. Akses langsung `pages/owner/dashboard.php` |
| **Ekspektasi** | Redirect ke halaman kasir atau muncul pesan "Akses ditolak" |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-AUTH-10 — Token JWT expired otomatis redirect

| Field | Detail |
|-------|--------|
| **Deskripsi** | Sistem harus redirect ke login jika token sudah kadaluarsa |
| **Langkah** | 1. Manipulasi `exp` di localStorage token menjadi waktu lampau <br>2. Reload halaman |
| **Ekspektasi** | Token dihapus, redirect ke `index.php` |
| **Status** | ⏳ PENDING |
| **Catatan** | Token valid 7 hari sesuai `api/auth.php` |
