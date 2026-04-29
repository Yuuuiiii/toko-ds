<?php
$page_title = "Barang Masuk (Inbound)";
$current_page = "masuk"; 
require_once '../../includes/header_gudang.php';
?>

<div class="page-content" style="padding-bottom: 50px;">
    
    <div style="background: var(--bg-surface); padding: 24px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h3 style="margin: 0 0 20px 0; color: white; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="download-cloud" style="color: #10b981;"></i> Catat Barang Masuk
        </h3>
        
        <form id="formInbound" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
            <div style="grid-column: span 2;">
                <label style="display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">Scan Barcode (Satuan / Kerdus)</label>
                <input type="text" id="inputBarcode" required placeholder="Arahkan scanner ke sini..." autocomplete="off" style="width: 100%; padding: 14px; border-radius: 8px; border: 2px solid var(--border); background: var(--bg-body); color: white; outline: none; font-size: 18px; font-weight: bold; letter-spacing: 1px; transition: 0.2s;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">Supplier / Pabrik</label>
                <select id="inputSupplier" style="width: 100%; padding: 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; cursor: pointer; font-size: 14px;">
                    <option value="">-- Pilih Supplier (Opsional) --</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">Jmlh Scan</label>
                <input type="number" id="inputQty" required min="1" value="1" style="width: 100%; padding: 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; font-size: 16px;">
            </div>

            <div style="grid-column: span 2;">
                <label style="display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">Keterangan</label>
                <input type="text" id="inputKeterangan" placeholder="Contoh: Turun dari mobil box nopol B 1234 CD..." style="width: 100%; padding: 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none;">
            </div>

            <div style="grid-column: span 2; text-align: right; margin-top: 8px;">
                <button type="submit" id="btnSimpan" style="background: #10b981; color: white; border: none; padding: 16px 32px; border-radius: 8px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 15px; box-shadow: 0 4px 12px rgba(16,185,129,0.3); transition: 0.2s;">
                    <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i> Masukkan ke Gudang
                </button>
            </div>
        </form>
    </div>

    <h4 style="color: var(--text-primary); margin-bottom: 12px; font-size: 15px;">Riwayat Barang Masuk (Terbaru)</h4>
    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 900px;">
            <thead style="background: var(--bg-elevated); border-bottom: 1px solid var(--border);">
                <tr>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Waktu Masuk</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-muted); text-transform: uppercase;">SKU / Barang</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Jumlah</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Supplier</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Penerima</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-secondary);">Memuat riwayat...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
<script src="../../assets/js/barang_masuk.js?v=<?= time() ?>"></script>