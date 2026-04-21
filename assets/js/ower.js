document.addEventListener('DOMContentLoaded', function() {
    // 1. Logika Tombol Filter Periode
    const periodBtns = document.querySelectorAll('.period-btn'); // Pastikan tombolmu punya class ini
    if (periodBtns.length > 0) {
        periodBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                periodBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Nanti ini memicu AJAX ke backend untuk ambil data baru
                console.log('Filter diubah ke:', this.innerText);
            });
        });
    }

    // 2. Konfigurasi Warna Tema (Otomatis menyesuaikan dark/light mode)
    const rootStyles = getComputedStyle(document.documentElement);
    const textColor = rootStyles.getPropertyValue('--text-secondary').trim() || '#888';
    const gridColor = rootStyles.getPropertyValue('--border').trim() || '#333';
    const accentColor = rootStyles.getPropertyValue('--accent').trim() || '#4361ee';
    const successColor = rootStyles.getPropertyValue('--success').trim() || '#22c55e';

    // 3. Render Grafik Tren Penjualan (Line Chart)
    const canvasTren = document.getElementById('chartTren');
    if (canvasTren && typeof Chart !== 'undefined') {
        new Chart(canvasTren, {
            type: 'line',
            data: {
                labels: ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: [150000, 450000, 320000, 890000, 540000, 760000, 1100000],
                    borderColor: accentColor,
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderWidth: 2,
                    tension: 0.4, // Membuat garis melengkung halus
                    fill: true,
                    pointBackgroundColor: accentColor
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } // Sembunyikan legenda agar bersih
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { color: textColor }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor }
                    }
                }
            }
        });
    }

    // 4. Render Grafik Metode Pembayaran (Doughnut Chart)
    const canvasMetode = document.getElementById('chartMetode');
    if (canvasMetode && typeof Chart !== 'undefined') {
        new Chart(canvasMetode, {
            type: 'doughnut',
            data: {
                labels: ['Tunai', 'QRIS', 'Debit'],
                datasets: [{
                    data: [65, 25, 10], // Persentase fiktif
                    backgroundColor: [
                        successColor, 
                        accentColor, 
                        '#fca311' // Warna peringatan/oranye
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // Ketebalan donat
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor, padding: 20 }
                    }
                }
            }
        });
    }
});