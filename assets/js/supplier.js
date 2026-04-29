const API_URL = '../../api/supplier.php';
const getAuthToken = () => localStorage.getItem('jwt_token');
let masterSupplier = []; 

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

    await loadSupplier();

    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase().trim();
        const filtered = masterSupplier.filter(s => 
            s.Nama_Supplier.toLowerCase().includes(keyword) || 
            (s.Nama_Sales && s.Nama_Sales.toLowerCase().includes(keyword)) ||
            (s.Kontak_Supplier && s.Kontak_Supplier.toLowerCase().includes(keyword))
        );
        renderTable(filtered);
    });

    if (window.lucide) lucide.createIcons();
});

async function loadSupplier() {
    try {
        const response = await fetch(API_URL, { method: 'GET', headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
        const data = await response.json();
        if (data.status === 'success') {
            masterSupplier = data.data;
            renderTable(masterSupplier);
        } else alert("Gagal memuat data supplier.");
    } catch (error) { console.error("Error fetching data:", error); }
}

function renderTable(data) {
    const tbody = document.getElementById('table-body');
    if (!tbody) return;
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-secondary);">Tidak ada data supplier ditemukan.</td></tr>';
        return;
    }

    data.forEach(row => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `
            <td style="padding: 16px; font-size: 13px; color: var(--text-secondary); font-weight: 600;">SUP-${row.ID_Supplier}</td>
            <td style="padding: 16px; font-size: 14px; font-weight: 600; color: var(--text-primary);">${row.Nama_Supplier} <div style="font-size:11px; color:var(--text-muted); font-weight:normal; margin-top:4px;">${row.Alamat || '-'}</div></td>
            <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);">${row.Nama_Sales || '-'}</td>
            <td style="padding: 16px; font-size: 14px; font-weight: 600; color: var(--success);">${row.Kontak_Supplier || '-'}</td>
            <td style="padding: 16px; text-align: right;">
                <button onclick="editSupplier(${row.ID_Supplier})" style="background:transparent; border:none; color:var(--accent); cursor:pointer; margin-right: 12px; font-weight: 600; font-size: 13px;">Edit</button>
                <button onclick="hapusSupplier(${row.ID_Supplier}, '${row.Nama_Supplier}')" style="background:transparent; border:none; color:var(--danger); cursor:pointer; font-weight: 600; font-size: 13px;">Hapus</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// === MODAL LOGIC ===
const modalSupplier = document.getElementById('modalSupplier');
const formSupplier = document.getElementById('formSupplier');

document.getElementById('btnTambah')?.addEventListener('click', () => {
    document.getElementById('modalTitle').innerText = 'Tambah Supplier Baru';
    document.getElementById('formMode').value = 'add';
    formSupplier.reset();
    modalSupplier.style.display = 'flex';
});

window.editSupplier = function(id) {
    const sup = masterSupplier.find(m => m.ID_Supplier == id);
    if (!sup) return;

    document.getElementById('modalTitle').innerText = 'Edit Data Supplier';
    document.getElementById('formMode').value = 'edit';
    document.getElementById('inputId').value = sup.ID_Supplier;
    document.getElementById('inputNama').value = sup.Nama_Supplier;
    document.getElementById('inputSales').value = sup.Nama_Sales;
    document.getElementById('inputKontak').value = sup.Kontak_Supplier;
    document.getElementById('inputAlamat').value = sup.Alamat;

    modalSupplier.style.display = 'flex';
};

window.hapusSupplier = async function(id, nama) {
    if (!confirm(`Yakin ingin menghapus Pabrik/Supplier: ${nama}?`)) return;
    try {
        const response = await fetch(API_URL, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ ID_Supplier: id })
        });
        const res = await response.json();
        if (res.status === 'success') { loadSupplier(); } else alert('GAGAL: ' + res.message);
    } catch (e) { alert('Terjadi kesalahan jaringan.'); }
};

const tutupModal = () => { modalSupplier.style.display = 'none'; };
document.getElementById('btnTutupModal')?.addEventListener('click', tutupModal);
document.getElementById('btnBatal')?.addEventListener('click', tutupModal);

formSupplier?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const mode = document.getElementById('formMode').value;
    const btnSimpan = document.getElementById('btnSimpan');
    
    const payload = {
        ID_Supplier: document.getElementById('inputId').value,
        Nama_Supplier: document.getElementById('inputNama').value,
        Nama_Sales: document.getElementById('inputSales').value,
        Kontak_Supplier: document.getElementById('inputKontak').value,
        Alamat: document.getElementById('inputAlamat').value
    };

    btnSimpan.innerHTML = 'Menyimpan...'; btnSimpan.disabled = true;

    try {
        const methodAPI = mode === 'add' ? 'POST' : 'PUT';
        const response = await fetch(API_URL, {
            method: methodAPI,
            headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const res = await response.json();

        if (res.status === 'success') {
            tutupModal(); loadSupplier(); 
        } else alert('Gagal: ' + res.message);
    } catch (e) { alert('Terjadi kesalahan jaringan.'); } 
    finally {
        btnSimpan.innerHTML = '<i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Data'; 
        btnSimpan.disabled = false;
        if (window.lucide) lucide.createIcons();
    }
});