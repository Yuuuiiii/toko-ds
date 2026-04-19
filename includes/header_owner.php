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
        <div class="notif-btn">
          <i data-lucide="bell"></i>
          <span class="notif-badge">3</span>
        </div>
      </div>
    </div>