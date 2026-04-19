function formatRupiah(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

// Fungsi membersihkan format Rupiah untuk kalkulasi (Rp 35.000 -> 35000)
function parseRupiah(text) {
    return parseInt(text.replace(/[^0-9]/g, '')) || 0;
}

// Fungsi utama kalkulasi ulang seluruh tabel
function hitungTotalTransaksi() {
    let grandTotal = 0;
    const barisBarang = document.querySelectorAll('.table-row'); // Asumsi row pakai class .table-row

    barisBarang.forEach(baris => {
        const inputQty = baris.querySelector('.qty-input');
        const elmHarga = baris.querySelector('.row-price');
        const elmSubtotal = baris.querySelector('.row-subtotal');

        if (inputQty && elmHarga && elmSubtotal) {
            const qty = parseInt(inputQty.value) || 0;
            const harga = parseRupiah(elmHarga.innerText);
            const subtotal = qty * harga;

            // Update text subtotal di baris tersebut
            elmSubtotal.innerText = formatRupiah(subtotal);
            grandTotal += subtotal;
        }
    });

    // Update Grand Total di panel kanan
    const elmTotalMassive = document.querySelector('.total-amount');
    if (elmTotalMassive) {
        elmTotalMassive.innerText = formatRupiah(grandTotal);
    }
    
    // Trigger hitung kembalian jika metode tunai sedang aktif
    hitungKembalian();
}

// Event Listener untuk semua input Qty
document.addEventListener('input', function(e) {
    if (e.target && e.target.classList.contains('qty-input')) {
        // Cegah input minus
        if (e.target.value < 1) e.target.value = 1; 
        hitungTotalTransaksi();
    }
});

// Fungsi hitung kembalian (Khusus Tunai)
function hitungKembalian() {
    const inputBayar = document.querySelector('.detail-input'); // Class input nominal bayarmu
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

// Pasang event listener ke input bayar tunai
const inputBayar = document.querySelector('.detail-input');
if (inputBayar) {
    inputBayar.addEventListener('input', hitungKembalian);
}