<?php
// session_start();
// if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['Peran'], ['Admin','Gudang'])) {
//     header('Location: /index.php');
//     exit;
// }
$owner_nama = "Siti Nurhaliza";
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Owner Dashboard — Toko DS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/toko-ds/assets/css/global.css" />
  <link rel="stylesheet" href="/toko-ds/assets/css/owner.css"/>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="app-shell">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-top">
      <div class="sidebar-logo">
        <span class="logo-text">TOKO<span class="logo-accent">DS</span></span>
        <span class="logo-sub">Management</span>
      </div>

      <nav class="sidebar-nav">
        <a href="/toko-ds/pages/owner/dashboard.php"
           class="nav-item <?= $current_page === 'dashboard' ? 'active' : '' ?>">
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard</span>
        </a>
        <a href="/toko-ds/pages/owner/stok.php"
           class="nav-item <?= $current_page === 'stok' ? 'active' : '' ?>">
          <i data-lucide="package"></i>
          <span>Manajemen Stok</span>
        </a>
        <a href="/toko-ds/pages/owner/laporan.php"
           class="nav-item <?= $current_page === 'laporan' ? 'active' : '' ?>">
          <i data-lucide="chart-column"></i>
          <span>Riwayat Keuangan</span>
        </a>
        <a href="/toko-ds/pages/owner/pengaturan.php"
           class="nav-item <?= $current_page === 'pengaturan' ? 'active' : '' ?>">
          <i data-lucide="settings"></i>
          <span>Pengaturan</span>
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
        <button class="btn-theme-toggle-side" onclick="toggleTheme()" title="Toggle tema">
          <i data-lucide="sun" id="theme-icon"></i>
        </button>
      </div>
      <a href="/index.php" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">
        <i data-lucide="log-out"></i>
        <span>Log Out</span>
      </a>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <!-- Top bar -->
    <div class="topbar">
      <div class="topbar-left">
        <h1 class="page-title" id="page-title">Riwayat Keuangan &amp; Rekonsiliasi</h1>
        <span class="page-date" id="page-date"></span>
      </div>
      <div class="topbar-right">
        <div class="notif-btn" title="Notifikasi stok">
          <i data-lucide="bell"></i>
          <span class="notif-badge">3</span>
        </div>
      </div>
    </div>