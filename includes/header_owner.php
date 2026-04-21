<?php
// includes/header_owner.php
require_once __DIR__ . '/config.php';

// Bypass Auth untuk fase UI
$owner_nama = "Siti Nurhaliza";
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Judul dinamis
$page_title = isset($page_title) ? $page_title : 'Dashboard Owner';
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($page_title) ?> — Toko DS</title>
  <script src="<?= BASE_URL ?>/assets/js/lucide.min.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/chart.min.js"></script>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css" />
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/owner.css"/>
  backend-rofik
  <link rel="stylesheet" href="../../assets/css/owner.css">
=======
>>>> main
  
  <script src="<?= BASE_URL ?>/assets/js/lucide.min.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/chart.min.js"></script>
</head>
<body>

<div class="app-shell">
  <aside class="sidebar">
    <div class="sidebar-top">
      <div class="sidebar-logo">
        <span class="logo-text">TOKO<span class="logo-accent">DS</span></span>
        <span class="logo-sub">Management</span>
      </div>

      <nav class="sidebar-nav">
        <a href="<?= BASE_URL ?>/pages/owner/dashboard.php" class="nav-item <?= $current_page === 'dashboard' ? 'active' : '' ?>">
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard</span>
        </a>
        <a href="<?= BASE_URL ?>/pages/owner/stok.php" class="nav-item <?= $current_page === 'stok' ? 'active' : '' ?>">
          <i data-lucide="package"></i>
          <span>Manajemen Stok</span>
        </a>
        <a href="<?= BASE_URL ?>/pages/owner/laporan.php" class="nav-item <?= $current_page === 'laporan' ? 'active' : '' ?>">
          <i data-lucide="chart-column"></i>
          <span>Riwayat Keuangan</span>
        </a>
      </nav>
    </div>

    <div class="sidebar-bottom">
      <div class="owner-profile">
        <div class="owner-avatar"><?= strtoupper(substr($owner_nama, 0, 1)) ?></div>
        <div class="owner-meta">
          <span class="owner-role">Owner</span>
          <span class="owner-name"><?= htmlspecialchars($owner_nama) ?></span>
        </div>
        <button class="btn-theme-toggle-side" onclick="toggleTheme()">
          <i data-lucide="sun" id="theme-icon"></i>
        </button>
      </div>
      <a href="<?= BASE_URL ?>/index.php" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">
        <i data-lucide="log-out"></i>
        <span>Log Out</span>
      </a>
    </div>
  </aside>

  <main class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h1 class="page-title"><?= htmlspecialchars($page_title) ?></h1>
        <span class="page-date" id="page-date"></span>
      </div>
      <div class="topbar-right">
<div class="notification-wrapper" style="position: relative;">
    
    <button id="btnNotifToggle" style="background: transparent; border: 1px solid var(--border); padding: 8px; border-radius: 8px; cursor: pointer; color: var(--text-secondary); position: relative; display: flex; align-items: center; justify-content: center; transition: 0.2s;">
        <i data-lucide="bell" style="width: 20px; height: 20px;"></i>
        <span style="position: absolute; top: -4px; right: -4px; background: var(--danger); color: white; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 10px; border: 2px solid var(--bg-surface);">3</span>
    </button>

    <div id="dropdownNotif" style="display: none; position: absolute; top: 120%; right: 0; width: 320px; background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); z-index: 999; overflow: hidden;">
        
        <div style="padding: 16px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
            <h4 style="margin: 0; font-size: 14px; color: var(--text-primary); font-weight: 700;">Notifikasi</h4>
            <span style="font-size: 12px; color: var(--accent); cursor: pointer; font-weight: 600;">Tandai dibaca</span>
        </div>
        
        <div style="max-height: 300px; overflow-y: auto;">
            
            <div style="padding: 16px; border-bottom: 1px solid var(--border); display: flex; gap: 12px; background: rgba(255,255,255,0.03); cursor: pointer; transition: 0.2s;">
                <div style="width: 8px; height: 8px; background: var(--danger); border-radius: 50%; margin-top: 6px; flex-shrink: 0;"></div>
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 13px; color: var(--text-primary); font-weight: 600;">Stok Menipis!</p>
                    <p style="margin: 0; font-size: 12px; color: var(--text-muted); line-height: 1.4;">Beras Ramos 5kg tersisa 2 unit. Segera lakukan restock.</p>
                    <span style="font-size: 11px; color: var(--text-muted); margin-top: 6px; display: block;">10 menit yang lalu</span>
                </div>
            </div>

            <div style="padding: 16px; border-bottom: 1px solid var(--border); display: flex; gap: 12px; cursor: pointer; transition: 0.2s;">
                <div style="width: 8px; height: 8px; background: transparent; border-radius: 50%; margin-top: 6px; flex-shrink: 0;"></div>
                <div>
                    <p style="margin: 0 0 4px 0; font-size: 13px; color: var(--text-primary); font-weight: 600;">Nanti ini notif disesuaikan dengan yang ada di BE, dan notif juga ada di dashboard</p>
                    <p style="margin: 0; font-size: 12px; color: var(--text-muted); line-height: 1.4;">Nandika Aditia telah mengakhiri shift. Selisih kas: Rp 0.</p>
                    <span style="font-size: 11px; color: var(--text-muted); margin-top: 6px; display: block;">1 jam yang lalu</span>
                </div>
            </div>

        </div>
        
        <div style="padding: 12px; text-align: center; border-top: 1px solid var(--border); cursor: pointer; background: rgba(255,255,255,0.01);">
            <span style="font-size: 12px; color: var(--text-secondary); font-weight: 600;">Lihat Semua Aktivitas</span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnToggle = document.getElementById('btnNotifToggle');
    const dropdown = document.getElementById('dropdownNotif');

    if (btnToggle && dropdown) {
        // Klik lonceng untuk buka/tutup
        btnToggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Cegah event bocor ke document
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
            
            // Ubah warna ikon saat aktif
            if (!isVisible) {
                btnToggle.style.background = 'rgba(255,255,255,0.05)';
                btnToggle.style.color = 'var(--text-primary)';
            } else {
                btnToggle.style.background = 'transparent';
                btnToggle.style.color = 'var(--text-secondary)';
            }
        });

        // Klik di luar area untuk menutup
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== btnToggle) {
                dropdown.style.display = 'none';
                btnToggle.style.background = 'transparent';
                btnToggle.style.color = 'var(--text-secondary)';
            }
        });
    }
});
</script>
      </div>
    </div>