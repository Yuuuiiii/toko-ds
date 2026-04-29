# 📡 Dokumentasi API — Toko DS

Semua endpoint API menggunakan format JSON. Base URL: `/UAS-RBPL/toko-ds/api/`

Autentikasi menggunakan **JWT Token** yang dikirim via header:
```
Authorization: Bearer <token>
```

---

## 🔐 Auth — `api/auth.php`

### POST — Login
```http
POST /api/auth.php
Content-Type: application/json
```

**Request Body:**
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Response Sukses:**
```json
{
  "status": "success",
  "token": "eyJ...",
  "user": {
    "id": 1,
    "nama": "Administrator",
    "role": "Admin"
  }
}
```

**Response Gagal:**
```json
{
  "status": "error",
  "message": "Username atau password salah"
}
```

---

## 🛒 Transaksi — `api/transaksi.php`

### POST — Buat Transaksi Baru
```http
POST /api/transaksi.php
```

**Request Body:**
```json
{
  "metode_bayar": "Tunai",
  "nominal_bayar": 50000,
  "items": [
    { "id_barang": 1, "qty": 2, "harga": 3500 },
    { "id_barang": 5, "qty": 1, "harga": 28000 }
  ]
}
```

### GET — Riwayat Transaksi
```http
GET /api/transaksi.php?periode=hari
GET /api/transaksi.php?periode=minggu
GET /api/transaksi.php?periode=bulan
```

---

## 📦 Barang — `api/barang.php`

### GET — Daftar Semua Barang
```http
GET /api/barang.php
GET /api/barang.php?search=indomie
GET /api/barang.php?kategori=2
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "ID_Barang": 1,
      "NamaBarang": "Indomie Goreng Original",
      "SKU": "BRG086",
      "HargaJual": 3500,
      "Stok": 120,
      "ID_Kategori": 1
    }
  ]
}
```

### POST — Tambah Barang
```http
POST /api/barang.php
```

### PUT — Edit Barang
```http
PUT /api/barang.php?id=1
```

### DELETE — Hapus Barang
```http
DELETE /api/barang.php?id=1
```

---

## 📊 Laporan — `api/laporan.php`

### GET — Laporan Penjualan
```http
GET /api/laporan.php?type=harian
GET /api/laporan.php?type=mingguan
GET /api/laporan.php?type=bulanan
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "total_pendapatan": 369000,
    "total_tunai": 251000,
    "total_nontunai": 118000,
    "jumlah_transaksi": 5,
    "riwayat": [...]
  }
}
```

---

## 📬 Notifikasi — `api/notifikasi.php`

### GET — Notifikasi Stok Menipis
```http
GET /api/notifikasi.php
```

**Response:**
```json
{
  "status": "success",
  "count": 3,
  "data": [
    {
      "NamaBarang": "Aqua 600ml",
      "Stok": 5,
      "StokMinimum": 10
    }
  ]
}
```

---

## 👤 Pengguna — `api/pengguna.php`

### GET — Daftar Pengguna
```http
GET /api/pengguna.php
```

### POST — Tambah Pengguna
```http
POST /api/pengguna.php
```

**Request Body:**
```json
{
  "nama": "Kasir Baru",
  "username": "kasir2",
  "password": "kasir123",
  "peran": "Kasir"
}
```

---

## 🚚 Supplier — `api/supplier.php`

### GET / POST / PUT / DELETE
```http
GET    /api/supplier.php
POST   /api/supplier.php
PUT    /api/supplier.php?id=1
DELETE /api/supplier.php?id=1
```

---

## ⏱️ Shift — `api/shift.php`

### POST — Mulai Shift
```http
POST /api/shift.php
Body: { "action": "start" }
```

### PUT — Akhiri Shift
```http
PUT /api/shift.php
Body: { "action": "end", "id_shift": 1 }
```

---

## 📥 Barang Masuk — `api/barang_masuk.php`

### POST — Catat Barang Masuk
```http
POST /api/barang_masuk.php
```

**Request Body:**
```json
{
  "id_supplier": 1,
  "items": [
    { "id_barang": 1, "qty": 50, "harga_beli": 2500 }
  ]
}
```
