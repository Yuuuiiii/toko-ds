<?php 
$page_title = "Dashboard Utama";
$current_page = "dashboard";
require_once '../../includes/header_owner.php'; 
?>

<style>
  /* 1. Paksa area kanan untuk bisa di-scroll */
  .main-content {
    height: 100vh !important;
    overflow-y: auto !important; 
    overflow-x: hidden !important;
  }
  
  /* 2. JURUS KUNCI: Kunci Navbar agar menempel di atas layar */
  .topbar {
    position: sticky !important;
    top: 0 !important;
    z-index: 999 !important;
    /* Beri background warna solid agar saat konten di-scroll, tidak tembus pandang/nabrak tulisan */
  }

  /* 3. Lepaskan batasan page-content */
  .page-content {
    display: block !important;
    height: auto !important;
    overflow: visible !important;
    padding-bottom: 40px !important; 
  }
</style>

<div class="page-content">

  <div class="kpi-grid" style="margin-bottom: 24px;">
    <div class="kpi-card">
      <div class="kpi-top">
        <span class="kpi-label">Pendapatan Hari Ini</span>
        <div class="kpi-icon success"><i data-lucide="banknote"></i></div>
      </div>
      <div class="kpi-value" id="kpi-pendapatan-hari">Rp 0</div>
      <div class="kpi-sub">Total transaksi hari ini</div>
    </div>

    <div class="kpi-card">
      <div class="kpi-top">
        <span class="kpi-label">Transaksi Hari Ini</span>
        <div class="kpi-icon info"><i data-lucide="shopping-cart"></i></div>
      </div>
      <div class="kpi-value" id="kpi-transaksi-hari">0</div>
      <div class="kpi-sub">Struk tercetak hari ini</div>
    </div>

    <div class="kpi-card">
      <div class="kpi-top">
        <span class="kpi-label">Pendapatan Bulan Ini</span>
        <div class="kpi-icon neutral"><i data-lucide="trending-up"></i></div>
      </div>
      <div class="kpi-value" id="kpi-pendapatan-bulan">Rp 0</div>
      <div class="kpi-sub">Akumulasi bulan berjalan</div>
    </div>

    <div class="kpi-card">
      <div class="kpi-top">
        <span class="kpi-label">Aset Inventaris</span>
        <div class="kpi-icon danger"><i data-lucide="package"></i></div>
      </div>
      <div class="kpi-value" id="kpi-aset">Rp 0</div>
      <div class="kpi-sub">Total nilai modal barang</div>
    </div>
  </div>

  <div class="chart-row" style="margin-bottom: 24px;">
    <div class="chart-card">
      <div class="chart-card-header">
        <div class="chart-card-title">Tren Penjualan (7 Hari Terakhir)</div>
      </div>
      <div class="chart-wrap">
        <canvas id="chartTren"></canvas>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-card-header">
        <div class="chart-card-title">Metode Bayar (7 Hari Terakhir)</div>
      </div>
      <div class="chart-wrap">
        <canvas id="chartMetode"></canvas>
      </div>
    </div>
  </div>

  <div class="table-section">
    <div class="table-section-header">
      <span class="table-section-title">Riwayat Transaksi Mingguan</span>
    </div>
    
    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden;">
        <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
          <thead style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border);">
            <tr>
              <th style="padding: 16px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Tanggal</th>
              <th style="padding: 16px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Kasir</th>
              <th style="padding: 16px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Metode Bayar</th>
              <th style="padding: 16px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Jumlah Trx</th>
              <th style="padding: 16px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Total Penjualan</th>
            </tr>
          </thead>
          <tbody id="table-body">
            <tr><td colspan="5" style="text-align:center; padding:40px; color: var(--text-secondary);">Memuat data transaksi...</td></tr>
          </tbody>
        </table>
    </div>
  </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
<script src="../../assets/js/owner.js?v=<?= time() ?>"></script>