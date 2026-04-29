<?php 
// Definisikan dulu nama halamannya biar PHP nggak bingung!
$page_title = "Kasir"; 
$current_page = "transaksi";

require_once '../../includes/header_kasir.php'; 
?>

<style>
    /* ==========================================
       CSS KHUSUS UNTUK PRINTER STRUK THERMAL 
       ========================================== */
    @media print {
        body * { visibility: hidden; }
        #printArea, #printArea * { visibility: visible; }
        #printArea {
            position: absolute; left: 0; top: 0; 
            width: 58mm; 
            padding: 0; margin: 0; 
            background: white; color: black;
            font-family: 'Courier New', Courier, monospace; 
            font-size: 12px; line-height: 1.2;
        }
        @page { margin: 0; }
    }
    #printArea { display: none; }
    @media print { #printArea { display: block; } }

    /* CSS Untuk Modal Pop-Up */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
    .modal-box { background: var(--bg-surface); width: 100%; max-width: 500px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 20px 40px rgba(0,0,0,0.5); overflow: hidden; animation: slideDown 0.3s forwards; }
    .modal-box.large { max-width: 800px; }
    .modal-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 20px; max-height: 60vh; overflow-y: auto; }
    .modal-footer { padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px; }
    @keyframes slideDown { from { transform: translateY(-20px); opacity: 0;} to { transform: translateY(0); opacity: 1;} }

    /* ==========================================
       OPTIMASI SPASI PANEL KANAN (UX KASIR)
       ========================================== */
    .method-btn { padding: 8px 4px !important; font-size: 11px !important; height: auto !important; min-height: 55px !important; }
    .method-btn svg { width: 18px !important; height: 18px !important; margin-bottom: 4px !important; }
    .payment-panel { padding: 24px !important; display: flex; flex-direction: column; gap: 10px !important; }
    .total-section { padding-bottom: 10px !important; margin-bottom: 0 !important; }
    .total-label { font-size: 10px !important; margin-bottom: 2px !important; }
    .method-section { margin-bottom: 0 !important; }
    .method-label { font-size: 14px !important; margin-bottom: 6px !important; }
    .detail-input-wrap { margin-bottom: 8px !important; }
    .detail-input { padding: 16px 12px !important; height: auto !important; font-size: 12px !important; }
    .kembalian-box { padding: 8px 12px !important; margin-bottom: 0 !important; }
    .btn-checkout { padding: 12px !important; font-size: 13px !important; margin-top: 4px !important; }
</style>

<div class="col-left">
    <div class="search-bar-wrap" style="position: relative;">
        <div class="search-input-wrap">
            <i data-lucide="search" class="icon-search"></i>
            <input type="text" class="search-input" id="searchInput" placeholder="Ketik nama barang atau SKU/Scan Barcode...">
        </div>
        <div id="searchResults" class="search-results-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--bg-surface, #1e293b); border: 1px solid var(--border, #334155); border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.6); z-index: 999; max-height: 350px; overflow-y: auto; margin-top: 8px;"></div>
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
        
        <div class="table-body" id="cartBody"></div>

        <div class="table-footer">
            <div class="item-count">Total item: <span>0</span></div>
            <button class="btn-clear">
                <i data-lucide="trash"></i> Kosongkan
            </button>
        </div>
    </div>
</div>

<div class="col-right">
    <div style="display: flex; justify-content: center; align-items: center; padding-top: 16px; border-top: 1px solid var(--border); margin-bottom: 4px;">            
        <button id="btnRiwayat" style="background: rgba(67, 97, 238, 0.1); color: #4361ee; border: 1px solid rgba(67, 97, 238, 0.3); padding: 8px 24px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 12px; transition: 0.2s;">
            <i data-lucide="receipt" style="width:16px;height:16px;"></i> Riwayat & Cetak Ulang
        </button>
    </div>

    <div style="background: var(--bg-surface); padding: 12px; border-radius: 12px; border: 1px solid var(--border); margin-left: 18px; margin-right: 18px; margin-top: 18px; margin-bottom: 2px;">
        <div style="font-size: 10px; color: var(--text-secondary); font-weight: 700; margin-bottom: 8px; text-transform: uppercase;">
            Pelanggan / Member <span style="color: var(--success);">(Diskon 10%)</span>
        </div>
        
        <div id="formCariMember" style="display: flex; gap: 8px;">
            <input type="number" id="inputHpMember" placeholder="Ketik No. HP..." style="flex: 1; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; font-size: 12px;">
            <button id="btnCariMember" style="background: var(--accent); color: white; border: none; padding: 0 16px; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer; transition: 0.2s;">Cari</button>
        </div>

        <div id="infoMemberAktif" style="display: none; align-items: center; justify-content: space-between; background: rgba(16, 185, 129, 0.1); padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(16, 185, 129, 0.2);">
            <div>
                <div style="font-size: 12px; font-weight: 700; color: var(--success);" id="namaMemberUI">Budi Santoso</div>
                <div style="font-size: 10px; color: var(--text-secondary);" id="hpMemberUI">08123456789</div>
            </div>
            <button id="btnHapusMember" style="background: transparent; color: var(--danger); border: none; font-size: 11px; font-weight: 700; cursor: pointer;">Batalkan</button>
        </div>
    </div>

    <div class="payment-panel">
        <div class="total-section">
            <div class="total-label">TOTAL TAGIHAN</div>
            <div class="total-amount">Rp 0</div>
        </div>

        <div class="method-section">
            <div class="method-label">METODE PEMBAYARAN</div>
            <div class="method-toggle">
                <button class="method-btn active"><i data-lucide="banknote"></i>TUNAI</button>
                <button class="method-btn"><i data-lucide="qr-code"></i>QRIS</button>
                <button class="method-btn"><i data-lucide="credit-card"></i>DEBIT/EDC</button>
            </div>
        </div>

        <div class="payment-detail">
            <div class="detail-input-wrap">
                <span class="detail-input-label">Nominal Uang Diterima</span>
                <input type="text" class="detail-input" placeholder="Contoh: 50.000">
            </div>
            <div class="kembalian-box">
                <span class="kembalian-label">Kembalian</span>
                <span class="kembalian-amount">Rp 0</span>
            </div>
        </div>

        <button class="btn-checkout">SELESAIKAN TRANSAKSI</button>
    </div>
</div>

<div id="printArea">
    <div style="text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 4px;">TOKO DS</div>
    <div style="text-align: center; font-size: 10px; margin-bottom: 8px;">Jl. Cerdas Berkarya No. 1<br>Telp: 0812-3456-7890</div>
    <div style="border-bottom: 1px dashed #000; margin-bottom: 6px;"></div>
    
    <div style="font-size: 10px; display: flex; justify-content: space-between; margin-bottom: 2px;">
        <span>Trx: <span id="strukTrx"></span></span>
        <span id="strukDate"></span>
    </div>
    <div style="font-size: 10px; margin-bottom: 6px;">Kasir: <span id="strukKasir"></span></div>
    <div style="border-bottom: 1px dashed #000; margin-bottom: 6px;"></div>
    
    <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
        <tbody id="strukItems"></tbody>
    </table>
    
    <div style="border-bottom: 1px dashed #000; margin-top: 6px; margin-bottom: 6px;"></div>
    
    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 12px; margin-bottom: 4px;">
        <span>TOTAL</span>
        <span id="strukTotal"></span>
    </div>
    <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 2px;">
        <span>BAYAR (<span id="strukMetode"></span>)</span>
        <span id="strukBayar"></span>
    </div>
    <div style="display: flex; justify-content: space-between; font-size: 11px;">
        <span>KEMBALI</span>
        <span id="strukKembali"></span>
    </div>
    
    <div style="border-bottom: 1px dashed #000; margin-top: 8px; margin-bottom: 8px;"></div>
    <div style="text-align: center; font-size: 10px;">Terima Kasih!<br>Barang yang sudah dibeli<br>tidak dapat ditukar/dikembalikan.</div>
</div>

<div id="modalSukses" class="modal-overlay">
    <div class="modal-box" style="text-align: center;">
        <div class="modal-body" style="padding: 40px 20px;">
            <div style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i data-lucide="check-circle" style="width: 36px; height: 36px;"></i>
            </div>
            <h2 style="color: var(--text-primary); margin-bottom: 8px;">Transaksi Berhasil!</h2>
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 24px;">Pembayaran telah diterima dan stok berhasil diperbarui.</p>
            
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button id="btnTransaksiBaru" style="background: transparent; color: var(--text-primary); border: 1px solid var(--border); padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600;">Transaksi Baru</button>
                <button id="btnCetakStruk" style="background: var(--success); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 700; display: flex; align-items: center; gap: 8px;"><i data-lucide="printer"></i> Cetak Struk</button>
            </div>
        </div>
    </div>
</div>

<div id="modalRiwayat" class="modal-overlay">
    <div class="modal-box large">
        <div class="modal-header">
            <h3 style="margin: 0; color: var(--text-primary);">Riwayat Transaksi Terakhir</h3>
            <button onclick="document.getElementById('modalRiwayat').style.display='none'" style="background: transparent; border: none; color: var(--text-secondary); cursor: pointer;"><i data-lucide="x"></i></button>
        </div>
        <div class="modal-body">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="border-bottom: 1px solid var(--border);">
                    <tr>
                        <th style="padding: 12px; font-size: 12px; color: var(--text-secondary);">ID PENJUALAN</th>
                        <th style="padding: 12px; font-size: 12px; color: var(--text-secondary);">WAKTU</th>
                        <th style="padding: 12px; font-size: 12px; color: var(--text-secondary);">METODE</th>
                        <th style="padding: 12px; font-size: 12px; color: var(--text-secondary); text-align: right;">TOTAL</th>
                        <th style="padding: 12px; font-size: 12px; color: var(--text-secondary); text-align: right;">AKSI</th>
                    </tr>
                </thead>
                <tbody id="riwayatBody"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalShift" class="modal-overlay">
    <div class="modal-box" style="max-width: 450px;">
        <div class="modal-header">
            <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="calculator" style="color: var(--accent); width: 20px; height: 20px;"></i> Rekap & Tutup Shift
            </h3>
            <button onclick="document.getElementById('modalShift').style.display='none'" style="background: transparent; border: none; color: var(--text-secondary); cursor: pointer;"><i data-lucide="x"></i></button>
        </div>
        <div class="modal-body">
            <div style="background: rgba(67, 97, 238, 0.1); border: 1px solid rgba(67, 97, 238, 0.2); padding: 16px; border-radius: 8px; margin-bottom: 16px; text-align: center;">
                <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 4px; font-weight: 700; text-transform: uppercase;">Total Penerimaan Tunai Sistem</div>
                <div id="shiftSistemUI" style="font-size: 28px; font-weight: 800; color: var(--text-primary);">Rp 0</div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase;">Uang Fisik di Laci (Rp)</label>
                <input type="text" id="inputUangFisik" placeholder="Hitung & ketik nominal..." style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; font-size: 18px; font-weight: 800; outline: none; box-sizing: border-box;">
            </div>
            
            <div id="boxSelisih" style="display: none; padding: 12px; border-radius: 8px; text-align: center; margin-bottom: 16px; font-weight: 800; font-size: 14px;"></div>

            <div style="margin-bottom: 8px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase;">Catatan Laporan</label>
                <textarea id="inputCatatanShift" placeholder="Tulis alasan jika uang minus atau lebih..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; font-size: 13px; outline: none; box-sizing: border-box; resize: vertical; min-height: 80px;"></textarea>
            </div>
        </div>
        <div class="modal-footer" style="justify-content: space-between;">
            <button onclick="document.getElementById('modalShift').style.display='none'" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border); background: transparent; color: var(--text-secondary); font-weight: 600; cursor: pointer;">Batal</button>
            <button id="btnProsesShift" style="padding: 10px 20px; border-radius: 8px; border: none; background: var(--danger); color: white; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;"><i data-lucide="log-out" style="width:16px; height:16px;"></i> Simpan & Log Out</button>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
<script src="../../assets/js/kasir.js?v=<?= time() ?>"></script>