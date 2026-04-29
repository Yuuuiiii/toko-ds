# 🤝 Panduan Kontribusi — Toko DS

Dokumen ini menjelaskan standar pengerjaan proyek agar seluruh anggota tim bekerja secara konsisten.

---

## 🌿 Git Workflow

### Branch Strategy

```
main                    ← Branch utama (stable, siap demo)
├── feat/kasir-ui       ← Fitur UI kasir
├── feat/ui-design      ← Pengembangan desain
└── revisi/[nama]       ← Branch revisi personal
```

### Alur Kerja Harian

```bash
# 1. Selalu update dari main sebelum mulai kerja
git checkout main
git pull origin main

# 2. Buat branch baru untuk fitur yang dikerjakan
git checkout -b feat/nama-fitur

# 3. Kerjakan fitur, lalu commit secara rutin
git add .
git commit -m "feat: deskripsi singkat perubahan"

# 4. Push ke GitHub
git push origin feat/nama-fitur

# 5. Buat Pull Request di GitHub untuk di-review
```

---

## 📝 Konvensi Commit

Format: `tipe: deskripsi singkat`

| Tipe | Kapan digunakan |
|------|----------------|
| `feat` | Menambah fitur baru |
| `fix` | Memperbaiki bug |
| `style` | Perubahan CSS / tampilan |
| `refactor` | Refaktor kode tanpa ubah fungsi |
| `docs` | Update dokumentasi |
| `init` | Setup awal proyek |

**Contoh:**
```bash
git commit -m "feat: tambah halaman login dengan animasi"
git commit -m "fix: perbaiki kalkulasi kembalian tunai"
git commit -m "style: update warna sidebar owner"
git commit -m "docs: update README instalasi"
```

---

## 📁 Konvensi Penamaan File & Folder

| Jenis | Format | Contoh |
|-------|--------|--------|
| File PHP | `snake_case.php` | `barang_masuk.php` |
| File JS | `snake_case.js` | `kasir.js` |
| File CSS | `snake_case.css` | `global.css` |
| Folder | `snake_case` | `pages/gudang/` |
| Variable PHP | `$snake_case` | `$total_bayar` |
| Function JS | `camelCase` | `calcKembalian()` |
| Class CSS | `kebab-case` | `.btn-checkout` |

---

## 🗂️ Pembagian Tugas Tim

| Anggota | Fokus | File Utama |
|---------|-------|------------|
| Nandika Aditia | UI/UX, Frontend Kasir | `pages/kasir/`, `assets/css/`, `assets/js/kasir.js` |
| Zaki Baharuna | Laporan (Bab 2, 4, 5) | `docs/` |
| Muhammad Nur Robbany | Laporan (Bab 1, 3), Frontend | `docs/`, `pages/` |
| Muhamad Rafly Affansyah | Backend, Database | `api/`, `includes/db.php` |
| Muhammad Rofik Aprizani | Backend, Integrasi | `api/`, `includes/auth.php` |

---

## ✅ Checklist Sebelum Pull Request

- [ ] Kode sudah diuji dan tidak ada error
- [ ] Tidak ada file sensitif yang ikut di-commit (password, dll)
- [ ] Pesan commit mengikuti konvensi
- [ ] Tidak ada konflik dengan branch `main`
- [ ] Komentar kode ditambahkan pada bagian yang kompleks
