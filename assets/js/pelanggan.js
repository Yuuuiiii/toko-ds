const API_URL = '../../api/pelanggan.php';
const getAuthToken = () => localStorage.getItem('jwt_token');

let masterPelanggan = []; 

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

    await loadPelanggan();

    // Fitur Live Search
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase().trim();
        const filtered = masterPelanggan.filter(p => 
            p.Nama_Pelanggan.toLowerCase().includes(keyword) || 
            p.No_HP.toLowerCase().includes(keyword)
        );
        renderTable(filtered);
    });

    if (window.lucide) lucide.createIcons();
});

async function loadPelanggan() {
    try {
        const response = await fetch(API_URL, { method: 'GET', headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
        const data = await response.json();
        if (data.status === 'success') {
            masterPelanggan = data.data;
            renderTable(masterPelanggan);
        } else { alert("Gagal memuat data member."); }
    } catch (error) { console.error("Error fetching data:", error); }
}

function renderTable(data) {
    const tbody = document.getElementById('table-body');
    if (!tbody) return;
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-secondary);">Tidak ada data pelanggan ditemukan.</td></tr>';
        return;
    }

    data.forEach((row, index) => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `
            <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);">MBR-${row.ID_Pelanggan}</td>
            <td style="padding: 16px; font-size: 14px; font-weight: 500; color: var(--text-primary);">${row.Nama_Pelanggan}</td>
            <td style="padding: 16px; font-size: 14px; font-weight: 600; color: var(--success);">${row.No_HP}</td>
            <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);">${row.Tanggal_Daftar}</td>
            <td style="padding: 16px; text-align: right;">
                <button onclick="editMember(${row.ID_Pelanggan})" style="background:transparent; border:none; color:var(--accent); cursor:pointer; margin-right: 12px; font-weight: 600; font-size: 13px;">Edit</button>
                <button onclick="hapusMember(${row.ID_Pelanggan}, '${row.Nama_Pelanggan}')" style="background:transparent; border:none; color:var(--danger); cursor:pointer; font-weight: 600; font-size: 13px;">Hapus</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// ==========================================
// MODAL & CRUD LOGIC
// ==========================================
const modalMember = document.getElementById('modalMember');
const formMember = document.getElementById('formMember');

document.getElementById('btnTambah')?.addEventListener('click', () => {
    document.getElementById('modalTitle').innerText = 'Tambah Member Baru';
    document.getElementById('formMode').value = 'add';
    formMember.reset();
    modalMember.style.display = 'flex';
});

window.editMember = function(id) {
    // REVISI: Pakai '==' (Dua Sama Dengan) agar kebal dari perbedaan tipe data String vs Integer
    const member = masterPelanggan.find(m => m.ID_Pelanggan == id); 
    
    if (!member) {
        alert("Data member tidak ditemukan di sistem!");
        return;
    }

    document.getElementById('modalTitle').innerText = 'Edit Data Member';
    document.getElementById('formMode').value = 'edit';
    document.getElementById('inputId').value = member.ID_Pelanggan;
    document.getElementById('inputNama').value = member.Nama_Pelanggan;
    document.getElementById('inputHp').value = member.No_HP;

    modalMember.style.display = 'flex';
};

window.hapusMember = async function(id, nama) {
    if (!confirm(`Yakin ingin menghapus member ${nama}? (Riwayat transaksi tidak akan terhapus, hanya data diskonnya saja yang di-nonaktifkan).`)) return;
    
    try {
        const response = await fetch(API_URL, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ ID_Pelanggan: id })
        });
        const res = await response.json();
        if (res.status === 'success') { alert('Berhasil dihapus!'); loadPelanggan(); } 
        else { alert('GAGAL: ' + res.message); }
    } catch (e) { alert('Terjadi kesalahan jaringan.'); }
};

const tutupModal = () => { modalMember.style.display = 'none'; };
document.getElementById('btnTutupModal')?.addEventListener('click', tutupModal);
document.getElementById('btnBatal')?.addEventListener('click', tutupModal);

formMember?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const mode = document.getElementById('formMode').value;
    const btnSimpan = document.getElementById('btnSimpan');
    
    const payload = {
        ID_Pelanggan: document.getElementById('inputId').value,
        Nama_Pelanggan: document.getElementById('inputNama').value,
        No_HP: document.getElementById('inputHp').value
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
            alert(`Member berhasil ${mode === 'add' ? 'ditambahkan' : 'diperbarui'}!`);
            tutupModal(); loadPelanggan(); 
        } else { alert('Gagal: ' + res.message); }
    } catch (e) { alert('Terjadi kesalahan jaringan.'); } 
    finally {
        btnSimpan.innerHTML = '<i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan'; btnSimpan.disabled = false;
        if (window.lucide) lucide.createIcons();
    }
});