# 🎨 Test Case — UI/UX & Tampilan

**Modul:** Responsivitas, Dark/Light Mode, Navigasi, Animasi  
**File Terkait:** `assets/css/global.css`, `assets/css/kasir.css`, `assets/css/owner.css`, `index.php`  
**Prioritas:** 🟢 Low / 🟡 Medium

---

## TC-UI-01 — Animasi landing page login

| Field | Detail |
|-------|--------|
| **Deskripsi** | Animasi blur + transisi form login berjalan mulus |
| **Langkah** | 1. Buka halaman login <br>2. Klik "Masuk Aplikasi" |
| **Ekspektasi** | Background blur, form login muncul dengan animasi slide-up |
| **Prioritas** | 🟢 Low |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-UI-02 — Toggle Dark/Light Mode — Kasir

| Field | Detail |
|-------|--------|
| **Deskripsi** | Tombol toggle mengubah tema dari dark ke light (dan sebaliknya) |
| **Prasyarat** | Login sebagai Kasir |
| **Langkah** | 1. Klik tombol ☀️ / 🌙 di header kasir |
| **Ekspektasi** | Seluruh halaman berubah tema, ikon berubah, warna konsisten |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-UI-03 — Toggle Dark/Light Mode — Owner

| Field | Detail |
|-------|--------|
| **Deskripsi** | Toggle tema pada halaman owner berfungsi |
| **Prasyarat** | Login sebagai Owner |
| **Langkah** | 1. Klik ikon toggle di sidebar owner |
| **Ekspektasi** | Tema berubah, sidebar + konten + chart ikut berubah |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-UI-04 — Navigasi sidebar owner aktif sesuai halaman

| Field | Detail |
|-------|--------|
| **Deskripsi** | Menu aktif di sidebar ter-highlight sesuai halaman yang dibuka |
| **Langkah** | 1. Buka halaman Laporan <br>2. Perhatikan sidebar |
| **Ekspektasi** | Menu "Riwayat Keuangan" ter-highlight (class `active`) |
| **Prioritas** | 🟢 Low |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-UI-05 — Layout kasir terminal tidak scroll

| Field | Detail |
|-------|--------|
| **Deskripsi** | Halaman kasir tidak perlu scroll — semua konten muat di layar |
| **Prasyarat** | Resolusi monitor 1920×1080 atau 1366×768 |
| **Langkah** | 1. Buka halaman kasir <br>2. Periksa apakah ada scrollbar |
| **Ekspektasi** | Tidak ada scrollbar vertikal di body utama |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | Sesuai requirement SRS: zero scrolling pada kasir |

---

## TC-UI-06 — Ikon Lucide tampil di semua halaman

| Field | Detail |
|-------|--------|
| **Deskripsi** | Semua ikon Lucide berhasil di-render dari file lokal |
| **Langkah** | 1. Buka semua halaman utama <br>2. Periksa ikon di header, sidebar, tombol |
| **Ekspektasi** | Semua ikon tampil, tidak ada ikon yang broken/kosong |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | Lucide diload dari `assets/js/lucide.min.js` (lokal) |

---

## TC-UI-07 — Zebra striping tabel kasir

| Field | Detail |
|-------|--------|
| **Deskripsi** | Baris tabel transaksi bergantian warna (odd/even) |
| **Langkah** | 1. Tambah 4+ item di kasir <br>2. Perhatikan warna baris |
| **Ekspektasi** | Baris ganjil dan genap memiliki background berbeda |
| **Prioritas** | 🟢 Low |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-UI-08 — Tombol scan barcode animasi aktif

| Field | Detail |
|-------|--------|
| **Deskripsi** | Tombol Scan Barcode berubah tampilan saat mode scan aktif |
| **Langkah** | 1. Klik "Scan Barcode" <br>2. Perhatikan tombol |
| **Ekspektasi** | Tombol berubah warna hijau + animasi pulse + teks "Stop Scan" |
| **Prioritas** | 🟢 Low |
| **Status** | ⏳ PENDING |
| **Catatan** | — |
