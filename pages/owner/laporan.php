<?php require_once '../../includes/base_owner.php'; ?>

    <!-- PAGE CONTENT -->
    <div class="page-content">

      <!-- KPI Cards -->
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

      <!-- Filter Chips -->
      <div class="filter-row">
        <span class="filter-label">Periode</span>
        <button class="chip active" onclick="setFilter(this,'hari')">Hari Ini</button>
        <button class="chip" onclick="setFilter(this,'kemarin')">Kemarin</button>
        <button class="chip" onclick="setFilter(this,'7hari')">7 Hari Terakhir</button>
        <button class="chip" onclick="setFilter(this,'bulan')">Bulan Ini</button>
      </div>

      <!-- Chart Row -->
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

      <!-- Data Table -->
      <div class="table-section">
        <div class="table-section-header">
          <span class="table-section-title">Riwayat Transaksi</span>
          <button class="btn-export" onclick="exportPDF()">
            <i data-lucide="file-down"></i>
            Export PDF
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
            <tr>
              <td class="td-time">2025-01-02 08:15</td>
              <td class="td-kasir">Dewi Lestari</td>
              <td><span class="badge badge-tunai">Tunai</span></td>
              <td class="td-time">—</td>
              <td class="td-nominal">Rp 38.000</td>
              <td><span class="badge badge-success">Selesai</span></td>
            </tr>
            <tr>
              <td class="td-time">2025-01-02 09:30</td>
              <td class="td-kasir">Dewi Lestari</td>
              <td><span class="badge badge-qris">QRIS</span></td>
              <td class="td-time">QR-20250102</td>
              <td class="td-nominal">Rp 50.000</td>
              <td><span class="badge badge-success">Selesai</span></td>
            </tr>
            <tr>
              <td class="td-time">2025-01-02 11:45</td>
              <td class="td-kasir">Dewi Lestari</td>
              <td><span class="badge badge-tunai">Tunai</span></td>
              <td class="td-time">—</td>
              <td class="td-nominal">Rp 69.500</td>
              <td><span class="badge badge-success">Selesai</span></td>
            </tr>
            <tr>
              <td class="td-time">2025-01-02 14:20</td>
              <td class="td-kasir">Dewi Lestari</td>
              <td><span class="badge badge-debit">Debit</span></td>
              <td class="td-time">4521</td>
              <td class="td-nominal">Rp 152.000</td>
              <td><span class="badge badge-success">Selesai</span></td>
            </tr>
            <tr>
              <td class="td-time">2025-01-02 16:10</td>
              <td class="td-kasir">Dewi Lestari</td>
              <td><span class="badge badge-tunai">Tunai</span></td>
              <td class="td-time">—</td>
              <td class="td-nominal">Rp 59.500</td>
              <td><span class="badge badge-success">Selesai</span></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div><!-- /page-content -->
  </main>
</div><!-- /app-shell -->

<?php require_once '../../includes/footer.php'; ?>

<script>
// ============ THEME ============
function toggleTheme() {
  const html = document.documentElement;
  const isDark = html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  document.getElementById('theme-icon').setAttribute('data-lucide', isDark ? 'moon' : 'sun');
  lucide.createIcons();
  updateChartTheme();
}

// ============ DATE ============
document.getElementById('page-date').textContent =
  new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

// ============ FORMAT ============
function formatRp(n) {
  return 'Rp ' + Number(n).toLocaleString('id-ID');
}

// ============ KPI DUMMY DATA ============
const kpiData = {
  hari:    { total: 369000,  tunai: 251000, nontunai: 118000, selisih: 0 },
  kemarin: { total: 580000,  tunai: 340000, nontunai: 240000, selisih: 5000 },
  '7hari': { total: 3250000, tunai: 1900000, nontunai: 1350000, selisih: 12000 },
  bulan:   { total: 14500000, tunai: 8200000, nontunai: 6300000, selisih: 25000 },
};

function updateKPI(period) {
  const d = kpiData[period];
  document.getElementById('kpi-total').textContent = formatRp(d.total);
  document.getElementById('kpi-tunai').textContent = formatRp(d.tunai);
  document.getElementById('kpi-nontunai').textContent = formatRp(d.nontunai);
  document.getElementById('kpi-selisih').textContent = formatRp(d.selisih);
}

// ============ FILTER ============
let currentFilter = 'hari';
function setFilter(el, period) {
  document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
  currentFilter = period;
  updateKPI(period);
  updateCharts(period);
}
updateKPI('hari');

// ============ CHARTS ============
const textMuted = () => getComputedStyle(document.documentElement).getPropertyValue('--text-muted').trim();
const borderColor = () => getComputedStyle(document.documentElement).getPropertyValue('--border').trim();

const trenData = {
  hari:    { labels: ['08:00','10:00','12:00','14:00','16:00','18:00'], data: [38000,50000,69500,152000,59500,0] },
  kemarin: { labels: ['08:00','10:00','12:00','14:00','16:00','18:00'], data: [50000,80000,120000,95000,140000,95000] },
  '7hari': { labels: ['Sen','Sel','Rab','Kam','Jum','Sab','Min'], data: [320000,450000,380000,520000,610000,780000,190000] },
  bulan:   { labels: ['Mg 1','Mg 2','Mg 3','Mg 4'], data: [3200000,3800000,4100000,3400000] },
};

const metodeData = {
  tunai: 251000, qris: 88000, debit: 30000
};

let chartTren, chartMetode;

function initCharts() {
  const accent = '#4f7bff';
  const ctxTren = document.getElementById('chart-tren').getContext('2d');
  chartTren = new Chart(ctxTren, {
    type: 'line',
    data: {
      labels: trenData.hari.labels,
      datasets: [{
        data: trenData.hari.data,
        borderColor: accent,
        backgroundColor: 'rgba(79,123,255,0.08)',
        borderWidth: 2,
        pointBackgroundColor: accent,
        pointRadius: 4,
        fill: true,
        tension: 0.4,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#555a72', font: { family: 'DM Mono', size: 11 } } },
        y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#555a72', font: { family: 'DM Mono', size: 11 }, callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k' } }
      }
    }
  });

  const ctxMetode = document.getElementById('chart-metode').getContext('2d');
  chartMetode = new Chart(ctxMetode, {
    type: 'doughnut',
    data: {
      labels: ['Tunai', 'QRIS', 'Debit'],
      datasets: [{
        data: [metodeData.tunai, metodeData.qris, metodeData.debit],
        backgroundColor: ['#22c87a', '#38bdf8', '#f0a500'],
        borderWidth: 0,
        hoverOffset: 6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: '#8b90a8', font: { family: 'DM Sans', size: 12 }, padding: 16, boxWidth: 10 }
        }
      }
    }
  });
}

function updateCharts(period) {
  const d = trenData[period];
  chartTren.data.labels = d.labels;
  chartTren.data.datasets[0].data = d.data;
  chartTren.update();
}

function updateChartTheme() {
  // chart colors update on theme change (simplified)
  chartTren.update();
  chartMetode.update();
}

// ============ EXPORT ============
function exportPDF() {
  alert('Fitur export PDF akan dihubungkan ke backend.');
}

// ============ INIT ============
initCharts();
lucide.createIcons();
</script>