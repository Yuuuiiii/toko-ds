// ==========================================
// STATE MANAGEMENT & KONFIGURASI
// ==========================================
let cart = []; 
let masterBarang = []; 
let currentMember = null; 
let totalDiskonAmount = 0; 

const API_URL_BARANG = '../../api/barang.php';
const API_URL_TRANSAKSI = '../../api/transaksi.php';
const API_URL_PELANGGAN = '../../api/pelanggan.php';

const getAuthToken = () => localStorage.getItem('jwt_token');
function formatRupiah(angka) { return 'Rp ' + parseInt(angka).toLocaleString('id-ID'); }
function parseRupiah(text) { return parseInt(text.toString().replace(/[^0-9]/g, '')) || 0; }

// ==========================================
// INTEGRASI API (FETCH DATA)
// ==========================================
async function fetchMasterBarang() {
    try {
        const response = await fetch(API_URL_BARANG, { method: 'GET', headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
        const data = await response.json();
        if (data.status === 'success') masterBarang = data.data;
    } catch (error) { console.error("Error fetching barang:", error); }
}

// ==========================================
// LOGIKA MEMBER & DISKON
// ==========================================
async function cariMember() {
    const hp = document.getElementById('inputHpMember').value.trim();
    if (!hp) return alert('Masukkan Nomor HP Pelanggan!');

    document.getElementById('btnCariMember').innerText = '...';
    try {
        const response = await fetch(`${API_URL_PELANGGAN}?hp=${hp}`, { headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
        const res = await response.json();

        if (res.status === 'success') {
            aktifkanMember(res.data);
        } else {
            if (confirm(`Nomor HP ${hp} belum terdaftar.\nIngin mendaftarkan sebagai Member Baru?`)) {
                const namaBaru = prompt("Masukkan Nama Pelanggan:");
                if (namaBaru) mendaftarMemberBaru(namaBaru, hp);
            }
        }
    } catch (e) { alert('Gagal mencari member.'); }
    document.getElementById('btnCariMember').innerText = 'Cari';
}

async function mendaftarMemberBaru(nama, hp) {
    try {
        const response = await fetch(API_URL_PELANGGAN, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ Nama_Pelanggan: nama, No_HP: hp })
        });
        const res = await response.json();
        if (res.status === 'success') {
            alert('Member berhasil didaftarkan!');
            aktifkanMember(res.data);
        } else { alert(res.message); }
    } catch (e) { alert('Gagal mendaftar member.'); }
}

function aktifkanMember(dataMember) {
    currentMember = dataMember;
    document.getElementById('formCariMember').style.display = 'none';
    document.getElementById('infoMemberAktif').style.display = 'flex';
    document.getElementById('namaMemberUI').innerText = dataMember.Nama_Pelanggan;
    document.getElementById('hpMemberUI').innerText = dataMember.No_HP;
    renderCart(); 
}

function batalkanMember() {
    currentMember = null;
    totalDiskonAmount = 0;
    document.getElementById('formCariMember').style.display = 'flex';
    document.getElementById('infoMemberAktif').style.display = 'none';
    document.getElementById('inputHpMember').value = '';
    renderCart(); 
}

// ==========================================
// LOGIKA KERANJANG BELANJA (CART)
// ==========================================
function renderCart() {
    const cartBody = document.getElementById('cartBody');
    if (!cartBody) return;
    
    cartBody.innerHTML = '';
    let subtotalAwal = 0; let totalItem = 0;

    cart.forEach((item, index) => {
        const subtotal = item.qty * item.harga;
        subtotalAwal += subtotal; totalItem += item.qty; 

        const row = document.createElement('div');
        row.className = 'table-row';
        row.innerHTML = `
            <span class="row-num">${index + 1}</span>
            <div><div class="row-product-name">${item.nama}</div><div class="row-product-sku">SKU: ${item.sku}</div></div>
            <input type="number" class="qty-input" data-index="${index}" value="${item.qty}" min="1">
            <span class="row-price">${formatRupiah(item.harga)}</span>
            <span class="row-subtotal" style="font-weight:bold; color:var(--success);">${formatRupiah(subtotal)}</span>
            <button class="btn-delete" data-index="${index}" title="Hapus"><i data-lucide="trash-2" class="icon-sm"></i></button>
        `;
        cartBody.appendChild(row);
    });

    totalDiskonAmount = currentMember ? (subtotalAwal * 0.1) : 0;
    const grandTotal = subtotalAwal - totalDiskonAmount;

    const totalElm = document.querySelector('.total-amount');
    if(totalElm) {
        if(currentMember && subtotalAwal > 0) {
            // REVISI UX: Ukuran font diperkecil agar aman di layar kecil
            totalElm.innerHTML = `
                <div style="display: flex; align-items: baseline; justify-content: center; gap: 8px; line-height: 1;">
                    <span style="font-size: 14px; text-decoration: line-through; color: #ef4444; font-weight: 700;">
                        ${formatRupiah(subtotalAwal)}
                    </span>
                    <span style="font-size: 32px; color: var(--success); font-weight: 800;">
                        ${formatRupiah(grandTotal)}
                    </span>
                </div>
            `;
        } else {
            // Jika tidak ada diskon, ukuran font juga kita paskan 32px
            totalElm.innerHTML = `<span style="font-size: 32px; font-weight: 800;">${formatRupiah(grandTotal)}</span>`;
        }
        totalElm.setAttribute('data-nilai-asli', grandTotal);
    }
    
    const countElm = document.querySelector('.item-count span');
    if(countElm) countElm.innerText = totalItem;
    
    if (window.lucide) lucide.createIcons();
    hitungKembalian();
}

function addToCart(barang) {
    const existingItemIndex = cart.findIndex(item => item.sku === barang.SKU_Barang);
    if (existingItemIndex > -1) cart[existingItemIndex].qty += 1;
    else cart.push({ sku: barang.SKU_Barang, nama: barang.Nama_Barang, id_satuan: barang.ID_Satuan_Dasar, harga: parseFloat(barang.Harga_Jual), qty: 1 });
    renderCart();
    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('searchInput').value = '';
    document.getElementById('searchInput').focus();
}

function hitungKembalian() {
    const inputBayar = document.querySelector('.detail-input');
    const elmTotal = document.querySelector('.total-amount');
    const elmKembalian = document.querySelector('.kembalian-amount');

    if (inputBayar && elmTotal && elmKembalian) {
        const totalTagihan = parseInt(elmTotal.getAttribute('data-nilai-asli')) || 0;
        const uangBayar = parseRupiah(inputBayar.value);
        const kembalian = uangBayar - totalTagihan;

        if (kembalian < 0) {
            elmKembalian.innerText = "Kurang " + formatRupiah(Math.abs(kembalian));
            elmKembalian.style.color = "var(--danger)";
        } else {
            elmKembalian.innerText = formatRupiah(kembalian);
            elmKembalian.style.color = "var(--success)";
        }
    }
}

// ==========================================
// GENERATOR STRUK & CHECKOUT
// ==========================================
function generateStrukHTML(items, subtotal, diskon, grandTotal, bayar, kembalian, metode, waktu, trxId, kasir, namaPelanggan) {
    const tbody = document.getElementById('strukItems');
    if(!tbody) return;
    tbody.innerHTML = '';
    
    items.forEach(item => {
        const sub = item.qty * item.harga;
        tbody.innerHTML += `
            <tr><td colspan="3" style="padding-bottom:2px;">${item.nama}</td></tr>
            <tr>
                <td style="width: 15%; padding-bottom:6px;">${item.qty}x</td>
                <td style="width: 40%; padding-bottom:6px;">${item.harga.toLocaleString('id-ID')}</td>
                <td style="width: 45%; text-align:right; padding-bottom:6px;">${sub.toLocaleString('id-ID')}</td>
            </tr>
        `;
    });
    
    document.getElementById('strukTrx').innerText = trxId;
    document.getElementById('strukDate').innerText = waktu;
    document.getElementById('strukKasir').innerText = kasir;
    
    let detailTotalHTML = '';
    if(diskon > 0) {
        detailTotalHTML += `<div style="display:flex; justify-content:space-between; font-size:11px; margin-bottom:2px;"><span>SUBTOTAL</span><span>${subtotal.toLocaleString('id-ID')}</span></div>`;
        detailTotalHTML += `<div style="display:flex; justify-content:space-between; font-size:11px; margin-bottom:2px;"><span>DISKON MEMBER</span><span>-${diskon.toLocaleString('id-ID')}</span></div>`;
    }
    detailTotalHTML += `<div style="display:flex; justify-content:space-between; font-weight:bold; font-size:12px; margin-bottom:4px; margin-top:4px;"><span>TOTAL</span><span>${grandTotal.toLocaleString('id-ID')}</span></div>`;
    
    document.getElementById('strukTotal').innerHTML = detailTotalHTML;
    document.getElementById('strukBayar').innerText = bayar.toLocaleString('id-ID');
    document.getElementById('strukKembali').innerText = kembalian.toLocaleString('id-ID');
    document.getElementById('strukMetode').innerText = metode.toUpperCase();
}

async function prosesCheckout() {
    if (cart.length === 0) return alert('Keranjang kosong! Silakan tambah barang dulu.');

    const grandTotal = parseInt(document.querySelector('.total-amount').getAttribute('data-nilai-asli')) || 0;
    const btnActive = document.querySelector('.method-btn.active');
    
    let metodeBayar = 'Tunai';
    if (btnActive.innerText.includes('QRIS')) metodeBayar = 'QRIS';
    if (btnActive.innerText.includes('DEBIT')) metodeBayar = 'Debit';

    let bayar = grandTotal; 
    let kembalian = 0;
    if (metodeBayar === 'Tunai') {
        bayar = parseRupiah(document.querySelector('.detail-input').value);
        kembalian = bayar - grandTotal;
        if (bayar < grandTotal) return alert('GAGAL: Nominal uang tunai kurang!');
    }

    let idKasir = 2; let namaKasir = 'Kasir';
    const jwtData = typeof parseJwt === "function" ? parseJwt(getAuthToken()) : null;
    if (jwtData) { idKasir = jwtData.id || jwtData.ID_Pengguna; namaKasir = jwtData.username; }

    const payload = {
        ID_Kasir: idKasir,
        ID_Pelanggan: currentMember ? currentMember.ID_Pelanggan : null,
        Total_Harga: grandTotal,
        Total_Diskon: totalDiskonAmount,
        Metode_Pembayaran: metodeBayar,
        Items: cart.map(item => ({ SKU_Barang: item.sku, Jumlah_Jual: item.qty, ID_Satuan_Jual: item.id_satuan, Harga_Saat_Jual: item.harga }))
    };

    const btnCheckout = document.querySelector('.btn-checkout');
    btnCheckout.innerHTML = 'MEMPROSES...'; btnCheckout.disabled = true;

    try {
        const response = await fetch(API_URL_TRANSAKSI, { method: 'POST', headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const data = await response.json();

        if (data.status === 'success') {
            const waktuSekarang = new Date().toLocaleString('id-ID');
            const idTrxAsli = data.data.ID_Penjualan ? `TRX-${String(data.data.ID_Penjualan).padStart(4,'0')}` : 'TRX-BARU';
            
            generateStrukHTML(cart, (grandTotal + totalDiskonAmount), totalDiskonAmount, grandTotal, bayar, kembalian, metodeBayar, waktuSekarang, idTrxAsli, namaKasir, currentMember ? currentMember.Nama_Pelanggan : null);
            
            if(document.getElementById('modalSukses')) document.getElementById('modalSukses').style.display = 'flex';
            else alert('TRANSAKSI BERHASIL!');
            
        } else { alert('GAGAL CHECKOUT: ' + data.message); }
    } catch (error) { alert('Terjadi kesalahan jaringan.'); } 
    finally { btnCheckout.innerHTML = 'SELESAIKAN TRANSAKSI'; btnCheckout.disabled = false; }
}

// ==========================================
// INISIALISASI & EVENT LISTENER (YANG TADI HILANG)
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) lucide.createIcons();
    fetchMasterBarang();

    // 1. INI DIA KODE JAM REALTIME YANG HILANG TADI
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const dateString = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        
        const clockEl = document.getElementById('live-clock');
        const dateEl = document.getElementById('live-date');
        if (clockEl) clockEl.innerText = timeString;
        if (dateEl) dateEl.innerText = dateString;
    }
    setInterval(updateClock, 1000);
    updateClock(); 

    // 2. INI DIA KODE PENARIK DATA KASIR & LOGOUT YANG HILANG
    if (typeof parseJwt === "function") {
        const jwtData = parseJwt(getAuthToken()); 
        if (jwtData) {
            const elmName = document.getElementById('kasirName');
            const elmRole = document.getElementById('kasirRole');
            const elmAvatar = document.getElementById('kasirAvatar');
            if (elmName) elmName.innerText = jwtData.username;
            if (elmRole) elmRole.innerText = jwtData.role || 'Kasir';
            if (elmAvatar) elmAvatar.innerText = jwtData.username.charAt(0).toUpperCase();
        }
    }

// ==========================================
    // LOGIKA TUTUP SHIFT & LOGOUT
    // ==========================================
    let tunaiSistemHariIni = 0;

    document.getElementById('btn-logout')?.addEventListener('click', async function(e) {
        e.preventDefault();
        const originalText = this.innerHTML;
        this.innerHTML = 'Memuat...';
        
        try {
            // Ambil data total tunai dari API
            const res = await fetch('../../api/shift.php', { headers: { 'Authorization': `Bearer ${getAuthToken()}` } });
            const json = await res.json();
            
            if (json.status === 'success') {
                tunaiSistemHariIni = parseFloat(json.data.Total_Tunai_Sistem) || 0;
                document.getElementById('shiftSistemUI').innerText = formatRupiah(tunaiSistemHariIni);
                document.getElementById('inputUangFisik').value = '';
                document.getElementById('boxSelisih').style.display = 'none';
                document.getElementById('inputCatatanShift').value = '';
                
                document.getElementById('modalShift').style.display = 'flex';
            } else { alert('Gagal mengambil data shift: ' + json.message); }
        } catch (err) { alert('Terjadi kesalahan jaringan.'); } 
        finally { this.innerHTML = originalText; if (window.lucide) lucide.createIcons(); }
    });

    // Fitur Penghitung Selisih Uang Otomatis (Saat diketik)
    document.getElementById('inputUangFisik')?.addEventListener('input', function(e) {
        let val = this.value.replace(/[^0-9]/g, ''); // Hanya boleh angka
        if(val === '') { document.getElementById('boxSelisih').style.display = 'none'; return; }
        
        this.value = formatRupiah(val).replace('Rp ', ''); // Format angka cantik
        
        const uangFisik = parseInt(val) || 0;
        const selisih = uangFisik - tunaiSistemHariIni;
        const box = document.getElementById('boxSelisih');
        
        box.style.display = 'block';
        if (selisih === 0) {
            box.style.background = 'rgba(16, 185, 129, 0.1)'; box.style.color = 'var(--success)'; box.style.border = '1px solid rgba(16, 185, 129, 0.2)';
            box.innerText = '✅ BALANCE (Uang Pas)';
        } else if (selisih > 0) {
            box.style.background = 'rgba(245, 158, 11, 0.1)'; box.style.color = '#f59e0b'; box.style.border = '1px solid rgba(245, 158, 11, 0.2)';
            box.innerText = '⚠️ LEBIH ' + formatRupiah(selisih);
        } else {
            box.style.background = 'rgba(239, 68, 68, 0.1)'; box.style.color = 'var(--danger)'; box.style.border = '1px solid rgba(239, 68, 68, 0.2)';
            box.innerText = '❌ MINUS ' + formatRupiah(Math.abs(selisih));
        }
    });

    // Proses Submit Tutup Shift
    document.getElementById('btnProsesShift')?.addEventListener('click', async function() {
        const val = document.getElementById('inputUangFisik').value.replace(/[^0-9]/g, '');
        if (val === '') return alert('Masukkan nominal uang fisik yang ada di laci!');
        
        const uangFisik = parseInt(val) || 0;
        const catatan = document.getElementById('inputCatatanShift').value;
        const selisih = uangFisik - tunaiSistemHariIni;

        if (selisih !== 0 && catatan.trim() === '') {
            return alert('Karena uang laci selisih (Minus/Lebih), Anda WAJIB mengisi Laporan Catatan untuk Owner!');
        }

        if (!confirm('Yakin perhitungan sudah benar? Anda akan Log Out dan data shift dilaporkan.')) return;

        this.innerHTML = 'Memproses...'; this.disabled = true;

        try {
            const res = await fetch('../../api/shift.php', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json' },
                body: JSON.stringify({ Total_Tunai_Sistem: tunaiSistemHariIni, Total_Tunai_Fisik: uangFisik, Catatan: catatan })
            });
            const json = await res.json();
            
            if (json.status === 'success') {
                alert('Shift berhasil ditutup! Sampai jumpa.');
                localStorage.removeItem('jwt_token');
                window.location.replace('../../index.php');
            } else { alert('Gagal: ' + json.message); }
        } catch (err) { alert('Terjadi kesalahan jaringan.'); }
        finally { this.innerHTML = '<i data-lucide="log-out" style="width:16px; height:16px;"></i> Simpan & Log Out'; this.disabled = false; if(window.lucide) lucide.createIcons(); }
    });
    
    // Event Listener Member
    document.getElementById('btnCariMember')?.addEventListener('click', cariMember);
    document.getElementById('btnHapusMember')?.addEventListener('click', batalkanMember);
    document.getElementById('inputHpMember')?.addEventListener('keydown', e => { if (e.key === 'Enter') cariMember(); });

    // Live Search & Barcode
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const keyword = e.target.value.toLowerCase().trim();
        const searchResults = document.getElementById('searchResults');
        searchResults.innerHTML = '';
        if (keyword.length < 2) { searchResults.style.display = 'none'; return; }

        const hasil = masterBarang.filter(b => b.Nama_Barang.toLowerCase().includes(keyword) || b.SKU_Barang.toLowerCase().includes(keyword)).slice(0, 5); 
        if (hasil.length > 0) {
            searchResults.style.display = 'block';
            hasil.forEach(barang => {
                const div = document.createElement('div');
                div.style.padding = '12px 16px'; div.style.cursor = 'pointer'; div.style.borderBottom = '1px solid var(--border)'; div.style.color = 'var(--text-primary)';
                div.innerHTML = `<div style="font-weight: 600; font-size: 14px;">${barang.Nama_Barang}</div><div style="display:flex; justify-content:space-between; font-size:12px; color:var(--text-secondary); margin-top:4px;"><span>SKU: ${barang.SKU_Barang}</span><span style="font-weight:bold; color:var(--accent);">${formatRupiah(barang.Harga_Jual)}</span></div>`;
                div.addEventListener('click', () => addToCart(barang));
                searchResults.appendChild(div);
            });
        } else { searchResults.style.display = 'none'; }
    });

    document.getElementById('searchInput')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); 
            const skuScan = this.value.trim().toUpperCase(); 
            if (skuScan === '') return;
            const barangDitemukan = masterBarang.find(b => b.SKU_Barang.toUpperCase() === skuScan);
            if (barangDitemukan) { addToCart(barangDitemukan); this.value = ''; document.getElementById('searchResults').style.display = 'none'; this.focus(); }
            else { alert(`ALARM: Barcode [${skuScan}] tidak ditemukan!`); this.value = ''; this.focus(); }
        }
    });

    // Cart Events
    document.getElementById('cartBody')?.addEventListener('input', e => {
        if (e.target.classList.contains('qty-input')) {
            const index = e.target.getAttribute('data-index');
            let val = parseInt(e.target.value);
            if (isNaN(val) || val < 1) return;
            cart[index].qty = val; renderCart();
        }
    });

    document.getElementById('cartBody')?.addEventListener('click', e => {
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) { cart.splice(btnDelete.getAttribute('data-index'), 1); renderCart(); }
    });

    // --- FITUR AUTO-FORMAT TITIK RIBUAN UNTUK KASIR ---
    document.querySelector('.detail-input')?.addEventListener('input', function(e) {
        // 1. Hapus semua karakter yang bukan angka
        let value = this.value.replace(/[^0-9]/g, '');
        
        // 2. Kasih format titik gaya Indonesia
        if (value !== '') {
            this.value = parseInt(value, 10).toLocaleString('id-ID');
        } else {
            this.value = '';
        }
        
        // 3. Langsung panggil fungsi hitungan kembalian!
        // (Aman karena parseRupiah() di dalam hitungKembalian() akan otomatis membuang titiknya saat dihitung)
        hitungKembalian();
    });
    
    document.querySelector('.btn-clear')?.addEventListener('click', () => {
        if(cart.length > 0 && confirm('Yakin ingin membatalkan semua belanjaan?')) { cart = []; renderCart(); document.querySelector('.detail-input').value = ''; batalkanMember(); }
    });

    document.querySelectorAll('.method-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('active')); this.classList.add('active');
            if (this.innerText.includes('TUNAI')) { document.querySelector('.payment-detail').style.display = 'block'; hitungKembalian(); } 
            else document.querySelector('.payment-detail').style.display = 'none';
        });
    });

    document.querySelector('.btn-checkout')?.addEventListener('click', prosesCheckout);
    
    // Tombol Transaksi Baru di Modal Sukses
    document.getElementById('btnTransaksiBaru')?.addEventListener('click', () => {
        if(document.getElementById('modalSukses')) document.getElementById('modalSukses').style.display = 'none';
        cart = []; batalkanMember(); document.querySelector('.detail-input').value = ''; fetchMasterBarang(); document.getElementById('searchInput').focus();
    });

    // Tombol Cetak Struk di Modal Sukses
    document.getElementById('btnCetakStruk')?.addEventListener('click', () => {
        window.print();
    });

    // Fitur Riwayat Transaksi (Untuk memunculkan modal riwayat)
    document.getElementById('btnRiwayat')?.addEventListener('click', async () => {
        document.getElementById('modalRiwayat').style.display = 'flex';
        const tbody = document.getElementById('riwayatBody');
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 20px;">Memuat data...</td></tr>';
        
        try {
            const res = await fetch(API_URL_TRANSAKSI, { headers: { 'Authorization': `Bearer ${getAuthToken()}` } }); 
            const json = await res.json();
            
            tbody.innerHTML = '';
            if (json.status === 'success' && json.data.length > 0) {
                json.data.slice(0, 10).forEach(row => {
                    const idStr = `TRX-${String(row.ID_Penjualan).padStart(4,'0')}`;
                    
                    // REVISI: Teks "Tercatat" diubah kembali menjadi tombol "Cetak Ulang"
                    tbody.innerHTML += `
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 12px; font-weight: 600; color: var(--text-primary);">${idStr}</td>
                            <td style="padding: 12px; color: var(--text-secondary);">${row.Tanggal_Penjualan}</td>
                            <td style="padding: 12px;"><span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">${row.Metode_Pembayaran}</span></td>
                            <td style="padding: 12px; font-weight: 600; color: var(--success); text-align: right;">${formatRupiah(row.Total_Harga)}</td>
                            <td style="padding: 12px; text-align: right;">
                                <button onclick="cetakUlangStruk(${row.ID_Penjualan}, '${row.Tanggal_Penjualan}', '${row.Kasir}', '${row.Metode_Pembayaran}', ${row.Total_Harga})" style="background: var(--accent); color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: bold;">Cetak Ulang</button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 20px;">Tidak ada riwayat.</td></tr>';
            }
        } catch (error) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color: red;">Gagal memuat riwayat.</td></tr>'; }
    });

    // FUNGSI BARU: Menarik detail barang dan mencetak ulang struk
    window.cetakUlangStruk = async function(id_penjualan, waktu, kasir, metode, totalHarga) {
        try {
            // Kita panggil API Laporan yang sudah kamu miliki sebelumnya
            const res = await fetch(`../../api/laporan.php?type=detail&id=${id_penjualan}`, { 
                headers: { 'Authorization': `Bearer ${getAuthToken()}` } 
            });
            const json = await res.json();
            
            if(json.status === 'success') {
                const items = json.data.map(d => ({ 
                    nama: d.Nama_Barang, 
                    qty: d.Jumlah_Jual, 
                    harga: parseFloat(d.Harga_Saat_Jual) 
                }));
                const idStr = `TRX-${String(id_penjualan).padStart(4,'0')}`;
                
                // Karena ini cetak ulang riwayat, kita asumsikan bayar = uang pas
                generateStrukHTML(items, parseFloat(totalHarga), 0, parseFloat(totalHarga), parseFloat(totalHarga), 0, metode, waktu, idStr, kasir, null);
                
                window.print(); // Panggil dialog printer
            } else {
                alert("Gagal mengambil detail barang untuk struk ini.");
            }
        } catch (e) { 
            alert("Gagal menarik data struk dari server."); 
            console.error(e);
        }
    };
});