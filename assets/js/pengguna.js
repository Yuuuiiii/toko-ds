const API_URL = '../../api/pengguna.php';
const getAuthToken = () => localStorage.getItem('jwt_token');

let masterPengguna = []; 

document.addEventListener('DOMContentLoaded', async function() {
    if (window.lucide) lucide.createIcons();
    
    // 1. FIX: Menarik Data Nama Admin ke Sidebar
    if (typeof parseJwt === "function") {
        const jwtData = parseJwt(getAuthToken());
        if (jwtData) {
            const ownerNameEl = document.getElementById('ownerName');
            const ownerAvatarEl = document.getElementById('ownerAvatar');
            if(ownerNameEl) ownerNameEl.innerText = jwtData.nama || jwtData.username;
            if(ownerAvatarEl) ownerAvatarEl.innerText = jwtData.username.charAt(0).toUpperCase();
        }
    }

    await loadPengguna();

    // Fitur Live Search
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase().trim();
        const filtered = masterPengguna.filter(p => 
            p.Nama_Lengkap.toLowerCase().includes(keyword) || 
            p.Username.toLowerCase().includes(keyword)
        );
        renderTable(filtered);
    });
});

async function loadPengguna() {
    try {
        const response = await fetch(API_URL, { method: 'GET', headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
        const data = await response.json();
        if (data.status === 'success') {
            masterPengguna = data.data;
            renderTable(masterPengguna);
        } else { alert("Gagal memuat data pengguna."); }
    } catch (error) { console.error("Error fetching data:", error); }
}

function renderTable(data) {
    const tbody = document.getElementById('table-body');
    if (!tbody) return;
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 40px; color: var(--text-secondary);">Tidak ada data pengguna.</td></tr>';
        return;
    }

    data.forEach(row => {
        let roleColor = 'var(--accent)';
        if (row.Peran === 'Admin') roleColor = '#f59e0b';
        else if (row.Peran === 'Gudang') roleColor = '#10b981';

        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `
            <td style="padding: 16px; font-size: 14px; font-weight: 500; color: var(--text-primary);">${row.Nama_Lengkap}</td>
            <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);"><i data-lucide="user" style="width: 14px; height: 14px; margin-right: 4px; vertical-align: middle;"></i>${row.Username}</td>
            <td style="padding: 16px;">
                <span style="background: ${roleColor}20; color: ${roleColor}; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                    ${row.Peran}
                </span>
            </td>
            <td style="padding: 16px; text-align: right;">
                <button onclick="editPengguna(${row.ID_Pengguna})" style="background:transparent; border:none; color:var(--accent); cursor:pointer; margin-right: 12px; font-weight: 600; font-size: 13px;">Edit</button>
                <button onclick="hapusPengguna(${row.ID_Pengguna}, '${row.Nama_Lengkap}')" style="background:transparent; border:none; color:var(--danger); cursor:pointer; font-weight: 600; font-size: 13px;">Hapus</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    if (window.lucide) lucide.createIcons();
}

// ==========================================
// MODAL & CRUD LOGIC
// ==========================================
const modalPengguna = document.getElementById('modalPengguna');
const formPengguna = document.getElementById('formPengguna');

document.getElementById('btnTambah')?.addEventListener('click', () => {
    document.getElementById('modalTitle').innerText = 'Tambah Pengguna Baru';
    document.getElementById('formMode').value = 'add';
    document.getElementById('passwordHint').style.display = 'none';
    document.getElementById('inputPassword').required = true; 
    formPengguna.reset();
    modalPengguna.style.display = 'flex';
});

window.editPengguna = function(id) {
    // 2. FIX: Pakai '==' alih-alih '===' untuk mengabaikan perbedaan Tipe Data
    const user = masterPengguna.find(m => m.ID_Pengguna == id);
    if (!user) return;

    document.getElementById('modalTitle').innerText = 'Edit Data Pengguna';
    document.getElementById('formMode').value = 'edit';
    
    document.getElementById('inputId').value = user.ID_Pengguna;
    document.getElementById('inputNama').value = user.Nama_Lengkap;
    document.getElementById('inputUsername').value = user.Username;
    document.getElementById('inputPeran').value = user.Peran;
    
    document.getElementById('inputPassword').value = '';
    document.getElementById('inputPassword').required = false; 
    document.getElementById('passwordHint').style.display = 'block';

    modalPengguna.style.display = 'flex';
};

window.hapusPengguna = async function(id, nama) {
    if (!confirm(`Yakin ingin mencabut akses dan menghapus pengguna: ${nama}?`)) return;
    
    try {
        const response = await fetch(API_URL, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ ID_Pengguna: id })
        });
        const res = await response.json();
        if (res.status === 'success') { alert('Berhasil dihapus!'); loadPengguna(); } 
        else { alert('GAGAL: ' + res.message); }
    } catch (e) { alert('Terjadi kesalahan jaringan.'); }
};

const tutupModal = () => { modalPengguna.style.display = 'none'; };
document.getElementById('btnTutupModal')?.addEventListener('click', tutupModal);
document.getElementById('btnBatal')?.addEventListener('click', tutupModal);

formPengguna?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const mode = document.getElementById('formMode').value;
    const btnSimpan = document.getElementById('btnSimpan');
    
    const payload = {
        ID_Pengguna: document.getElementById('inputId').value,
        Nama_Lengkap: document.getElementById('inputNama').value,
        Username: document.getElementById('inputUsername').value,
        Peran: document.getElementById('inputPeran').value,
        Password: document.getElementById('inputPassword').value
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
            alert(`Pengguna berhasil ${mode === 'add' ? 'ditambahkan' : 'diperbarui'}!`);
            tutupModal(); loadPengguna(); 
        } else { alert('Gagal: ' + res.message); }
    } catch (e) { alert('Terjadi kesalahan jaringan.'); } 
    finally {
        btnSimpan.innerHTML = '<i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan'; btnSimpan.disabled = false;
        if (window.lucide) lucide.createIcons();
    }
});