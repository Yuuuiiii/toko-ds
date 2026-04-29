const API_URL = '../../api/barang.php';
const getAuthToken = () => localStorage.getItem('jwt_token');

let masterBarang = []; 
let currentFilterStatus = 'semua';

function formatRupiah(angka) { return 'Rp ' + parseInt(angka).toLocaleString('id-ID'); }

function applyFilters() {
    const keyword = document.getElementById('searchInput')?.value.toLowerCase().trim() || '';
    const filtered = masterBarang.filter(b => {
        const matchKeyword = b.Nama_Barang.toLowerCase().includes(keyword) || b.SKU_Barang.toLowerCase().includes(keyword);
        let matchStatus = true;
        if (currentFilterStatus === 'aman') matchStatus = b.Status_Stok === 'AMAN';
        else if (currentFilterStatus === 'menipis') matchStatus = (b.Status_Stok !== 'AMAN' && b.Status_Stok !== 'HABIS');
        else if (currentFilterStatus === 'habis') matchStatus = b.Status_Stok === 'HABIS';
        return matchKeyword && matchStatus;
    });
    renderTable(filtered); 
}

document.addEventListener('DOMContentLoaded', async function() {
    await loadKategori();
    await loadSatuan(); 
    await loadSupplier(); // Tambahkan ini

    async function loadSupplier() {
        const selectMaster = document.getElementById('inputSupplierMaster');
        const selectInbound = document.getElementById('inboundSupplier'); // Tangkap elemen baru
        
        try {
            const response = await fetch('../../api/supplier.php', { method: 'GET', headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
            const res = await response.json();
            
            if (selectMaster) selectMaster.innerHTML = '<option value="">-- Pilih Supplier --</option>';
            if (selectInbound) selectInbound.innerHTML = '<option value="">-- Non-Supplier / Lainnya --</option>';
            
            if (res.status === 'success') {
                res.data.forEach(sup => {
                    if (selectMaster) selectMaster.innerHTML += `<option value="${sup.ID_Supplier}">${sup.Nama_Supplier}</option>`;
                    if (selectInbound) selectInbound.innerHTML += `<option value="${sup.ID_Supplier}">${sup.Nama_Supplier}</option>`;
                });
            }
        } catch (error) { console.error('Gagal memuat supplier'); }
    }

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

    // --- FITUR AUTO-FORMAT TITIK RIBUAN UNTUK HARGA JUAL ---
    const inputHarga = document.getElementById('inputHarga');
    if (inputHarga) {
        inputHarga.addEventListener('input', function(e) {
            // Hapus semua karakter yang bukan angka
            let value = this.value.replace(/[^0-9]/g, '');
            if (value !== '') {
                // Kasih titik gaya Indonesia
                this.value = parseInt(value, 10).toLocaleString('id-ID');
            } else {
                this.value = '';
            }
        });
    }

    await loadBarang();

    document.getElementById('searchInput')?.addEventListener('input', applyFilters);
    document.querySelectorAll('.tab-filter').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-filter').forEach(b => b.classList.remove('active-tab'));
            this.classList.add('active-tab');
            currentFilterStatus = this.dataset.filter;
            applyFilters();
        });
    });

    // ==========================================
    // KEMBALINYA FITUR API OPENFOODFACTS SAKTI!
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
});

async function loadBarang() {
    const tbody = document.getElementById('table-body');
    try {
        const response = await fetch(API_URL, { method: 'GET', headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
        
        // JEBAKAN 1: Kita tangkap balasannya sebagai teks mentah dulu, bukan langsung JSON
        const text = await response.text(); 
        
        try {
            // JEBAKAN 2: Kita coba ubah paksa jadi JSON
            const data = JSON.parse(text); 
            if (data.status === 'success') {
                masterBarang = data.data;
                applyFilters();
            } else { 
                tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: #f59e0b; padding: 20px;">Gagal dari API: ${data.message}</td></tr>`;
            }
        } catch (e) {
            // JEBAKAN 3: Kalau gagal jadi JSON, berarti PHP ngeluarin Error HTML! Munculkan di layar!
            console.error("Raw Response PHP:", text);
            tbody.innerHTML = `<tr><td colspan="7" style="color: #ef4444; background: rgba(239,68,68,0.1); padding: 20px; font-family: monospace; font-size: 13px; line-height: 1.5;"><b>💥 KETAHUAN! INI ERROR DARI PHP:</b><br><br>${text}</td></tr>`;
        }
    } catch (error) { 
        tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: #ef4444; padding: 20px;">Koneksi jaringan terputus. Server mati?</td></tr>`;
    }
}

function renderTable(data) {
    const tbody = document.getElementById('table-body');
    if (!tbody) return;
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--text-secondary);">Tidak ada data.</td></tr>';
        return;
    }

    data.forEach(row => {
        let color = '#10b981'; 
        if (row.Status_Stok === 'HABIS') color = '#ef4444'; 
        else if (row.Status_Stok !== 'AMAN') color = '#f59e0b'; 

        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `
            <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);">${row.SKU_Barang}</td>
            <td style="padding: 16px; font-size: 14px; font-weight: 500; color: var(--text-primary);">${row.Nama_Barang}</td>
            <td style="padding: 16px; font-size: 14px; color: var(--text-secondary);">${row.Nama_Kategori || '-'}</td>
            <td style="padding: 16px; font-size: 14px; font-weight: 600; color: var(--success);">${formatRupiah(row.Harga_Jual)}</td>
            <td style="padding: 16px; font-size: 15px; font-weight: 800; color: var(--text-primary);">
                ${row.Stok_Tersedia} <span style="font-size: 12px; font-weight: 600; color: var(--text-secondary);">${row.Satuan_Dasar || ''}</span>
            </td>
            <td style="padding: 16px;"><span style="background:${color}20; color:${color}; padding:6px 12px; border-radius:6px; font-size:11px; font-weight:700;">${row.Status_Stok}</span></td>
            <td style="padding: 16px; text-align: right; white-space: nowrap;">
                <button onclick="editBarang('${row.SKU_Barang}')" style="background:transparent; border:none; color:var(--accent); cursor:pointer; margin-right:12px; font-weight:600;">Edit</button>
                <button onclick="hapusBarang('${row.SKU_Barang}', '${row.Nama_Barang}')" style="background:transparent; border:none; color:var(--danger); cursor:pointer; font-weight:600;">Hapus</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// ==========================================
// MODAL 1: TAMBAH STOK (INBOUND)
// ==========================================
document.getElementById('btnTambahStok')?.addEventListener('click', () => {
    document.getElementById('formInbound').reset();
    
    // FITUR BARU: Auto-set tanggal hari ini (waktu lokal)
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('inboundTanggal').value = now.toISOString().slice(0,16);

    document.getElementById('modalInbound').style.display = 'flex';
    setTimeout(() => document.getElementById('inboundSKU').focus(), 100);
});
document.getElementById('formInbound')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btnSimpan = document.getElementById('btnSimpanInbound');
    const payload = {
        Barcode: document.getElementById('inboundSKU').value.trim(),
        Qty: document.getElementById('inboundQty').value,
        Tanggal: document.getElementById('inboundTanggal').value, // <--- TAMBAHKAN BARIS INI
        // ID_Supplier: document.getElementById('inboundSupplier') ? document.getElementById('inboundSupplier').value : null, (kalau kamu pakai supplier di sini)
        Keterangan: document.getElementById('inboundKeterangan').value.trim()
    };
    btnSimpan.innerHTML = 'Memproses...'; btnSimpan.disabled = true;
    try {
        const response = await fetch('../../api/barang_masuk.php', {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const res = await response.json();
        if (res.status === 'success') {
            alert(res.message); 
            document.getElementById('modalInbound').style.display = 'none'; 
            loadBarang(); 
        } else { 
            alert('GAGAL: ' + res.message); 
            document.getElementById('inboundSKU').focus(); document.getElementById('inboundSKU').select();
        }
    } catch (error) { alert('Terjadi kesalahan jaringan.'); } 
    finally { btnSimpan.innerHTML = 'Konfirmasi Masuk'; btnSimpan.disabled = false; }
});

// ==========================================
// MODAL 2: MASTER BARANG (ASLIMU)
// ==========================================
document.getElementById('btnTambahBarang')?.addEventListener('click', () => {
    document.getElementById('modalTitle').innerText = 'Tambah Barang Baru';
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

    document.getElementById('modalTitle').innerText = 'Edit Data Barang';
    document.getElementById('formMode').value = 'edit';
    
    document.getElementById('inputSKU').value = barang.SKU_Barang;
    document.getElementById('inputSKU').readOnly = true; 
    document.getElementById('inputSKU').style.opacity = '0.5'; 
    
    document.getElementById('inputNama').value = barang.Nama_Barang;
    document.getElementById('inputHarga').value = parseInt(barang.Harga_Jual, 10).toLocaleString('id-ID');
    
    // PERHATIAN: inputStok sudah dihapus dari sini!

    const kategoriSelect = document.getElementById('inputKategori');
    if (kategoriSelect && barang.ID_Kategori) kategoriSelect.value = barang.ID_Kategori;

    const satuanSelect = document.getElementById('inputSatuan');
    if (satuanSelect && barang.ID_Satuan_Dasar) satuanSelect.value = barang.ID_Satuan_Dasar;

    const supplierSelect = document.getElementById('inputSupplierMaster');
    if (supplierSelect) supplierSelect.value = barang.ID_Supplier || '';

    modalBarang.style.display = 'flex';
    if (typeof lucide !== 'undefined') lucide.createIcons();
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

formBarang?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const mode = document.getElementById('formMode').value;
    const btnSimpan = document.getElementById('btnSimpan');
    
    try {
        const payload = {
            SKU_Barang: document.getElementById('inputSKU').value.trim(),
            Nama_Barang: document.getElementById('inputNama').value.trim(),
            // BUANG TITIKNYA SEBELUM DIKIRIM KE API
            Harga_Jual: document.getElementById('inputHarga').value.replace(/\./g, ''), 
            ID_Kategori: document.getElementById('inputKategori').value,
            ID_Satuan_Dasar: document.getElementById('inputSatuan').value,
            ID_Supplier: document.getElementById('inputSupplierMaster') ? document.getElementById('inputSupplierMaster').value : null
        };

        if(btnSimpan) {
            btnSimpan.innerHTML = 'Menyimpan...'; 
            btnSimpan.disabled = true;
        }

        const methodAPI = mode === 'add' ? 'POST' : 'PUT';
        const response = await fetch(API_URL, {
            method: methodAPI,
            headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const res = await response.json();

        if (res.status === 'success') {
            alert(`Barang berhasil ${mode === 'add' ? 'ditambahkan' : 'diperbarui'}!`);
            
            // Kita tembak langsung elemennya biar nggak usah nyari fungsi lagi!
            const modal = document.getElementById('modalBarang');
            if(modal) modal.style.display = 'none';
            
            loadBarang(); 
        } else {
            alert('Gagal: ' + res.message); 
        }
    } catch (error) { 
        // INI DIA! Kalau JS-nya crash, dia bakal ngasih tahu baris mana yang rusak!
        alert('Error Javascript: ' + error.message); 
        console.error("Detail Error JS:", error);
    } finally {
        if(btnSimpan) {
            btnSimpan.innerHTML = 'Simpan Master';
            btnSimpan.disabled = false;
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
});