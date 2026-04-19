<?php
// includes/header_kasir.php
require_once __DIR__ . '/config.php';

// Bypass Auth untuk fase UI (Nanti diganti SESSION oleh Backend)
$kasir_nama = "Nandika Aditia";
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kasir Terminal — Toko DS</title>
  
  <script src="<?= BASE_URL ?>/assets/js/lucide.min.js"></script>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css" />
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/kasir.css" />
</head>
<body>

<header class="top-header">
  <div class="header-left">
    <span class="logo-text">TOKO<span class="logo-accent">DS</span></span>
    <div class="divider-v"></div>
    <div class="clock-wrap">
      <i data-lucide="clock" class="icon-sm"></i>
      <span id="live-clock">--:--:--</span>
      <span id="live-date">---</span>
    </div>
  </div>

  <div class="header-right">
    <div class="shift-info">
      <span class="shift-label">SHIFT AKTIF</span>
      <span class="shift-time" id="shift-start">08:00</span>
    </div>
    
    <div class="divider-v"></div>

    <div class="kasir-info">
      <div class="kasir-avatar"><?= strtoupper(substr($kasir_nama, 0, 1)) ?></div>
      <div class="kasir-meta">
        <span class="kasir-role">Kasir</span>
        <span class="kasir-name"><?= htmlspecialchars($kasir_nama) ?></span>
      </div>
    </div>

    <button class="btn-end-shift" onclick="confirmEndShift()">
      <i data-lucide="log-out" class="icon-sm"></i>
      Akhiri Shift
    </button>
    
    <button class="btn-theme-toggle" onclick="toggleTheme()" title="Toggle tema">
      <i data-lucide="sun" id="theme-icon"></i>
    </button>
  </div>
</header>

<main class="main-body">