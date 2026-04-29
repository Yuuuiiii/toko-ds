# 📋 Test Plan — Sistem Kasir Toko DS

**Versi:** 1.0  
**Tanggal:** Mei 2026  
**Tim Penguji:** Kelompok 3 — Kelas B  

---

## 1. Tujuan Pengujian

Memastikan bahwa seluruh fitur fungsional sistem kasir Toko DS berjalan sesuai dengan dokumen SRS yang telah dibuat, mencakup:
- Kebenaran logika bisnis (transaksi, stok, laporan)
- Keamanan autentikasi dan hak akses
- Validasi input dan penanganan error
- Tampilan dan pengalaman pengguna (UI/UX)

---

## 2. Ruang Lingkup Pengujian

### ✅ Yang Diuji
| Modul | Fitur |
|-------|-------|
| Autentikasi | Login, logout, redirect per role, token JWT |
| Transaksi Kasir | Input barang, kalkulasi, metode bayar, cetak struk |
| Manajemen Barang | CRUD barang, stok, notifikasi stok menipis |
| Laporan | Dashboard owner, rekap harian/mingguan/bulanan |
| UI/UX | Responsivitas, dark/light mode, navigasi |

### ❌ Yang Tidak Diuji
- Integrasi printer fisik struk
- Mode offline/sinkronisasi (fitur hybrid)
- Load testing & performance testing skala besar

---

## 3. Strategi Pengujian

**Metode:** Black Box Testing (pengujian berbasis fungsionalitas)  
**Pendekatan:** Manual Testing oleh anggota tim  
**Lingkungan:** Localhost (XAMPP), Browser Chrome/Firefox

---

## 4. Jadwal Pengujian

| Fase | Aktivitas | Waktu |
|------|-----------|-------|
| Persiapan | Setup environment, import DB | H-3 UAS |
| Eksekusi | Jalankan semua test case | H-2 UAS |
| Evaluasi | Catat hasil, perbaiki bug | H-1 UAS |
| Final | Re-test bug yang diperbaiki | H UAS |

---

## 5. Pembagian Tugas Testing

| Modul | Penguji |
|-------|---------|
| Autentikasi | Muhammad Rofik Aprizani |
| Transaksi Kasir | Nandika Aditia |
| Manajemen Barang & Stok | Muhamad Rafly Affansyah |
| Laporan & Dashboard | Nandika Aditia |
| UI/UX | Nandika Aditia + Zaki Baharuna |

---

## 6. Kriteria Kelulusan (Pass/Fail Criteria)

### ✅ Sistem dinyatakan LULUS jika:
- Semua test case **High Priority** bernilai PASS
- Maksimal 20% test case **Medium Priority** bernilai FAIL
- Tidak ada bug kritikal yang menyebabkan sistem crash

### ❌ Sistem dinyatakan GAGAL jika:
- Ada test case High Priority yang FAIL
- Sistem crash saat transaksi berlangsung
- Data transaksi tidak tersimpan ke database

---

## 7. Prioritas Test Case

| Prioritas | Keterangan | Modul |
|-----------|------------|-------|
| 🔴 High | Wajib PASS, kritikal | Login, Transaksi, Stok |
| 🟡 Medium | Penting tapi tidak kritikal | Laporan, Validasi |
| 🟢 Low | Nice to have | UI detail, edge case |

---

## 8. Ringkasan Test Case

| File | Jumlah TC | Prioritas |
|------|-----------|-----------|
| TC_AUTH.md | 10 | High |
| TC_TRANSAKSI.md | 12 | High |
| TC_BARANG.md | 10 | High/Medium |
| TC_LAPORAN.md | 8 | Medium |
| TC_UI_UX.md | 8 | Low/Medium |
| **Total** | **48** | — |
