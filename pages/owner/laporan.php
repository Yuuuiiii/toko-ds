<?php 
// 1. Tentukan judul halaman sebelum memanggil header
$page_title = "Riwayat Keuangan & Rekonsiliasi";

// 2. Panggil Header Owner (Pastikan file base_owner.php sudah kamu pecah/rename jadi header_owner.php)
require_once '../../includes/header_owner.php'; 
?>

<div class="page-content">

  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-top">
        <span class="kpi-label">Total Pendapatan</span>
        <div class="kpi-icon neutral"><i data-lucide="trending-up"></i></div>
      </div>
      <div class="kpi-value" id="kpi-total">Rp 0</div>
      <div class="kpi-sub">
        <span class="kpi-trend up" id="kpi-total-trend">↑ 0%</span>
        vs periode sebelumnya
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-top">
        <span class="kpi-label">Kas Laci (Tunai)</span>
        <div class="kpi-icon success"><i data-lucide="banknote"></i></div>
      </div>
      <div class="kpi-value" id="kpi-tunai">Rp 0</div>
      <div class="kpi-sub">Pembayaran cash</div>
    </div>

    <div class="kpi-card">
      <div class="kpi-top">
        <span class="kpi-label">Non-Tunai</span>
        <div class="kpi-icon info"><i data-lucide="credit-card"></i></div>
      </div>
      <div class="kpi-value" id="kpi-nontunai">Rp 0</div>
      <div class="kpi-sub">QRIS + Debit</div>
    </div>

    <div class="kpi-card">
      <div class="kpi-top">
        <span class="kpi-label">Selisih Kas</span>
        <div class="kpi-icon danger"><i data-lucide="alert-triangle"></i></div>
      </div>
      <div class="kpi-value" id="kpi-selisih" style="color:var(--danger)">Rp 0</div>
      <div class="kpi-sub">Perlu rekonsiliasi</div>
    </div>
  </div>

  <div class="filter-row">
    <span class="filter-label">Periode</span>
    <button class="chip active" onclick="setFilter(this,'hari')">Hari Ini</button>
    <button class="chip" onclick="setFilter(this,'kemarin')">Kemarin</button>
    <button class="chip" onclick="setFilter(this,'7hari')">7 Hari Terakhir</button>
    <button class="chip" onclick="setFilter(this,'bulan')">Bulan Ini</button>
  </div>

  <div class="chart-row">
    <div class="chart-card">
      <div class="chart-card-header">
        <div>
          <div class="chart-card-title">Tren Penjualan</div>
          <div class="chart-card-sub">Total transaksi per hari</div>
        </div>
      </div>
      <div class="chart-wrap">
        <canvas id="chart-tren"></canvas>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-card-header">
        <div>
          <div class="chart-card-title">Metode Bayar</div>
          <div class="chart-card-sub">Distribusi pembayaran</div>
        </div>
      </div>
      <div class="chart-wrap">
        <canvas id="chart-metode"></canvas>
      </div>
    </div>
  </div>

  <div class="table-section">
    <div class="table-section-header">
      <span class="table-section-title">Riwayat Transaksi</span>
        <button onclick="window.print()" style="background: transparent; border: 1px solid var(--border); color: var(--text-secondary); padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s;">
            <i data-lucide="file-down" style="width: 18px; height: 18px;"></i> Export PDF
        </button>
    </div>
    <table class="data-table">
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Kasir</th>
          <th>Tipe Pembayaran</th>
          <th>No. Ref</th>
          <th>Nominal</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="table-body">
        <tbody id="table-body">
        <tr>
          <td class="td-time">2025-01-02 08:15</td>
          <td class="td-kasir">Dewi Lestari</td>
          <td><span class="badge badge-tunai">Tunai</span></td>
          <td class="td-time">—</td>
          <td class="td-nominal">Rp 38.000</td>
          <td><span class="badge badge-success">Selesai</span></td>
        </tr>
        </tbody>
        </tbody>
    </table>
  </div>
</div>

<?php 
// 4. Panggil Footer (Berisi penutup tag main, body, dan script global)
require_once '../../includes/footer.php'; 
?>

<script>
// Pastikan Chart.js sudah diload secara lokal di header_owner.php
// Letakkan logika spesifik chart di sini atau pisahkan ke file JS eksternal
const kpiData = {
  hari: { total: 369000, tunai: 251000, nontunai: 118000, selisih: 0 },
  kemarin: { total: 580000, tunai: 340000, nontunai: 240000, selisih: 5000 },
  '7hari': { total: 3250000, tunai: 1900000, nontunai: 1350000, selisih: 12000 },
  bulan: { total: 14500000, tunai: 8200000, nontunai: 6300000, selisih: 25000 },
};

function updateKPI(period) {
  const d = kpiData[period];
  document.getElementById('kpi-total').textContent = 'Rp ' + d.total.toLocaleString('id-ID');
  document.getElementById('kpi-tunai').textContent = 'Rp ' + d.tunai.toLocaleString('id-ID');
  document.getElementById('kpi-nontunai').textContent = 'Rp ' + d.nontunai.toLocaleString('id-ID');
  document.getElementById('kpi-selisih').textContent = 'Rp ' + d.selisih.toLocaleString('id-ID');
}

function setFilter(el, period) {
  document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
  updateKPI(period);
  // Tambahkan logika update chart di sini
}

// Inisialisasi awal
updateKPI('hari');
</script>