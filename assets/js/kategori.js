const API_URL = '../../api/kategori.php';
const getAuthToken = () => localStorage.getItem('jwt_token');

let masterKategori = [];

document.addEventListener('DOMContentLoaded', () => {
    // === TAMBAHAN BARU: Menampilkan Username & Avatar dari JWT ===
    if (typeof parseJwt === "function") {
        const jwtData = parseJwt(getAuthToken());
        if (jwtData) {
            const ownerNameEl = document.getElementById('ownerName');
            const ownerAvatarEl = document.getElementById('ownerAvatar');
            if(ownerNameEl) ownerNameEl.innerText = jwtData.nama || jwtData.username;
            if(ownerAvatarEl) ownerAvatarEl.innerText = jwtData.username.charAt(0).toUpperCase();
        }
    }

    loadKategori();
    if (window.lucide) lucide.createIcons();

    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase().trim();
        const filtered = masterKategori.filter(k => k.Nama_Kategori.toLowerCase().includes(keyword));
        renderTable(filtered);
    });
});

async function loadKategori() {
    try {
        // Jangan lupa sertakan header Authorization agar tidak ditolak API (jika diproteksi)
        const response = await fetch(API_URL, {
            headers: { 'Authorization': `Bearer ${getAuthToken()}` }
        });
        const data = await response.json();
        if (data.status === 'success') {
            masterKategori = data.data;
            renderTable(masterKategori);
        }
    } catch (e) { console.error('Error:', e); }
}

function renderTable(data) {
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 40px; color: var(--text-secondary);">Tidak ada kategori.</td></tr>';
        return;
    }

    data.forEach(row => {
        tbody.innerHTML += `
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 16px 20px; color: var(--text-secondary); font-weight: 600;">KAT-${row.ID_Kategori}</td>
                <td style="padding: 16px 20px; color: var(--text-primary); font-weight: 500;">${row.Nama_Kategori}</td>
                <td style="padding: 16px 20px; text-align: right;">
                    <button onclick="editKategori(${row.ID_Kategori}, '${row.Nama_Kategori}')" style="background:transparent; border:none; color:var(--accent); cursor:pointer; margin-right:12px; font-weight:600;">Edit</button>
                    <button onclick="hapusKategori(${row.ID_Kategori})" style="background:transparent; border:none; color:var(--danger); cursor:pointer; font-weight:600;">Hapus</button>
                </td>
            </tr>
        `;
    });
}

// Modal Logic
const modal = document.getElementById('modalKategori');
document.getElementById('btnTambah').onclick = () => {
    document.getElementById('modalTitle').innerText = 'Tambah Kategori';
    document.getElementById('formMode').value = 'add';
    document.getElementById('formKategori').reset();
    modal.style.display = 'flex';
};

window.editKategori = (id, nama) => {
    document.getElementById('modalTitle').innerText = 'Edit Kategori';
    document.getElementById('formMode').value = 'edit';
    document.getElementById('inputId').value = id;
    document.getElementById('inputNama').value = nama;
    modal.style.display = 'flex';
};

document.getElementById('btnBatal').onclick = () => modal.style.display = 'none';

document.getElementById('formKategori').onsubmit = async (e) => {
    e.preventDefault();
    const mode = document.getElementById('formMode').value;
    const payload = {
        ID_Kategori: document.getElementById('inputId').value,
        Nama_Kategori: document.getElementById('inputNama').value
    };

    try {
        const response = await fetch(API_URL, {
            method: mode === 'add' ? 'POST' : 'PUT',
            headers: { 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${getAuthToken()}`
            },
            body: JSON.stringify(payload)
        });
        const res = await response.json();
        if (res.status === 'success') {
            modal.style.display = 'none';
            loadKategori();
        } else alert(res.message);
    } catch (e) { alert('Error jaringan'); }
};

window.hapusKategori = async (id) => {
    if(!confirm('Yakin ingin menghapus kategori ini?')) return;
    try {
        const response = await fetch(API_URL, {
            method: 'DELETE',
            headers: { 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${getAuthToken()}`
            },
            body: JSON.stringify({ ID_Kategori: id })
        });
        const res = await response.json();
        if (res.status === 'success') loadKategori();
        else alert(res.message);
    } catch (e) { alert('Error jaringan'); }
};