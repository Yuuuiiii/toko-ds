const API_URL = '../../api/barang_masuk.php';
const getAuthToken = () => localStorage.getItem('jwt_token');

let riwayatData = [];

document.addEventListener('DOMContentLoaded', async function() {
    if (typeof parseJwt === "function") {
        const jwtData = parseJwt(getAuthToken());
        if (jwtData) {
            const ownerNameEl = document.getElementById('ownerName');
            const ownerAvatarEl = document.getElementById('ownerAvatar');
            if(ownerNameEl) ownerNameEl.innerText = jwtData.nama || jwtData.username;
            if(ownerAvatarEl) ownerAvatarEl.innerText = jwtData.username.charAt(0).toUpperCase();
        }
    }

    if (window.lucide) lucide.createIcons();

    await loadRiwayat();

    // Fitur Live Search
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase().trim();
        const filtered = riwayatData.filter(r => 
            (r.Nama_Barang && r.Nama_Barang.toLowerCase().includes(keyword)) ||
            (r.SKU_Barang && r.SKU_Barang.toLowerCase().includes(keyword)) ||
            (r.Nama_Admin && r.Nama_Admin.toLowerCase().includes(keyword)) ||
            (r.Nama_Supplier && r.Nama_Supplier.toLowerCase().includes(keyword)) ||
            (r.Keterangan && r.Keterangan.toLowerCase().includes(keyword))
        );
        renderTable(filtered);
    });
});

async function loadRiwayat() {
    const tbody = document.getElementById('table-body');
    try {
        const response = await fetch(API_URL, { 
            method: 'GET', 
            headers: { 'Authorization': `Bearer ${getAuthToken()}` } 
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            riwayatData = data.data;
            renderTable(riwayatData);
        } else {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #f59e0b; padding: 20px;">Gagal memuat: ${data.message}</td></tr>`;
        }
    } catch (error) { 
        tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #ef4444; padding: 20px;">Koneksi jaringan terputus. Periksa server!</td></tr>`;
        console.error(error);
    }
}

function renderTable(data) {
    const tbody = document.getElementById('table-body');
    if (!tbody) return;
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">Belum ada riwayat barang masuk.</td></tr>';
        return;
    }

    data.forEach(row => {
        // Format Tanggal jadi lebih enak dibaca (Contoh: 29 Apr 2026, 06:54)
        const dateObj = new Date(row.Tanggal_Masuk);
        const formatTanggal = dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        const formatJam = dateObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `
            <td style="padding: 16px; font-size: 13px; color: var(--text-secondary); font-weight: 600;">
                ${formatTanggal} <span style="font-size:11px; color:var(--text-muted); display:block;">${formatJam}</span>
            </td>
            <td style="padding: 16px; font-size: 14px; font-weight: 600; color: var(--text-primary);">
                ${row.Nama_Barang} <span style="font-size:11px; color:var(--text-muted); display:block; font-weight:normal;">SKU: ${row.SKU_Barang}</span>
            </td>
            <td style="padding: 16px; font-size: 15px; font-weight: 800; color: #10b981;">
                +${row.Jumlah_Masuk}
            </td>
            <td style="padding: 16px; font-size: 13px; color: var(--text-secondary);">
                ${row.Nama_Supplier || '<span style="color:var(--text-muted); font-style:italic;">Tanpa Supplier</span>'}
            </td>
            <td style="padding: 16px; font-size: 13px; color: var(--text-secondary);">
                <i data-lucide="user" style="width:14px; height:14px; vertical-align:-2px; margin-right:4px;"></i> ${row.Nama_Admin}
            </td>
            <td style="padding: 16px; font-size: 13px; color: var(--text-secondary); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${row.Keterangan || ''}">
                ${row.Keterangan || '-'}
            </td>
        `;
        tbody.appendChild(tr);
    });

    if (typeof lucide !== 'undefined') lucide.createIcons();
}