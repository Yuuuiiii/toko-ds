<?php
// MATIKAN CACHE BROWSER SECARA PAKSA!
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0"); // Masa kedaluwarsa 0 detik

$page_title = $page_title ?? 'Dashboard Gudang';
$current_page = $current_page ?? '';
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= htmlspecialchars($page_title) ?> — Toko DS</title>
  <link rel="icon" type="image/webp" href="../../assets/img/logo.webp">
  <script>
    // 1. Cek keberadaan token & Ranjau BFCache (Tombol Back)
    const token = localStorage.getItem('jwt_token');
    if (!token) {
        window.location.replace('../../index.php');
    }

    window.addEventListener('pageshow', function(event) {
        if (event.persisted || !localStorage.getItem('jwt_token')) {
            if (!localStorage.getItem('jwt_token')) {
                document.body.style.display = 'none'; 
                window.location.replace('../../index.php');
            }
        }
    });

    function parseJwt(token) {
        try {
            const base64Url = token.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            return JSON.parse(decodeURIComponent(window.atob(base64).split('').map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)).join('')));
        } catch(e) { return null; }
    }

    // PROTEKSI: KICK JIKA BUKAN GUDANG (ATAU ADMIN)
    const userData = parseJwt(token);
    if (!userData || (userData.exp * 1000) < Date.now() || (userData.role.toLowerCase() !== 'gudang' && userData.role.toLowerCase() !== 'admin')) {
        alert('Akses Ditolak! Anda bukan Staf Gudang.');
        localStorage.removeItem('jwt_token');
        window.location.replace('../../index.php');
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        if (theme === 'light') document.body.classList.add('light-mode');
        else document.body.classList.remove('light-mode');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('saved_theme') || 'dark';
        applyTheme(savedTheme);
        
        // --- LOGIKA TOMBOL TEMA (LIGHT/DARK MODE) ---
        const themeToggleBtn = document.getElementById('theme-toggle');

        if (themeToggleBtn) {
            // Kita buat fungsi khusus biar tombolnya dirender ulang dari awal setiap diklik
            const updateButtonUI = (theme) => {
                if (theme === 'light') {
                    themeToggleBtn.innerHTML = `<i data-lucide="moon" style="width: 16px; height: 16px;"></i>`;
                } else {
                    themeToggleBtn.innerHTML = `<i data-lucide="sun" style="width: 16px; height: 16px;"></i>`;
                }
                if (window.lucide) lucide.createIcons(); // Panggil Lucide untuk menyulap <i> yang baru
            };

            // Set ikon bawaan pas halaman baru dimuat
            updateButtonUI(savedTheme);

            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                applyTheme(newTheme);
                localStorage.setItem('saved_theme', newTheme);
                
                // Ubah ikon & teks pakai fungsi tadi
                updateButtonUI(newTheme);
            });
        }   

        // --- LOGIKA LOGOUT ---
        document.getElementById('btn-logout')?.addEventListener('click', function(e) {
            e.preventDefault();
            if(confirm('Yakin ingin keluar dari panel Gudang?')) {
                localStorage.removeItem('jwt_token');
                window.location.replace('../../index.php');
            }
        });
    });
  </script>

  <script src="../../assets/js/lucide.min.js"></script>
  <link rel="stylesheet" href="../../assets/css/global.css" />
  <link rel="stylesheet" href="../../assets/css/owner.css"/>

  <style>
      #btn-logout { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border); cursor: pointer; transition: all 0.3s ease; text-decoration: none; color: var(--text-secondary); }
      #btn-logout:hover { background-color: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; border-color: #ef4444 !important; }
      #btn-logout:hover .logout-icon { color: #ef4444 !important; }
  </style>
</head>
<body>

<div class="app-shell">
  <aside class="sidebar">
    <div class="sidebar-top">
      <div class="sidebar-logo">
        <span class="logo-text">TOKO<span class="logo-accent">DS</span></span>
        <span class="logo-sub" style="color: #10b981;">Warehouse</span>
      </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= $current_page === 'dashboard' ? 'active' : '' ?>">
          <i data-lucide="layout-dashboard"></i> <span>Dashboard Gudang</span>
        </a>
        <a href="stok.php" class="nav-item <?= $current_page === 'stok' ? 'active' : '' ?>">
          <i data-lucide="package"></i> <span>Kelola Stok & Barang</span>
        </a>
        <a href="riwayat_inbound.php" class="nav-item <?= $current_page === 'riwayat_inbound' ? 'active' : '' ?>">
          <i data-lucide="history"></i> <span>Riwayat Masuk</span>
        </a>
        <a href="kategori.php" class="nav-item <?= $current_page === 'kategori' ? 'active' : '' ?>">
          <i data-lucide="tags"></i> <span>Kategori Barang</span>
        </a>
        <a href="supplier.php" class="nav-item <?= $current_page === 'supplier' ? 'active' : '' ?>">
          <i data-lucide="truck"></i> <span>Data Supplier</span>
        </a>
      </nav>
      </nav>
    </div>

    <div class="sidebar-bottom">
      <div class="owner-profile">
        <div class="owner-avatar" id="ownerAvatar" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">?</div>
        <div class="owner-meta">
          <span class="owner-role" id="ownerRole" style="color: #10b981;">Staf Gudang</span>
          <span style="font-size: 14px; font-weight: 600; color: var(--text-primary);" id="ownerName">Memuat...</span>
        </div>
      </div>
      
      <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: auto;">
            <a href="#" id="btn-logout">
                <i data-lucide="log-out" class="logout-icon" style="width: 18px; height: 18px;"></i>
                <span style="font-size: 14px; font-weight: 600;">Log Out</span>
            </a>
      </div>
    </div>
  </aside>

  <main class="main-content">
    <div class="topbar" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--border);">
      <div class="topbar-left">
        <h1 class="page-title" style="margin: 0;"><?= htmlspecialchars($page_title) ?></h1>
      </div>
      <div class="topbar-right" style="display: flex; align-items: center; gap: 16px;">
        <button id="theme-toggle" style="background: var(--bg-surface); border: 1px solid var(--border); color: var(--text-secondary); cursor: pointer; padding: 8px 12px; border-radius: 8px; display: flex; align-items: center; gap: 8px; transition: 0.2s; font-weight: 600; font-size: 12px;">
            <i data-lucide="sun" id="theme-icon" style="width: 16px; height: 16px;"></i>
        </button>
      </div>
    </div>