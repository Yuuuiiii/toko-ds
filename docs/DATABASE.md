# 🗃️ Dokumentasi Database — Toko DS

Database: `toko_ds` | Engine: MySQL | Charset: `utf8mb4`

---

## 📊 Diagram Relasi (ERD Ringkas)

```
pengguna
    └── penjualan (via ID_Pengguna)
            └── detail_penjualan (via ID_Penjualan)
                    └── barang (via ID_Barang)
                            ├── stok_barang
                            ├── kategori
                            ├── satuan
                            └── notifikasi_stok (trigger otomatis)

pembelian (via ID_Pengguna + ID_Supplier)
    └── detail_pembelian (via ID_Pembelian)
            └── barang (via ID_Barang)

supplier
    └── pembelian

stok_opname
    └── detail_stok_opname
```

---

## 📋 Detail Tabel

### `pengguna`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| ID_Pengguna | INT (PK, AI) | ID unik pengguna |
| Nama | VARCHAR | Nama lengkap |
| Username | VARCHAR (UNIQUE) | Username login |
| Password | VARCHAR | Password (hashed) |
| Peran | ENUM | `Admin`, `Kasir`, `Gudang` |
| CreatedAt | TIMESTAMP | Waktu dibuat |

---

### `barang`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| ID_Barang | INT (PK, AI) | ID unik barang |
| SKU | VARCHAR (UNIQUE) | Kode unik produk |
| NamaBarang | VARCHAR | Nama produk |
| ID_Kategori | INT (FK) | Kategori barang |
| ID_Satuan | INT (FK) | Satuan barang |
| HargaBeli | DECIMAL | Harga beli dari supplier |
| HargaJual | DECIMAL | Harga jual ke pelanggan |
| StokMinimum | INT | Batas minimum stok (default: 10) |

---

### `stok_barang`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| ID_Stok | INT (PK, AI) | ID stok |
| ID_Barang | INT (FK) | Referensi barang |
| Jumlah | INT | Jumlah stok saat ini |
| LastUpdated | TIMESTAMP | Terakhir diperbarui |

> ⚡ Stok diperbarui otomatis via trigger setiap transaksi terjadi.

---

### `penjualan`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| ID_Penjualan | INT (PK, AI) | ID transaksi |
| ID_Pengguna | INT (FK) | Kasir yang bertugas |
| TanggalPenjualan | DATETIME | Waktu transaksi |
| TotalHarga | DECIMAL | Total belanja |
| MetodePembayaran | ENUM | `Tunai`, `QRIS`, `Debit` |
| NominalBayar | DECIMAL | Uang yang dibayarkan |
| Kembalian | DECIMAL | Uang kembalian |
| NomorReferensi | VARCHAR | No. ref EDC / QRIS |

---

### `detail_penjualan`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| ID_Detail | INT (PK, AI) | ID detail |
| ID_Penjualan | INT (FK) | Referensi transaksi |
| ID_Barang | INT (FK) | Referensi barang |
| Qty | INT | Jumlah barang dibeli |
| HargaSatuan | DECIMAL | Harga per satuan saat transaksi |
| Subtotal | DECIMAL | Qty × HargaSatuan |

---

### `pembelian`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| ID_Pembelian | INT (PK, AI) | ID pembelian |
| ID_Pengguna | INT (FK) | Petugas gudang |
| ID_Supplier | INT (FK) | Supplier barang |
| TanggalPembelian | DATETIME | Tanggal pembelian |
| TotalHarga | DECIMAL | Total nilai pembelian |
| NomorNota | VARCHAR | Nomor nota supplier |

---

### `notifikasi_stok`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| ID_Notifikasi | INT (PK, AI) | ID notifikasi |
| ID_Barang | INT (FK) | Barang yang stoknya menipis |
| Pesan | TEXT | Pesan notifikasi |
| TanggalNotifikasi | DATETIME | Waktu notifikasi |
| StatusBaca | TINYINT | 0 = belum dibaca, 1 = sudah |

> ⚡ Dibuat otomatis via **trigger** saat stok < StokMinimum.

---

## ⚡ Triggers

| Nama Trigger | Event | Fungsi |
|---|---|---|
| `after_barang_insert` | AFTER INSERT on `barang` | Auto buat record stok awal = 0 |
| `before_barang_delete` | BEFORE DELETE on `barang` | Cegah hapus jika sudah pernah dijual |
| `after_stok_update` | AFTER UPDATE on `stok_barang` | Auto buat notifikasi jika stok < minimum |

---

## 👁️ Views

| Nama View | Sumber Data | Kegunaan |
|-----------|-------------|----------|
| `v_penjualan_harian` | penjualan + pengguna | Rekap penjualan harian per kasir |
| `v_laporan_stok` | barang + stok_barang + kategori | Status stok lengkap per barang |
| `v_riwayat_pembelian` | pembelian + supplier + pengguna | Riwayat pembelian dari supplier |
| `v_detail_stok_opname` | stok_opname + detail + barang | Detail penyesuaian stok |

---

## 🔄 Cara Import Ulang Database

```bash
# Via phpMyAdmin:
# 1. Buat database toko_ds
# 2. Import > pilih toko_ds.sql

# Via command line:
mysql -u root -p toko_ds < toko_ds.sql
```
