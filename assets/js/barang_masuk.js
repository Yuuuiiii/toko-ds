const API_URL = '../../api/barang_masuk.php';
const API_SUPPLIER = '../../api/supplier.php';
const getAuthToken = () => localStorage.getItem('jwt_token');

document.addEventListener('DOMContentLoaded', async () => {
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

    await loadSupplier();
    await loadHistory();
    
    // Auto-focus barcode
    document.getElementById('inputBarcode').focus();
});

async function loadSupplier() {
    const select = document.getElementById('inputSupplier');
    try {
        const response = await fetch(API_SUPPLIER, { headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
        const res = await response.json();
        if (res.status === 'success') {
            res.data.forEach(s => {
                select.innerHTML += `<option value="${s.ID_Supplier}">${s.Nama_Supplier}</option>`;
            });
        }
    } catch (e) { console.error(e); }
}

async function loadHistory() {
    const tbody = document.getElementById('table-body');
    try {
        const response = await fetch(API_URL, { headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
        const res = await response.json();
        tbody.innerHTML = '';

        if (res.status === 'success' && res.data.length > 0) {
            res.data.forEach(row => {
                tbody.innerHTML += `
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 16px; font-size: 13px; color: var(--text-secondary);">${row.Tanggal_Masuk}</td>
                        <td style="padding: 16px; font-size: 14px; font-weight: 600; color: var(--text-primary);">${row.Nama_Barang} <div style="font-size:11px; color:var(--text-secondary); font-weight:normal;">${row.SKU_Barang}</div></td>
                        <td style="padding: 16px; font-size: 15px; font-weight: 800; color: #10b981;">+${row.Jumlah_Masuk} Pcs</td>
                        <td style="padding: 16px; font-size: 13px; color: var(--text-secondary);">${row.Nama_Supplier || '-'}</td>
                        <td style="padding: 16px; font-size: 13px; color: var(--text-secondary);"><i data-lucide="user" style="width:14px; vertical-align:-2px;"></i> ${row.Nama_Admin}</td>
                    </tr>
                `;
            });
            if (window.lucide) lucide.createIcons();
        } else {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-secondary);">Belum ada riwayat barang masuk.</td></tr>';
        }
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--danger);">Gagal memuat riwayat.</td></tr>';
    }
}

// LOGIKA SUBMIT BARANG MASUK
document.getElementById('formInbound').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btnSimpan = document.getElementById('btnSimpan');
    const inputBarcode = document.getElementById('inputBarcode');
    
    const payload = {
        Barcode: inputBarcode.value.trim(),
        ID_Supplier: document.getElementById('inputSupplier').value,
        Qty: document.getElementById('inputQty').value,
        Keterangan: document.getElementById('inputKeterangan').value.trim()
    };

    btnSimpan.innerHTML = 'Merekam...'; btnSimpan.disabled = true;

    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const res = await response.json();

        if (res.status === 'success') {
            alert(res.message);
            this.reset(); // Kosongkan form
            document.getElementById('inputQty').value = 1; // Reset qty ke 1
            loadHistory(); // Update tabel bawah otomatis
        } else {
            alert('GAGAL: ' + res.message);
        }
    } catch (e) { alert('Terjadi kesalahan jaringan.'); } 
    finally {
        btnSimpan.innerHTML = '<i data-lucide="check-circle" style="width: 20px; height: 20px;"></i> Masukkan ke Gudang'; 
        btnSimpan.disabled = false;
        inputBarcode.focus(); // Kembalikan cursor ke barcode biar siap scan lagi
        if (window.lucide) lucide.createIcons();
    }
});