<?php 
// 1. Panggil Header (Otomatis memuat tag HTML, CSS, dan Bar Navigasi Atas)
require_once '../../includes/header_kasir.php'; 
?>

<div class="col-left">
    
    <div class="search-bar-wrap">
        <div class="search-input-wrap">
            <i data-lucide="search"></i>
            <input type="text" class="search-input" placeholder="Cari Nama Barang atau Scan Barcode/QR...">
        </div>
        <button class="btn-scan">
            <i data-lucide="scan-barcode"></i> Scan
        </button>
    </div>

    <div class="table-card">
        <div class="table-header-row">
            <span>#</span>
            <span>Produk</span>
            <span>Qty</span>
            <span>Harga</span>
            <span>Subtotal</span>
            <span></span>
        </div>
        
        <div class="table-body">
            <div class="table-row">
                <span class="row-num">1</span>
                <div>
                    <div class="row-product-name">Minyak Goreng Bimoli 2L</div>
                    <div class="row-product-sku">SKU: 89999881234</div>
                </div>
                <input type="number" class="qty-input" value="1" min="1">
                <span class="row-price">Rp 35.000</span>
                <span class="row-subtotal">Rp 35.000</span>
                <button class="btn-delete" title="Hapus item">
                    <i data-lucide="trash-2" class="icon-sm"></i>
                </button>
            </div>
            
            </div>

        <div class="table-footer">
            <div class="item-count">Total item: <span>1</span></div>
            <button class="btn-clear">
                <i data-lucide="trash"></i> Kosongkan
            </button>
        </div>
    </div>
</div>

<div class="col-right">
    <div class="payment-panel">
        
        <div class="total-section">
            <div class="total-label">TOTAL TAGIHAN</div>
            <div class="total-amount">Rp 35.000</div>
        </div>

        <div class="method-section">
            <div class="method-label">METODE PEMBAYARAN</div>
            <div class="method-toggle">
                <button class="method-btn active">
                    <i data-lucide="banknote"></i>
                    TUNAI
                </button>
                <button class="method-btn">
                    <i data-lucide="qr-code"></i>
                    QRIS
                </button>
                <button class="method-btn">
                    <i data-lucide="credit-card"></i>
                    DEBIT/EDC
                </button>
            </div>
        </div>

        <div class="payment-detail">
            <div class="detail-input-wrap">
                <span class="detail-input-label">Nominal Uang Diterima</span>
                <input type="text" class="detail-input" placeholder="Contoh: 50000" value="50000">
            </div>
            
            <div class="kembalian-box">
                <span class="kembalian-label">Kembalian</span>
                <span class="kembalian-amount">Rp 15.000</span>
            </div>
        </div>

        <button class="btn-checkout">
            SELESAIKAN TRANSAKSI
        </button>
    </div>
</div>

<?php 
// 2. Panggil Footer (Menutup struktur main, HTML, dan memuat script JS)
require_once '../../includes/footer.php'; 
?>