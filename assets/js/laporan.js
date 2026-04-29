const API_URL = '../../api/laporan.php';
const API_PENGELUARAN = '../../api/pengeluaran.php';
const getAuthToken = () => localStorage.getItem('jwt_token');

let currentLaporanData = []; 
let currentPengeluaranData = [];

function formatRupiah(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', async function() {
    if (typeof parseJwt === "function") {
        const jwtData = parseJwt(getAuthToken());
        if (jwtData) {
            document.getElementById('ownerName').innerText = jwtData.nama || jwtData.username;
            document.getElementById('ownerAvatar').innerText = jwtData.username.charAt(0).toUpperCase();
        }
    }

    const dateStartEl = document.getElementById('filterStart');
    const dateEndEl = document.getElementById('filterEnd');
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

    const formatDate = (dateObj) => {
        const y = dateObj.getFullYear();
        const m = String(dateObj.getMonth() + 1).padStart(2, '0');
        const d = String(dateObj.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    };

    if (dateStartEl && dateEndEl) {
        dateStartEl.value = formatDate(firstDay);
        dateEndEl.value = formatDate(today);
    }

    document.getElementById('btnFilter')?.addEventListener('click', loadSemuaLaporan);
    document.getElementById('btnExportExcel')?.addEventListener('click', exportToExcel);
    document.getElementById('btnExportPDF')?.addEventListener('click', exportToPDF);
    
    // Logika Modal Tambah Pengeluaran
    document.getElementById('btnTambahPengeluaran')?.addEventListener('click', () => {
        document.getElementById('formPengeluaran').reset();
        document.getElementById('inputTglPengeluaran').value = formatDate(today);
        document.getElementById('modalPengeluaran').style.display = 'flex';
    });

    document.getElementById('formPengeluaran')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSimpanPengeluaran');
        const payload = {
            Tanggal: document.getElementById('inputTglPengeluaran').value,
            Keterangan: document.getElementById('inputKetPengeluaran').value,
            // Hapus titiknya lagi sebelum dikirim ke API biar database gak error!
            Nominal: document.getElementById('inputNominalPengeluaran').value.replace(/\./g, '') 
        };
        btn.innerText = 'Menyimpan...'; btn.disabled = true;
        
        try {
            const res = await fetch(API_PENGELUARAN, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if(data.status === 'success') {
                alert(data.message);
                document.getElementById('modalPengeluaran').style.display = 'none';
                loadSemuaLaporan();
            } else alert("Gagal: " + data.message);
        } catch(e) { alert("Kesalahan jaringan."); }
        finally { btn.innerText = 'Simpan Pengeluaran'; btn.disabled = false; }
    });

    // --- FITUR AUTO-FORMAT TITIK RIBUAN ---
    const inputNominal = document.getElementById('inputNominalPengeluaran');
    if (inputNominal) {
        inputNominal.addEventListener('input', function(e) {
            // Hapus semua karakter yang BUKAN angka
            let value = this.value.replace(/[^0-9]/g, '');
            
            if (value !== '') {
                // Format ulang dengan titik gaya Indonesia
                this.value = parseInt(value, 10).toLocaleString('id-ID');
            } else {
                this.value = '';
            }
        });
    }

    if (window.lucide) lucide.createIcons();
    await loadSemuaLaporan();
});

async function loadSemuaLaporan() {
    const tbodyJual = document.getElementById('laporan-body');
    const tbodyKeluar = document.getElementById('pengeluaran-body');
    
    tbodyJual.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Memuat Pemasukan...</td></tr>';
    tbodyKeluar.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px;">Memuat Pengeluaran...</td></tr>';

    const startDate = document.getElementById('filterStart').value;
    const endDate = document.getElementById('filterEnd').value;

    try {
        // 1. Tarik Data Pemasukan (Penjualan)
        const resJual = await fetch(`${API_URL}?type=riwayat`, { method: 'GET' });
        const dataJual = await resJual.json();
        
        if (dataJual.status === 'success') {
            currentLaporanData = dataJual.data.filter(row => {
                const rowDate = row.Waktu_Transaksi.split(' ')[0];
                return rowDate >= startDate && rowDate <= endDate;
            });
        } else currentLaporanData = [];

        // 2. Tarik Data Pengeluaran
        const resKeluar = await fetch(API_PENGELUARAN, { method: 'GET' });
        const dataKeluar = await resKeluar.json();
        
        if (dataKeluar.status === 'success') {
            currentPengeluaranData = dataKeluar.data.filter(row => {
                return row.Tanggal_Pengeluaran >= startDate && row.Tanggal_Pengeluaran <= endDate;
            });
        } else currentPengeluaranData = [];

        renderSemuaTabel();

    } catch (error) {
        tbodyJual.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--danger);">Gagal menghubungi server.</td></tr>';
        tbodyKeluar.innerHTML = '<tr><td colspan="3" style="text-align: center; color: var(--danger);">Gagal menghubungi server.</td></tr>';
    }
}

function renderSemuaTabel() {
    const tbodyJual = document.getElementById('laporan-body');
    const tbodyKeluar = document.getElementById('pengeluaran-body');
    
    let totalPemasukan = 0;
    let totalPengeluaran = 0;

    // RENDER PEMASUKAN
    tbodyJual.innerHTML = '';
    if (currentLaporanData.length === 0) {
        tbodyJual.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">Tidak ada transaksi pemasukan.</td></tr>';
    } else {
        currentLaporanData.forEach(row => {
            totalPemasukan += parseFloat(row.Total_Penjualan) || 0;
            const trxIdString = `TRX-${String(row.ID_Penjualan).padStart(4, '0')}`;
            tbodyJual.innerHTML += `
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 16px; font-size: 14px; font-weight: 600; color: var(--text-secondary);">${trxIdString}</td>
                    <td style="padding: 16px; font-size: 14px; font-weight: 500; color: white;">${row.Waktu_Transaksi}</td>
                    <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);">${row.Kasir}</td>
                    <td style="padding: 16px; font-size: 12px; font-weight: 700;">${row.Metode_Pembayaran}</td>
                    <td style="padding: 16px; font-size: 14px; font-weight: 600; color: var(--success); text-align: right;">${formatRupiah(row.Total_Penjualan)}</td>
                    <td style="padding: 16px; text-align: right;">
                        <button onclick="lihatDetailTransaksi(${row.ID_Penjualan}, '${trxIdString}')" style="background: transparent; border: 1px solid var(--accent); color: var(--accent); padding: 4px 10px; border-radius: 6px; cursor: pointer;">Detail</button>
                    </td>
                </tr>
            `;
        });
    }

    // RENDER PENGELUARAN
    tbodyKeluar.innerHTML = '';
    if (currentPengeluaranData.length === 0) {
        tbodyKeluar.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 40px; color: var(--text-secondary);">Tidak ada pengeluaran.</td></tr>';
    } else {
        currentPengeluaranData.forEach(row => {
            totalPengeluaran += parseFloat(row.Nominal) || 0;
            
            // Ubah format tanggal YYYY-MM-DD ke DD MMM YYYY
            const dateObj = new Date(row.Tanggal_Pengeluaran);
            const formatTanggal = dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

            tbodyKeluar.innerHTML += `
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 16px; font-size: 14px; font-weight: 600; color: white;">${formatTanggal}</td>
                    <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);">${row.Keterangan}</td>
                    <td style="padding: 16px; font-size: 14px; font-weight: 700; color: #ef4444; text-align: right;">- ${formatRupiah(row.Nominal)}</td>
                </tr>
            `;
        });
    }

    // UPDATE KARTU SUMMARY
    const labaBersih = totalPemasukan - totalPengeluaran;
    document.getElementById('summaryPendapatan').innerText = formatRupiah(totalPemasukan);
    document.getElementById('summaryPengeluaran').innerText = formatRupiah(totalPengeluaran);
    
    const elLaba = document.getElementById('summaryLaba');
    elLaba.innerText = formatRupiah(labaBersih);
    if(labaBersih < 0) elLaba.style.color = '#ef4444'; // Merah kalau rugi
    else elLaba.style.color = '#10b981'; // Hijau kalau untung
    
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

// Bawaan Asli Detail Transaksi
window.lihatDetailTransaksi = async function(id_penjualan, trxString) {
    document.getElementById('detailTrxId').innerText = trxString;
    document.getElementById('modalDetail').style.display = 'flex';
    const tBodyDetail = document.getElementById('detail-body');
    tBodyDetail.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px;">Memuat detail...</td></tr>';
    
    try {
        const response = await fetch(`${API_URL}?type=detail&id=${id_penjualan}`);
        const res = await response.json();
        tBodyDetail.innerHTML = '';
        let total = 0;
        if (res.status === 'success' && res.data.length > 0) {
            res.data.forEach(item => {
                total += parseFloat(item.Subtotal);
                tBodyDetail.innerHTML += `
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px; color: white; font-size: 13px;">${item.Nama_Barang}</td>
                        <td style="padding: 12px; color: var(--text-secondary); font-size: 13px;">${formatRupiah(item.Harga_Saat_Jual)}</td>
                        <td style="padding: 12px; color: white; font-size: 13px; font-weight: 600;">x${item.Jumlah_Jual}</td>
                        <td style="padding: 12px; color: white; font-size: 13px; font-weight: 600; text-align: right;">${formatRupiah(item.Subtotal)}</td>
                    </tr>
                `;
            });
            document.getElementById('detailTotal').innerText = formatRupiah(total);
        } else {
            tBodyDetail.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px;">Detail tidak ditemukan.</td></tr>';
            document.getElementById('detailTotal').innerText = 'Rp 0';
        }
    } catch (error) { tBodyDetail.innerHTML = '<tr><td colspan="4" style="text-align: center; color: red;">Gagal memuat.</td></tr>'; }
}

// ==========================================
// EXPORT EXCEL (2 TAB SHEET: PEMASUKAN & PENGELUARAN)
// ==========================================
function exportToExcel() {
    const startDate = document.getElementById('filterStart').value;
    const endDate = document.getElementById('filterEnd').value;
    const wb = XLSX.utils.book_new();

    // Sheet 1: Pemasukan
    const dataJualExcel = currentLaporanData.map((row, index) => ({
        "No": index + 1,
        "ID Penjualan": `TRX-${String(row.ID_Penjualan).padStart(4, '0')}`,
        "Waktu": row.Waktu_Transaksi,
        "Kasir": row.Kasir,
        "Metode Bayar": row.Metode_Pembayaran,
        "Pemasukan (Rp)": parseFloat(row.Total_Penjualan)
    }));
    const ws1 = XLSX.utils.json_to_sheet(dataJualExcel.length > 0 ? dataJualExcel : [{"Data": "Tidak ada pemasukan"}]);
    XLSX.utils.book_append_sheet(wb, ws1, "Pemasukan");

    // Sheet 2: Pengeluaran
    const dataKeluarExcel = currentPengeluaranData.map((row, index) => ({
        "No": index + 1,
        "Tanggal": row.Tanggal_Pengeluaran,
        "Keterangan": row.Keterangan,
        "Pengeluaran (Rp)": parseFloat(row.Nominal)
    }));
    const ws2 = XLSX.utils.json_to_sheet(dataKeluarExcel.length > 0 ? dataKeluarExcel : [{"Data": "Tidak ada pengeluaran"}]);
    XLSX.utils.book_append_sheet(wb, ws2, "Pengeluaran");

    XLSX.writeFile(wb, `Laporan_Keuangan_${startDate}_sd_${endDate}.xlsx`);
}

// ==========================================
// EXPORT PDF (PEMASUKAN, PENGELUARAN, LABA BERSIH)
// ==========================================
function exportToPDF() {
    const startDate = document.getElementById('filterStart').value;
    const endDate = document.getElementById('filterEnd').value;

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4'); 

    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text("Laporan Keuangan & Akuntansi - Toko DS", 14, 20);
    
    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text(`Periode: ${startDate} s/d ${endDate}`, 14, 28);
    doc.text(`Dicetak: ${new Date().toLocaleString('id-ID')}`, 14, 34);

    let totalMasuk = 0;
    let totalKeluar = 0;

    // --- TABEL 1: PEMASUKAN ---
    doc.setFontSize(12);
    doc.setFont("helvetica", "bold");
    doc.text("A. Data Pemasukan (Penjualan)", 14, 45);

    const rowsMasuk = [];
    currentLaporanData.forEach((row, i) => {
        totalMasuk += parseFloat(row.Total_Penjualan);
        rowsMasuk.push([ i + 1, `TRX-${String(row.ID_Penjualan).padStart(4,'0')}`, row.Waktu_Transaksi, row.Metode_Pembayaran, formatRupiah(row.Total_Penjualan) ]);
    });

    doc.autoTable({
        startY: 50,
        head: [["No", "ID Transaksi", "Waktu", "Metode", "Total"]],
        body: rowsMasuk,
        theme: 'grid',
        headStyles: { fillColor: [16, 185, 129] }, // Hijau
        styles: { fontSize: 9 },
        columnStyles: { 4: { halign: 'right' } }
    });

    let currentY = doc.lastAutoTable.finalY + 15;

    // --- TABEL 2: PENGELUARAN ---
    doc.setFontSize(12);
    doc.setFont("helvetica", "bold");
    doc.text("B. Data Pengeluaran Operasional", 14, currentY);

    const rowsKeluar = [];
    currentPengeluaranData.forEach((row, i) => {
        totalKeluar += parseFloat(row.Nominal);
        rowsKeluar.push([ i + 1, row.Tanggal_Pengeluaran, row.Keterangan, formatRupiah(row.Nominal) ]);
    });

    doc.autoTable({
        startY: currentY + 5,
        head: [["No", "Tanggal", "Keterangan", "Nominal"]],
        body: rowsKeluar,
        theme: 'grid',
        headStyles: { fillColor: [239, 68, 68] }, // Merah
        styles: { fontSize: 9 },
        columnStyles: { 3: { halign: 'right' } }
    });

    currentY = doc.lastAutoTable.finalY + 15;

    // --- RINGKASAN AKHIR ---
    const labaBersih = totalMasuk - totalKeluar;
    doc.setFontSize(12);
    doc.setFont("helvetica", "normal");
    doc.text(`Total Pemasukan: ${formatRupiah(totalMasuk)}`, 14, currentY);
    doc.text(`Total Pengeluaran: ${formatRupiah(totalKeluar)}`, 14, currentY + 7);
    
    doc.setFontSize(14);
    doc.setFont("helvetica", "bold");
    doc.text(`Laba Bersih: ${formatRupiah(labaBersih)}`, 14, currentY + 17);

    doc.save(`Laporan_Keuangan_${startDate}_sd_${endDate}.pdf`);
}