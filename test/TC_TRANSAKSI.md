# 🛒 Test Case — Transaksi Kasir

**Modul:** Proses Transaksi, Kalkulasi, Metode Bayar  
**File Terkait:** `pages/kasir/transaksi.php`, `api/transaksi.php`, `assets/js/kasir.js`  
**Prioritas:** 🔴 High

---

## TC-TRX-01 — Tambah barang via pencarian nama

| Field | Detail |
|-------|--------|
| **Deskripsi** | Kasir bisa mencari dan menambah barang ke keranjang via input nama |
| **Prasyarat** | Login sebagai Kasir, minimal 1 barang ada di DB |
| **Langkah** | 1. Ketik nama barang di search bar (contoh: "Indomie") <br>2. Pilih barang dari hasil pencarian |
| **Ekspektasi** | Barang muncul di tabel transaksi dengan qty=1 |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-TRX-02 — Tambah barang via scan barcode

| Field | Detail |
|-------|--------|
| **Deskripsi** | Kasir bisa menambah barang dengan scan SKU barcode |
| **Langkah** | 1. Klik tombol "Scan Barcode" <br>2. Scan / ketik SKU barang (contoh: `BRG086`) <br>3. Tekan Enter |
| **Ekspektasi** | Barang dengan SKU tersebut masuk ke tabel transaksi |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-TRX-03 — Update qty barang di tabel

| Field | Detail |
|-------|--------|
| **Deskripsi** | Kasir bisa mengubah jumlah qty barang secara langsung |
| **Prasyarat** | Minimal 1 barang sudah ada di tabel transaksi |
| **Langkah** | 1. Klik input qty pada salah satu baris <br>2. Ubah nilainya (contoh: dari 1 menjadi 3) |
| **Ekspektasi** | Subtotal baris & total keseluruhan otomatis diperbarui |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-TRX-04 — Hapus barang dari tabel

| Field | Detail |
|-------|--------|
| **Deskripsi** | Kasir bisa menghapus barang dari daftar transaksi |
| **Langkah** | 1. Klik ikon 🗑️ pada baris barang yang ingin dihapus |
| **Ekspektasi** | Baris terhapus, total otomatis diperbarui |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-TRX-05 — Kalkulasi total belanja otomatis

| Field | Detail |
|-------|--------|
| **Deskripsi** | Total belanja dihitung otomatis dari semua item |
| **Langkah** | 1. Tambah 3 barang berbeda <br>2. Perhatikan nilai Total di panel kanan |
| **Ekspektasi** | Total = jumlah dari (qty × harga) semua item, selalu akurat |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-TRX-06 — Kalkulasi kembalian otomatis (Tunai)

| Field | Detail |
|-------|--------|
| **Deskripsi** | Kembalian dihitung otomatis saat metode bayar Tunai |
| **Prasyarat** | Ada item di keranjang, total = Rp 46.500 |
| **Langkah** | 1. Pilih metode "TUNAI" <br>2. Isi nominal bayar: `50000` |
| **Ekspektasi** | Kembalian = Rp 3.500 tampil di kotak hijau |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-TRX-07 — Kembalian negatif (uang kurang)

| Field | Detail |
|-------|--------|
| **Deskripsi** | Sistem harus menangani jika nominal bayar kurang dari total |
| **Prasyarat** | Total = Rp 46.500 |
| **Langkah** | 1. Pilih "TUNAI" <br>2. Isi nominal bayar: `10000` |
| **Ekspektasi** | Kembalian menampilkan nilai negatif / peringatan "uang kurang" |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-TRX-08 — Pilih metode bayar QRIS

| Field | Detail |
|-------|--------|
| **Deskripsi** | Panel berubah sesuai metode QRIS |
| **Langkah** | 1. Klik tombol "QRIS" |
| **Ekspektasi** | Panel tunai tersembunyi, muncul placeholder QR code |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-TRX-09 — Pilih metode bayar Debit/EDC

| Field | Detail |
|-------|--------|
| **Deskripsi** | Panel berubah sesuai metode Debit, muncul field ref EDC |
| **Langkah** | 1. Klik tombol "DEBIT" |
| **Ekspektasi** | Muncul field "No. Referensi EDC" dan "Nama Bank" |
| **Status** | ⏳ PENDING |
| **Catatan** | — |

---

## TC-TRX-10 — Selesaikan transaksi & simpan ke DB

| Field | Detail |
|-------|--------|
| **Deskripsi** | Transaksi berhasil disimpan ke database |
| **Prasyarat** | Ada item di keranjang, metode bayar dipilih |
| **Langkah** | 1. Klik "SELESAIKAN & CETAK STRUK" |
| **Ekspektasi** | Data tersimpan di tabel `penjualan` & `detail_penjualan`, stok berkurang |
| **Status** | ⏳ PENDING |
| **Catatan** | Verifikasi via phpMyAdmin |

---

## TC-TRX-11 — Transaksi dengan stok tidak mencukupi

| Field | Detail |
|-------|--------|
| **Deskripsi** | Sistem menolak transaksi jika stok barang habis |
| **Prasyarat** | Ada barang dengan stok = 0 |
| **Langkah** | 1. Tambah barang dengan stok = 0 ke keranjang <br>2. Klik selesaikan |
| **Ekspektasi** | Muncul pesan error: "Stok tidak mencukupi" |
| **Status** | ⏳ PENDING |
| **Catatan** | Sesuai logic rollback di `api/transaksi.php` |

---

## TC-TRX-12 — Batal semua item (Clear)

| Field | Detail |
|-------|--------|
| **Deskripsi** | Kasir bisa membatalkan semua item sekaligus |
| **Langkah** | 1. Tambah beberapa item <br>2. Klik "Batal Semua" <br>3. Konfirmasi |
| **Ekspektasi** | Semua item terhapus, total kembali ke Rp 0 |
| **Status** | ⏳ PENDING |
| **Catatan** | — |
