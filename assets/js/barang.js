const API_URL = '../../api/barang.php';
const getAuthToken = () => localStorage.getItem('jwt_token');

let masterBarang = []; 

function formatRupiah(angka) { return 'Rp ' + parseInt(angka).toLocaleString('id-ID'); }

document.addEventListener('DOMContentLoaded', async function() {
    await loadKategori();
    await loadSatuan(); 

    async function loadKategori() {
        const selectKategori = document.getElementById('inputKategori');
        try {
            const response = await fetch('../../api/kategori.php', { headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
            const res = await response.json();
            selectKategori.innerHTML = '<option value="">-- Pilih Kategori --</option>';
            if (res.status === 'success') res.data.forEach(kat => selectKategori.innerHTML += `<option value="${kat.ID_Kategori}">${kat.Nama_Kategori}</option>`);
        } catch (error) {}
    }

    async function loadSatuan() {
        const selectSatuan = document.getElementById('inputSatuan');
        try {
            const response = await fetch('../../api/satuan.php', { headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
            const res = await response.json();
            selectSatuan.innerHTML = '<option value="">-- Pilih Satuan --</option>';
            if (res.status === 'success') res.data.forEach(sat => selectSatuan.innerHTML += `<option value="${sat.ID_Satuan}">${sat.Nama_Satuan}</option>`);
        } catch (error) {}
    }
    
    if (typeof parseJwt === "function") {
        const jwtData = parseJwt(getAuthToken());
        if (jwtData) {
            const ownerNameEl = document.getElementById('ownerName');
            const ownerAvatarEl = document.getElementById('ownerAvatar');
            if(ownerNameEl) ownerNameEl.innerText = jwtData.nama || jwtData.username;
            if(ownerAvatarEl) ownerAvatarEl.innerText = jwtData.username.charAt(0).toUpperCase();
        }
    }

    await loadBarang();

    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase().trim();
        const filtered = masterBarang.filter(b => 
            b.Nama_Barang.toLowerCase().includes(keyword) || 
            b.SKU_Barang.toLowerCase().includes(keyword)
        );
        renderTable(filtered);
    });

    // ==========================================
    // API OPENFOODFACTS SAKTI!
    // ==========================================
    const inputSKU = document.getElementById('inputSKU');
    const inputNama = document.getElementById('inputNama');
    const inputKategori = document.getElementById('inputKategori');

    function setKategoriByText(targetText) {
        if(!inputKategori) return false;
        for (let i = 0; i < inputKategori.options.length; i++) {
            if (inputKategori.options[i].text.toLowerCase() === targetText.toLowerCase()) {
                inputKategori.selectedIndex = i; return true;
            }
        }
        return false;
    }

    inputSKU?.addEventListener('keydown', async function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); 
            const sku = this.value.trim();
            if (sku.length < 8) return; 

            inputNama.value = "Mencari data di internet..."; inputNama.disabled = true;

            try {
                const response = await fetch(`https://world.openfoodfacts.org/api/v0/product/${sku}.json`);
                const data = await response.json();

                if (data.status === 1 && data.product.product_name) {
                    const merk = data.product.brands ? data.product.brands + " " : "";
                    inputNama.value = merk + data.product.product_name;
                    
                    const apiCategories = (data.product.categories || "").toLowerCase();
                    let kategoriDitemukan = false;

                    if (apiCategories.includes('noodle') || apiCategories.includes('pasta')) kategoriDitemukan = setKategoriByText('Mie Instan');
                    else if (apiCategories.includes('beverage') || apiCategories.includes('drink') || apiCategories.includes('water')) {
                        kategoriDitemukan = setKategoriByText('Minuman Kemasan');
                    } else if (apiCategories.includes('snack') || apiCategories.includes('chips')) kategoriDitemukan = setKategoriByText('Makanan Ringan');

                    if (!kategoriDitemukan) inputKategori.selectedIndex = 0;
                    document.getElementById('inputHarga').focus();
                } else {
                    inputNama.value = ""; inputNama.placeholder = "Data tidak ditemukan. Ketik manual...";
                    inputNama.focus(); 
                }
            } catch (error) {
                inputNama.value = ""; inputNama.placeholder = "Koneksi API gagal. Ketik manual...";
                inputNama.focus();
            } finally { inputNama.disabled = false; }
        }
    });

    if (window.lucide) lucide.createIcons();
});

async function loadBarang() {
    try {
        const response = await fetch(API_URL, { headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
        const data = await response.json();
        if (data.status === 'success') { 
            masterBarang = data.data; 
            renderTable(masterBarang); 
        }
    } catch (error) {}
}

function renderTable(data) {
    const tbody = document.getElementById('table-body');
    if (!tbody) return;
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">Tidak ada data Master Barang.</td></tr>';
        return;
    }

    data.forEach(row => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `
            <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);">${row.SKU_Barang}</td>
            <td style="padding: 16px; font-size: 14px; font-weight: 500; color: var(--text-primary);">${row.Nama_Barang}</td>
            <td style="padding: 16px; font-size: 14px; color: var(--text-primary);">${formatRupiah(row.Harga_Jual)}</td>
            <td style="padding: 16px; font-size: 14px; color: var(--text-primary); font-weight: 600;">${row.Stok_Tersedia} ...
            <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);">${row.Satuan_Dasar || '-'}</td>
            <td style="padding: 16px; text-align: right; white-space: nowrap;">
                <button onclick="bukaModalBarcode('${row.SKU_Barang}', '${row.Nama_Barang}', ${row.Harga_Jual})" style="background:transparent; border:none; color:#10b981; cursor:pointer; margin-right:12px; font-weight:600;">Barcode</button>
                <button onclick="editBarang('${row.SKU_Barang}')" style="background:transparent; border:none; color:var(--accent); cursor:pointer; margin-right:12px; font-weight:600;">Edit</button>
                <button onclick="hapusBarang('${row.SKU_Barang}', '${row.Nama_Barang}')" style="background:transparent; border:none; color:var(--danger); cursor:pointer; font-weight:600;">Hapus</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// ==========================================
// MODAL MASTER BARANG
// ==========================================
document.getElementById('btnTambahBarang')?.addEventListener('click', () => {
    document.getElementById('modalTitle').innerText = 'Tambah Master Barang Baru';
    document.getElementById('formMode').value = 'add';
    document.getElementById('formBarang').reset();
    document.getElementById('inputSKU').readOnly = false;
    document.getElementById('inputSKU').style.opacity = '1';
    document.getElementById('modalBarang').style.display = 'flex';
    setTimeout(() => document.getElementById('inputSKU').focus(), 100);
});

window.editBarang = function(sku) {
    const barang = masterBarang.find(b => b.SKU_Barang === sku);
    if (!barang) return;
    document.getElementById('modalTitle').innerText = 'Edit Data Master Barang';
    document.getElementById('formMode').value = 'edit';
    document.getElementById('inputSKU').value = barang.SKU_Barang;
    document.getElementById('inputSKU').readOnly = true; 
    document.getElementById('inputSKU').style.opacity = '0.5'; 
    document.getElementById('inputNama').value = barang.Nama_Barang;
    document.getElementById('inputHarga').value = barang.Harga_Jual;
    if (barang.ID_Kategori) document.getElementById('inputKategori').value = barang.ID_Kategori;
    if (barang.ID_Satuan_Dasar) document.getElementById('inputSatuan').value = barang.ID_Satuan_Dasar;
    document.getElementById('modalBarang').style.display = 'flex';
};

window.hapusBarang = async function(sku, nama) {
    if (!confirm(`YAKIN INGIN MENGHAPUS BARANG INI?\nNama: ${nama}\nSKU: ${sku}`)) return;
    try {
        const response = await fetch(API_URL, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ SKU_Barang: sku })
        });
        const res = await response.json();
        if (res.status === 'success') { alert('Berhasil dihapus!'); loadBarang(); } 
        else { alert('GAGAL: ' + res.message); }
    } catch (error) { alert('Terjadi kesalahan jaringan.'); }
};

document.getElementById('formBarang')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const mode = document.getElementById('formMode').value;
    const btnSimpan = document.getElementById('btnSimpan');
    
    // PAYLOAD MURNI TANPA STOK AWAL!
    const payload = {
        SKU_Barang: document.getElementById('inputSKU').value.trim(),
        Nama_Barang: document.getElementById('inputNama').value.trim(),
        Harga_Jual: document.getElementById('inputHarga').value,
        ID_Kategori: document.getElementById('inputKategori').value,
        ID_Satuan_Dasar: document.getElementById('inputSatuan').value
    };

    btnSimpan.innerHTML = 'Menyimpan...'; btnSimpan.disabled = true;

    try {
        const response = await fetch(API_URL, {
            method: mode === 'add' ? 'POST' : 'PUT',
            headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const res = await response.json();
        if (res.status === 'success') {
            alert(`Berhasil ${mode === 'add' ? 'mendaftarkan' : 'memperbarui'} master barang!`);
            document.getElementById('modalBarang').style.display = 'none'; 
            loadBarang(); 
        } else { alert('Gagal: ' + res.message); }
    } catch (error) { alert('Terjadi kesalahan jaringan.'); } 
    finally { btnSimpan.innerHTML = 'Simpan Master'; btnSimpan.disabled = false; }
});