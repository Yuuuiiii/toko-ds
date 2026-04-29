<?php
// MATIKAN CACHE BROWSER SECARA PAKSA!
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0"); // Masa kedaluwarsa 0 detik
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($page_title) ?> — Toko DS</title>
  <link rel="icon" type="image/webp" href="../../assets/img/logo.webp">
  <script>
    // 1. Cek keberadaan token & Ranjau BFCache (Tombol Back)
    const token = localStorage.getItem('jwt_token');
    if (!token) {
        window.location.replace('../../index.php'); // Tendang ke login
    }

    window.addEventListener('pageshow', function(event) {
        if (event.persisted || !localStorage.getItem('jwt_token')) {
            if (!localStorage.getItem('jwt_token')) {
                document.body.style.display = 'none'; // Sembunyikan konten
                window.location.replace('../../index.php');
            }
        }
    });

    // 2. Fungsi sederhana untuk membedah isi JWT (Base64 Decode)
    function parseJwt(token) {
        try {
            const base64Url = token.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
            return JSON.parse(jsonPayload);
        } catch(e) {
            return null;
        }
    }

    // 3. Cek masa kedaluwarsa (Expired) Token
    const userData = parseJwt(token);
    if (!userData || (userData.exp * 1000) < Date.now()) {
        alert('Sesi Anda telah berakhir, silakan login kembali.');
        localStorage.removeItem('jwt_token');
        window.location.replace('../../index.php');
    }
  </script>

  <script src="../../assets/js/lucide.min.js"></script>
  <link rel="stylesheet" href="../../assets/css/global.css" />
  <link rel="stylesheet" href="../../assets/css/kasir.css" />
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
      <div class="kasir-avatar" id="kasirAvatar">?</div>
      <div class="kasir-meta">
        <span class="kasir-role" id="kasirRole">Kasir</span>
        <span class="kasir-name" id="kasirName">Memuat...</span>
      </div>
    </div>

    <button class="btn-end-shift" id="btn-logout">
      <i data-lucide="log-out" class="icon-sm"></i>
      Akhiri Shift
    </button>
    
    <button class="btn-theme-toggle" onclick="toggleTheme()" title="Toggle tema">
      <i data-lucide="sun" id="theme-icon"></i>
    </button>
  </div>
</header>

<main class="main-body">