# 📊 Test Case — Laporan & Dashboard Owner

**Modul:** Dashboard, Laporan Keuangan, Filter Periode  
**File Terkait:** `pages/owner/dashboard.php`, `pages/owner/laporan.php`, `api/laporan.php`, `assets/js/laporan.js`  
**Prioritas:** 🟡 Medium

---

## TC-LAP-01 — Dashboard owner menampilkan KPI

| Field | Detail |
|-------|--------|
| **Deskripsi** | Halaman dashboard menampilkan 4 kartu KPI yang terisi data |
| **Prasyarat** | Login sebagai Owner/Admin, ada data transaksi di DB |
| **Langkah** | 1. Login sebagai Owner <br>2. Buka halaman Dashboard |
| **Ekspektasi** | KPI card Total Pendapatan, Kas Laci, Non-Tunai, Selisih Kas terisi angka |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-LAP-02 — Filter periode "Hari Ini" pada laporan

| Field | Detail |
|-------|--------|
| **Deskripsi** | Filter Hari Ini menampilkan data transaksi hari ini saja |
| **Prasyarat** | Ada transaksi yang dibuat hari ini |
| **Langkah** | 1. Buka halaman Riwayat Keuangan <br>2. Klik chip "Hari Ini" |
| **Ekspektasi** | Tabel & KPI hanya menampilkan data hari ini |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-LAP-03 — Filter periode "7 Hari Terakhir"

| Field | Detail |
|-------|--------|
| **Deskripsi** | Filter 7 Hari menampilkan data 7 hari ke belakang |
| **Langkah** | 1. Klik chip "7 Hari Terakhir" |
| **Ekspektasi** | Data berubah, chart tren menampilkan 7 titik data |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-LAP-04 — Filter periode "Bulan Ini"

| Field | Detail |
|-------|--------|
| **Deskripsi** | Filter Bulan Ini menampilkan rekap bulanan |
| **Langkah** | 1. Klik chip "Bulan Ini" |
| **Ekspektasi** | Total pendapatan menampilkan akumulasi bulan berjalan |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-LAP-05 — Chart tren penjualan tampil

| Field | Detail |
|-------|--------|
| **Deskripsi** | Grafik line chart tren penjualan berhasil di-render |
| **Langkah** | 1. Buka halaman Laporan |
| **Ekspektasi** | Line chart tampil dengan data, tidak error/blank |
| **Prioritas** | 🟢 Low |
| **Status** | ⏳ PENDING |
| **Catatan** | Menggunakan Chart.js |

---

## TC-LAP-06 — Chart distribusi metode bayar tampil

| Field | Detail |
|-------|--------|
| **Deskripsi** | Grafik donut metode pembayaran berhasil di-render |
| **Langkah** | 1. Buka halaman Laporan |
| **Ekspektasi** | Donut chart menampilkan proporsi Tunai/QRIS/Debit |
| **Prioritas** | 🟢 Low |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-LAP-07 — Tabel riwayat transaksi tampil dengan benar

| Field | Detail |
|-------|--------|
| **Deskripsi** | Tabel riwayat menampilkan data transaksi yang benar |
| **Langkah** | 1. Buka halaman Laporan <br>2. Cek tabel riwayat transaksi |
| **Ekspektasi** | Tabel terisi: waktu, nama kasir, metode bayar, nominal, status |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | Data dari query JOIN `penjualan` + `pengguna` |

---

## TC-LAP-08 — Tombol Export PDF tersedia

| Field | Detail |
|-------|--------|
| **Deskripsi** | Tombol Export PDF ada dan bisa diklik |
| **Langkah** | 1. Buka halaman Laporan <br>2. Klik "Export PDF" |
| **Ekspektasi** | Fungsi terpanggil (alert/download/response) |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | Fitur mungkin masih placeholder, catat kondisi aktual |
