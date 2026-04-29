# 📦 Test Case — Manajemen Barang & Stok

**Modul:** CRUD Barang, Stok, Notifikasi  
**File Terkait:** `pages/owner/stok.php`, `pages/gudang/barang.php`, `api/barang.php`, `api/notifikasi.php`  
**Prioritas:** 🔴 High / 🟡 Medium

---

## TC-BRG-01 — Tampilkan daftar semua barang

| Field | Detail |
|-------|--------|
| **Deskripsi** | Halaman stok menampilkan semua barang dari database |
| **Prasyarat** | Login sebagai Owner/Gudang, ada data barang di DB |
| **Langkah** | 1. Buka halaman Manajemen Stok |
| **Ekspektasi** | Tabel menampilkan semua barang dengan nama, SKU, harga, stok |
| **Prioritas** | 🔴 High |
| **Status** | ⏳ PENDING |
| **Catatan** | Data dari `v_laporan_stok` |

---

## TC-BRG-02 — Tambah barang baru (data lengkap)

| Field | Detail |
|-------|--------|
| **Deskripsi** | Owner/Gudang bisa menambah barang baru |
| **Langkah** | 1. Klik tombol "Tambah Barang" <br>2. Isi semua field (SKU, nama, kategori, harga, stok) <br>3. Klik Simpan |
| **Input** | SKU: `BRG999` \| Nama: `Test Produk` \| Harga: `5000` \| Stok: `50` |
| **Ekspektasi** | Barang baru muncul di daftar, stok tercatat = 50 |
| **Prioritas** | 🔴 High |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-BRG-03 — Tambah barang dengan SKU duplikat

| Field | Detail |
|-------|--------|
| **Deskripsi** | Sistem menolak SKU yang sudah dipakai |
| **Langkah** | 1. Tambah barang dengan SKU yang sudah ada (contoh: `BRG086`) |
| **Ekspektasi** | Muncul pesan error: "SKU belum pernah dipakai" / gagal simpan |
| **Prioritas** | 🔴 High |
| **Status** | ⏳ PENDING |
| **Catatan** | SKU bersifat UNIQUE di database |

---

## TC-BRG-04 — Edit data barang

| Field | Detail |
|-------|--------|
| **Deskripsi** | Owner bisa mengedit nama dan harga barang yang sudah ada |
| **Langkah** | 1. Klik tombol edit pada salah satu barang <br>2. Ubah nama/harga <br>3. Klik Simpan |
| **Ekspektasi** | Data barang diperbarui di tabel dan database |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-BRG-05 — Hapus barang yang belum pernah dijual

| Field | Detail |
|-------|--------|
| **Deskripsi** | Barang yang belum pernah dijual bisa dihapus |
| **Prasyarat** | Ada barang baru yang belum masuk transaksi |
| **Langkah** | 1. Klik tombol hapus pada barang baru <br>2. Konfirmasi hapus |
| **Ekspektasi** | Barang terhapus dari daftar dan database |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-BRG-06 — Hapus barang yang sudah pernah dijual

| Field | Detail |
|-------|--------|
| **Deskripsi** | Sistem menolak menghapus barang yang sudah ada di riwayat transaksi |
| **Prasyarat** | Ada barang yang sudah masuk tabel `detail_penjualan` |
| **Langkah** | 1. Klik hapus pada barang yang sudah dijual |
| **Ekspektasi** | Muncul pesan error, barang tidak terhapus |
| **Prioritas** | 🔴 High |
| **Status** | ⏳ PENDING |
| **Catatan** | Dilindungi oleh trigger `before_barang_delete` di DB |

---

## TC-BRG-07 — Notifikasi stok menipis muncul

| Field | Detail |
|-------|--------|
| **Deskripsi** | Notifikasi muncul saat stok barang di bawah minimum (< 10) |
| **Prasyarat** | Ada barang dengan stok < 10 |
| **Langkah** | 1. Login sebagai Owner/Gudang <br>2. Perhatikan ikon 🔔 di header |
| **Ekspektasi** | Badge notifikasi menampilkan jumlah barang kritis, list barang tampil |
| **Prioritas** | 🔴 High |
| **Status** | ⏳ PENDING |
| **Catatan** | Trigger otomatis dari DB |

---

## TC-BRG-08 — Pencatatan barang masuk (inbound)

| Field | Detail |
|-------|--------|
| **Deskripsi** | Stok bertambah setelah pencatatan barang masuk dari supplier |
| **Prasyarat** | Login sebagai Gudang |
| **Langkah** | 1. Buka halaman Barang Masuk <br>2. Pilih supplier & barang <br>3. Isi qty & harga beli <br>4. Simpan |
| **Ekspektasi** | Stok barang bertambah sesuai qty yang dimasukkan |
| **Prioritas** | 🔴 High |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-BRG-09 — Tambah barang dengan field kosong

| Field | Detail |
|-------|--------|
| **Deskripsi** | Sistem menolak submit form barang jika ada field wajib yang kosong |
| **Langkah** | 1. Buka form tambah barang <br>2. Biarkan nama/SKU kosong <br>3. Klik Simpan |
| **Ekspektasi** | Muncul validasi, form tidak tersubmit |
| **Prioritas** | 🟡 Medium |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-BRG-10 — Pencarian barang di halaman stok

| Field | Detail |
|-------|--------|
| **Deskripsi** | Filter/pencarian barang di halaman stok berfungsi |
| **Langkah** | 1. Ketik nama barang di search field |
| **Ekspektasi** | Tabel memfilter dan hanya menampilkan barang yang relevan |
| **Prioritas** | 🟢 Low |
| **Status** | ⏳ PENDING |
| **Catatan** | — |
