// Ambil token dari localStorage
const getAuthToken = () => localStorage.getItem('jwt_token');

// Fungsi Format Uang
function formatRupiah(angka) {
    if (!angka) return 'Rp 0';
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

// Konfigurasi Warna Tema untuk Chart
const rootStyles = getComputedStyle(document.documentElement);
const textColor = rootStyles.getPropertyValue('--text-secondary').trim() || '#888';
const gridColor = rootStyles.getPropertyValue('--border').trim() || '#333';
const accentColor = rootStyles.getPropertyValue('--accent').trim() || '#4361ee';
const successColor = rootStyles.getPropertyValue('--success').trim() || '#22c55e';

// Variabel Global untuk Chart Instance agar bisa di-destroy jika diupdate
let chartTrenInstance = null;
let chartMetodeInstance = null;

document.addEventListener('DOMContentLoaded', async function() {
    
    // 1. Tampilkan Profil Admin dari JWT
    if (typeof parseJwt === "function") {
        const jwtData = parseJwt(getAuthToken());
        if (jwtData) {
            document.getElementById('ownerName').innerText = jwtData.nama || jwtData.username;
            document.getElementById('ownerAvatar').innerText = jwtData.username.charAt(0).toUpperCase();
        }
    }

    // 2. Logika Tombol Keluar (Logout)
    document.getElementById('btn-logout')?.addEventListener('click', function(e) {
        e.preventDefault();
        if(confirm('Yakin ingin mengakhiri sesi dan keluar?')) {
            localStorage.removeItem('jwt_token');
            window.location.replace('../../index.php');
        }
    });

    // 3. Render Tanggal di Header
    const dateEl = document.getElementById('page-date');
    if (dateEl) {
        dateEl.textContent = new Date().toLocaleDateString('id-ID', { 
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
        });
    }

    // 4. FETCH DATA API LAPORAN
    await loadDashboardData();
});

async function loadDashboardData() {
    try {
        // --- A. Ambil Data Summary (Untuk Kotak KPI Atas) ---
        const resSummary = await fetch('../../api/laporan.php?type=summary', {
            headers: { 'Authorization': `Bearer ${getAuthToken()}` }
        });
        const dataSummary = await resSummary.json();
        
        if (dataSummary.status === 'success') {
            const sum = dataSummary.data;
            document.getElementById('kpi-pendapatan-hari').innerText = formatRupiah(sum.Penjualan_Hari_Ini);
            document.getElementById('kpi-transaksi-hari').innerText = sum.Transaksi_Hari_Ini || 0;
            document.getElementById('kpi-pendapatan-bulan').innerText = formatRupiah(sum.Penjualan_Bulan_Ini);
            document.getElementById('kpi-aset').innerText = formatRupiah(sum.Nilai_Inventori_Total);
        }

        // --- B. Ambil Data Harian (Untuk Grafik & Tabel) ---
        const resHarian = await fetch('../../api/laporan.php?type=harian&limit=30', {
            headers: { 'Authorization': `Bearer ${getAuthToken()}` }
        });
        const dataHarian = await resHarian.json();

        if (dataHarian.status === 'success') {
            renderTable(dataHarian.data);
            renderCharts(dataHarian.data);
        }

    } catch (error) {
        console.error("Gagal memuat data laporan:", error);
    }
}

function renderTable(data) {
    const tbody = document.getElementById('table-body');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Belum ada transaksi hari ini.</td></tr>';
        return;
    }

    data.forEach(row => {
        let badgeClass = 'badge-tunai';
        if (row.Metode_Pembayaran === 'QRIS') badgeClass = 'badge-success';
        else if (row.Metode_Pembayaran === 'Debit') badgeClass = 'badge-warning'; // Asumsi ada CSS nya

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="font-weight: 500;">${row.Tanggal}</td>
            <td>${row.Kasir}</td>
            <td><span class="badge ${badgeClass}">${row.Metode_Pembayaran}</span></td>
            <td>${row.Jumlah_Transaksi} Trx</td>
            <td style="font-weight: bold; color: var(--success);">${formatRupiah(row.Total_Penjualan)}</td>
        `;
        tbody.appendChild(tr);
    });
}

function renderCharts(data) {
    // Kelompokkan data per tanggal untuk Line Chart (karena 1 tanggal bisa ada bbrp metode bayar)
    const trendMap = {};
    const metodeMap = { 'Tunai': 0, 'QRIS': 0, 'Debit': 0 };

    // Proses data (Data dari API diurutkan DESC, kita reverse agar di chart dari kiri ke kanan / lama ke baru)
    const reversedData = [...data].reverse();

    reversedData.forEach(row => {
        // Akumulasi Tren Harian
        if (!trendMap[row.Tanggal]) trendMap[row.Tanggal] = 0;
        trendMap[row.Tanggal] += parseFloat(row.Total_Penjualan);

        // Akumulasi Metode Pembayaran
        if (metodeMap[row.Metode_Pembayaran] !== undefined) {
            metodeMap[row.Metode_Pembayaran] += parseInt(row.Jumlah_Transaksi);
        }
    });

    const labelsTren = Object.keys(trendMap);
    const dataTren = Object.values(trendMap);

    // 1. Render Line Chart
    const ctxTren = document.getElementById('chartTren');
    if (ctxTren && typeof Chart !== 'undefined') {
        if (chartTrenInstance) chartTrenInstance.destroy(); // Hapus canvas lama jika re-render
        chartTrenInstance = new Chart(ctxTren, {
            type: 'line',
            data: {
                labels: labelsTren,
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: dataTren,
                    borderColor: accentColor,
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderWidth: 2,
                    tension: 0.3, 
                    fill: true,
                    pointBackgroundColor: accentColor
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor } },
                    x: { grid: { display: false }, ticks: { color: textColor } }
                }
            }
        });
    }

    // 2. Render Doughnut Chart
    const ctxMetode = document.getElementById('chartMetode');
    if (ctxMetode && typeof Chart !== 'undefined') {
        if (chartMetodeInstance) chartMetodeInstance.destroy();
        chartMetodeInstance = new Chart(ctxMetode, {
            type: 'doughnut',
            data: {
                labels: ['Tunai', 'QRIS', 'Debit'],
                datasets: [{
                    data: [metodeMap['Tunai'], metodeMap['QRIS'], metodeMap['Debit']],
                    backgroundColor: [successColor, accentColor, '#fca311'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, padding: 20 } }
                }
            }
        });
    }
}