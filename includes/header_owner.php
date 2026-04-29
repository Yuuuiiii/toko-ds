<?php
// MATIKAN CACHE BROWSER SECARA PAKSA!
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0"); // Masa kedaluwarsa 0 detik

$page_title = $page_title ?? 'Dashboard';
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

    const userData = parseJwt(token);
    if (!userData || (userData.exp * 1000) < Date.now() || userData.role.toLowerCase() !== 'admin') {
        alert('Akses Ditolak! Sesi berakhir atau Anda bukan Admin.');
        localStorage.removeItem('jwt_token');
        window.location.replace('../../index.php');
    }

    // ==========================================
    // LOGIKA TEMA (DARK/LIGHT)
    // ==========================================
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        if (theme === 'light') document.body.classList.add('light-mode');
        else document.body.classList.remove('light-mode');
        updateThemeIcon(theme === 'light');
    }

    function updateThemeIcon(isLight) {
        const btn = document.getElementById('btnThemeToggleHeader');
        if (btn) {
            btn.innerHTML = `<i data-lucide="${isLight ? 'moon' : 'sun'}" style="width: 22px; height: 22px;"></i>`;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function toggleThemeHeader() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        localStorage.setItem('saved_theme', newTheme);
        applyTheme(newTheme);
    }

    // ==========================================
    // LOGIKA NOTIFIKASI REAL-TIME
    // ==========================================
    async function loadNotifications() {
        const badge = document.getElementById('notifBadge');
        const list = document.getElementById('notifList');

        try {
            const response = await fetch('../../api/notifikasi.php', { 
                method: 'GET',
                headers: { 'Authorization': `Bearer ${localStorage.getItem('jwt_token')}` }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }

            const res = await response.json();
            
            if (res.status === 'success') {
                if (res.unread > 0) {
                    badge.innerText = res.unread;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }

                list.innerHTML = '';
                if (res.data.length === 0) {
                    list.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-secondary); font-size: 13px;">Belum ada notifikasi stok.</div>';
                } else {
                    res.data.forEach(notif => {
                        let color = '#f59e0b'; 
                        if (notif.Status_Stok === 'HABIS') color = '#ef4444'; 
                        
                        list.innerHTML += `
                            <div style="padding: 16px; border-bottom: 1px solid var(--border); display: flex; gap: 12px; align-items: start; background: ${notif.Is_Read === 0 ? 'rgba(67, 97, 238, 0.05)' : 'transparent'};">
                                <div style="background: ${color}20; color: ${color}; padding: 8px; border-radius: 8px;">
                                    <i data-lucide="package-minus" style="width: 18px; height: 18px;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">${notif.Nama_Barang}</div>
                                    <div style="font-size: 12px; color: var(--text-secondary);">Sisa stok saat ini: <strong style="color: ${color};">${notif.Jumlah_Stok}</strong> (${notif.Status_Stok})</div>
                                    <div style="font-size: 11px; color: var(--text-secondary); margin-top: 6px; opacity: 0.7;">${new Date(notif.Tanggal_Notifikasi).toLocaleString('id-ID')}</div>
                                </div>
                            </div>
                        `;
                    });
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }
            } else {
                list.innerHTML = `<div style="padding: 20px; text-align: center; color: #ef4444; font-size: 13px;"><b>Gagal:</b> ${res.message}</div>`;
            }
        } catch (error) { 
            console.error('Error load notifikasi:', error); 
            list.innerHTML = `<div style="padding: 20px; text-align: center; color: #ef4444; font-size: 13px;"><b>Error Sistem:</b> Gagal menghubungi API. Silakan cek Inspect > Console.</div>`;
        }
    }

    async function markAsRead() {
        try {
            await fetch('../../api/notifikasi.php', { method: 'POST' });
            loadNotifications(); 
        } catch (error) { console.error('Error:', error); }
    }

    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notifDropdown');
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
            markAsRead(); 
        } else {
            dropdown.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('saved_theme') || 'dark';
        applyTheme(savedTheme);
        loadNotifications();
        
        document.addEventListener('click', function(event) {
            const btn = document.getElementById('btnNotifWrapper');
            const dropdown = document.getElementById('notifDropdown');
            if (btn && dropdown && !btn.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });

        // ==========================================
        // LOGIKA LOGOUT GLOBAL
        // ==========================================
        document.getElementById('btn-logout')?.addEventListener('click', function(e) {
            e.preventDefault(); 
            if(confirm('Yakin ingin mengakhiri sesi dan keluar?')) {
                localStorage.removeItem('jwt_token'); 
                window.location.replace('../../index.php'); 
            }
        });
    });
  </script>

  <script src="../../assets/js/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
  
  <link rel="stylesheet" href="../../assets/css/global.css" />
  <link rel="stylesheet" href="../../assets/css/owner.css"/>

  <style>
      .topbar-right { display: flex; align-items: center; gap: 16px; }
      .top-action-btn { background: transparent; border: none; color: var(--text-secondary, #94a3b8); cursor: pointer; transition: all 0.2s ease; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
      .top-action-btn:hover { color: white !important; background: rgba(255, 255, 255, 0.08); transform: scale(1.05); }

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
        <span class="logo-sub">Management</span>
      </div>

      <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= $current_page === 'dashboard' ? 'active' : '' ?>">
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard Utama</span>
        </a>
        <a href="stok.php" class="nav-item <?= $current_page === 'stok' ? 'active' : '' ?>">
          <i data-lucide="package"></i>
          <span>Kelola Stok & Barang</span>
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
        <a href="pelanggan.php" class="nav-item <?= $current_page === 'pelanggan' ? 'active' : '' ?>">
            <i data-lucide="users"></i>
            <span>Data Pelanggan</span>
        </a>

        <hr></hr>
        
        <a href="laporan.php" class="nav-item <?= $current_page === 'laporan' ? 'active' : '' ?>">
          <i data-lucide="file-spreadsheet"></i>
          <span>Laporan Keuangan</span>
        </a>
         <a href="pengguna.php" class="nav-item <?= $current_page === 'pengguna' ? 'active' : '' ?>">
          <i data-lucide="user-cog"></i>
          <span>Kelola Pengguna</span>
        </a>
      </nav>
    </div>

    <div class="sidebar-bottom">
      <div class="owner-profile">
        <div class="owner-avatar" id="ownerAvatar">?</div>
        <div class="owner-meta">
          <span class="owner-role" id="ownerRole">Admin</span>
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
    <div class="topbar">
      <div class="topbar-left">
        <h1 class="page-title"><?= htmlspecialchars($page_title) ?></h1>
      </div>
      
      <div class="topbar-right">
          
          <div style="position: relative;" id="btnNotifWrapper">
              <button id="btnNotif" class="top-action-btn" title="Notifikasi Stok" onclick="toggleNotifDropdown()" style="position: relative;">
                  <i data-lucide="bell" style="width: 22px; height: 22px;"></i>
                  <span id="notifBadge" style="display: none; position: absolute; top: 0px; right: 2px; background: #ef4444; color: white; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 20px; border: 2px solid var(--bg-body); align-items: center; justify-content: center;">0</span>
              </button>

              <div id="notifDropdown" style="display: none; position: absolute; top: 50px; right: 0; width: 320px; background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); z-index: 1000; overflow: hidden;">
                  <div style="padding: 16px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
                      <div style="font-size: 14px; font-weight: 700; color: var(--text-primary);">Notifikasi Stok</div>
                      <a href="stok.php" style="font-size: 12px; color: var(--accent); text-decoration: none; font-weight: 600;">Lihat Stok</a>
                  </div>
                  <div id="notifList" style="max-height: 300px; overflow-y: auto;">
                      <div style="padding: 20px; text-align: center; color: var(--text-secondary); font-size: 13px;">Memuat data...</div>
                  </div>
              </div>
          </div>

          <button id="btnThemeToggleHeader" class="top-action-btn" title="Ubah Tema" onclick="toggleThemeHeader()">
              <i data-lucide="sun" style="width: 22px; height: 22px;"></i>
          </button>
      </div>
    </div>