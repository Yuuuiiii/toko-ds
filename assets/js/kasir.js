
// FUNGSI UTILITAS (Hitung & Format)

function formatRupiah(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

function parseRupiah(text) {
    return parseInt(text.replace(/[^0-9]/g, '')) || 0;
}

function hitungTotalTransaksi() {
    let grandTotal = 0;
    let totalItem = 0;
    const barisBarang = document.querySelectorAll('.table-row');

    barisBarang.forEach(baris => {
        const inputQty = baris.querySelector('.qty-input');
        const elmHarga = baris.querySelector('.row-price');
        const elmSubtotal = baris.querySelector('.row-subtotal');

        if (inputQty && elmHarga && elmSubtotal) {
            const qty = parseInt(inputQty.value) || 0;
            const harga = parseRupiah(elmHarga.innerText);
            const subtotal = qty * harga;

            elmSubtotal.innerText = formatRupiah(subtotal);
            grandTotal += subtotal;
            totalItem += 1;
        }
    });

    // Update panel kanan & footer
    const elmTotalMassive = document.querySelector('.total-amount');
    if (elmTotalMassive) elmTotalMassive.innerText = formatRupiah(grandTotal);
    
    const elmTotalItem = document.querySelector('.item-count span');
    if (elmTotalItem) elmTotalItem.innerText = totalItem;

    hitungKembalian();
}

function hitungKembalian() {
    const inputBayar = document.querySelector('.detail-input');
    const elmTotal = document.querySelector('.total-amount');
    const elmKembalian = document.querySelector('.kembalian-amount');

    if (inputBayar && elmTotal && elmKembalian) {
        const totalTagihan = parseRupiah(elmTotal.innerText);
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


//EVENT LISTENER (Interaksi UI)

document.addEventListener('DOMContentLoaded', function() {
    
    // --- Hitung otomatis saat Qty/Bayar diketik ---
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input')) {
            if (e.target.value < 1) e.target.value = 1; 
            hitungTotalTransaksi();
        }
        if (e.target.classList.contains('detail-input')) {
            hitungKembalian();
        }
    });

    // --- Tombol Hapus Baris (Tong Sampah) ---
    const tableBody = document.querySelector('.table-body');
    if (tableBody) {
        tableBody.addEventListener('click', function(e) {
            const btnDelete = e.target.closest('.btn-delete');
            if (btnDelete) {
                btnDelete.closest('.table-row').remove();
                hitungTotalTransaksi();
            }
        });
    }

    // --- Tombol Kosongkan ---
    const btnClear = document.querySelector('.btn-clear');
    if (btnClear && tableBody) {
        btnClear.addEventListener('click', function() {
            if(confirm('Yakin ingin membatalkan semua transaksi di keranjang?')) {
                tableBody.innerHTML = '';
                hitungTotalTransaksi();
            }
        });
    }

    // --- Toggle Metode Pembayaran ---
    const methodBtns = document.querySelectorAll('.method-btn');
    const paymentDetail = document.querySelector('.payment-detail');
    
    methodBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Pindahkan class active
            methodBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Logika UI: Sembunyikan input uang jika bukan TUNAI
            if (this.innerText.includes('TUNAI')) {
                paymentDetail.style.display = 'block';
                hitungKembalian();
            } else {
                paymentDetail.style.display = 'none';
            }
        });
    });

    // --- Tombol Cari / Scan ---
    const btnSearch = document.querySelector('.btn-action-search');
    const inputSearch = document.querySelector('.search-input');
    
    function eksekusiPencarian() {
        const keyword = inputSearch.value.trim();
        if (keyword !== '') {
            // Nanti ini diganti dengan AJAX ke PHP Backend
            alert('Sistem akan mencari barang: ' + keyword);
            inputSearch.value = ''; // Kosongkan setelah cari
        }
    }

    if (btnSearch) btnSearch.addEventListener('click', eksekusiPencarian);
    if (inputSearch) {
        inputSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') eksekusiPencarian();
        });
    }

    // --- Tombol Selesaikan Transaksi ---
    const btnCheckout = document.querySelector('.btn-checkout');
    if (btnCheckout) {
        btnCheckout.addEventListener('click', function() {
            const total = parseRupiah(document.querySelector('.total-amount').innerText);
            
            if (total === 0) {
                alert('Keranjang kosong! Tidak ada yang bisa di-checkout.');
                return;
            }

            // Validasi khusus Tunai
            const isTunai = document.querySelector('.method-btn.active').innerText.includes('TUNAI');
            if (isTunai) {
                const bayar = parseRupiah(document.querySelector('.detail-input').value);
                if (bayar < total) {
                    alert('GAGAL: Nominal uang pelanggan kurang dari total tagihan!');
                    return;
                }
            }

            // Simulasi sukses
            alert('Transaksi Berhasil! Data akan disimpan ke Database.');
            tableBody.innerHTML = ''; // Bersihkan keranjang
            if (document.querySelector('.detail-input')) document.querySelector('.detail-input').value = '';
            hitungTotalTransaksi();
        });
    }
    
    // Inisialisasi awal
    hitungTotalTransaksi();
});