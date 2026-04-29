<?php
$page_title = "Manajemen Stok";
$current_page = "stok"; 
require_once '../../includes/header_gudang.php';
?>

<style>
    @media print {
        body * { visibility: hidden; }
        #printBarcodeArea, #printBarcodeArea * { visibility: visible; }
        #printBarcodeArea {
            position: absolute; left: 0; top: 0; width: 50mm; padding: 2mm; 
            text-align: center; background: white; color: black; font-family: 'Inter', sans-serif;
        }
        @page { margin: 0; }
    }
    #printBarcodeArea { display: none; }
    @media print { #printBarcodeArea { display: block; } }
    @keyframes slideDown { to { transform: translateY(0); } }
    .tab-filter {
        padding: 8px 16px !important; border-radius: 8px !important; border: 1px solid transparent !important;
        background: transparent !important; color: var(--text-secondary) !important;
        cursor: pointer !important; font-weight: 600 !important; font-size: 13px !important; transition: 0.2s !important;
    }
    .tab-filter:hover { background: rgba(255,255,255,0.05) !important; color: var(--text-primary) !important; }
    .tab-filter.active-tab { background: var(--bg-elevated) !important; color: var(--text-primary) !important; border: 1px solid var(--border) !important; }
    .tab-filter.active-tab[data-filter="semua"] { border-bottom: 2px solid var(--accent) !important; }
    .tab-filter.active-tab[data-filter="aman"] { border-bottom: 2px solid #10b981 !important; }
    .tab-filter.active-tab[data-filter="menipis"] { border-bottom: 2px solid #f59e0b !important; }
    .tab-filter.active-tab[data-filter="habis"] { border-bottom: 2px solid #ef4444 !important; }
</style>

<div class="page-content" style="padding-bottom: 50px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;">
        <div class="search-bar-wrap" style="flex: 1; min-width: 300px;">
            <input type="text" id="searchInput" placeholder="Cari SKU atau Nama Barang..." style="width: 100%; padding: 16px 20px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-surface); color: var(--text-primary); outline: none; font-family: inherit;">        </div>
        
        <div style="display: flex; gap: 12px;">
            <button id="btnTambahBarang" style="background: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border); padding: 14px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s;">       
                <i data-lucide="plus-circle" style="width: 18px; height: 18px; color: var(--accent);"></i> Barang Baru
            </button>
            <button id="btnTambahStok" style="background: #10b981; color: white; border: none; padding: 14px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 12px rgba(16,185,129,0.2);">       
                <i data-lucide="download-cloud" style="width: 18px; height: 18px;"></i> Tambah Stok Masuk
            </button>
        </div>
    </div>

    <div style="background: var(--bg-surface); padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; border-right: 1px solid var(--border); padding-right: 16px;">
            <i data-lucide="sliders-horizontal" style="width: 14px; height: 14px; vertical-align:-2px;"></i> Filter
        </div>
        <button class="tab-filter active-tab" data-filter="semua">Semua Stok</button>
        <button class="tab-filter" data-filter="aman"><div style="width:8px; height:8px; border-radius:50%; background:#10b981; display:inline-block;"></div> Aman</button>
        <button class="tab-filter" data-filter="menipis"><div style="width:8px; height:8px; border-radius:50%; background:#f59e0b; display:inline-block;"></div> Menipis</button>
        <button class="tab-filter" data-filter="habis"><div style="width:8px; height:8px; border-radius:50%; background:#ef4444; display:inline-block;"></div> Habis</button>
    </div>

    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; overflow-y: auto; overflow-x: auto; max-height: calc(100vh - 200px);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 900px;">
            <thead style="position: sticky; top: 0; background: var(--bg-surface); z-index: 10; box-shadow: 0 1px 0 var(--border);">
                <tr>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">SKU</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">NAMA BARANG</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">KATEGORI</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">HARGA</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">STOK</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">STATUS</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-align: right;">AKSI</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--text-secondary);">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div id="modalInbound" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: var(--bg-surface); width: 100%; max-width: 450px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 20px 40px rgba(0,0,0,0.5); animation: slideDown 0.3s forwards;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: white; font-size: 16px;">Tambah Stok Masuk</h3>
            <button onclick="document.getElementById('modalInbound').style.display='none'" style="background: transparent; border: none; color: var(--text-secondary); cursor: pointer;"><i data-lucide="x" style="width: 20px;"></i></button>
        </div>
        <form id="formInbound" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Scan Barcode (SKU / Kerdus)</label>
                <input type="text" id="inboundSKU" required placeholder="Masukkan SKU atau scan ke sini..." autocomplete="off" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; font-size: 16px; font-weight: reguler;">
            </div>
            <div style="display: flex; gap: 16px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Jumlah Masuk</label>
                    <input type="number" id="inboundQty" required min="1" value="1" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; font-size: 16px;">
            </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Tanggal Masuk</label>
                    <input type="datetime-local" id="inboundTanggal" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; font-size: 14px; color-scheme: dark;">                </div>
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Supplier Asal (Opsional)</label>
                <select id="inboundSupplier" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; cursor:pointer;">
                    <option value="">-- Non-Supplier / Lainnya --</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 8px; padding-top: 16px; border-top: 1px solid var(--border);">
                <button type="submit" id="btnSimpanInbound" style="padding: 10px 20px; border-radius: 8px; border: none; background: #10b981; color: white; font-weight: 600; cursor: pointer;">Konfirmasi Masuk</button>
            </div>
        </form>
    </div>
</div>

<div id="modalBarang" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: var(--bg-surface); width: 100%; max-width: 500px; border-radius: 12px; border: 1px solid var(--border); animation: slideDown 0.3s forwards;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle" style="margin: 0; color: white; font-size: 16px;">Tambah Barang Baru</h3>
            <button onclick="document.getElementById('modalBarang').style.display='none'" style="background: transparent; border: none; color: var(--text-secondary); cursor: pointer;"><i data-lucide="x" style="width: 20px;"></i></button>
        </div>
        <form id="formBarang" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
            <input type="hidden" id="formMode" value="add">
            <div style="display: flex; gap: 16px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">SKU Barang</label>
                    <input type="text" id="inputSKU" required autocomplete="off" placeholder="Scan Barcode / Ketik" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: var(--text-primary); outline: none;">                            </div>
                <div style="flex: 2;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Nama Barang</label>
<input type="text" id="inputNama" required placeholder="Contoh: Indomie" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: var(--text-primary); outline: none;">                </div>
            </div>
            <div style="display: flex; gap: 16px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Kategori</label>
<select id="inputKategori" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: var(--text-primary); outline: none;">                        <option value="">Memuat...</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Harga Jual (Rp)</label>
<input type="text" id="inputHarga" required placeholder="0" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: var(--text-primary); outline: none; font-weight: bold;">                </div>
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Satuan Dasar (Pcs/Botol/dll)</label>
                <select id="inputSatuan" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: var(--text-primary); outline: none; cursor: pointer;">
                    <option value="">Memuat...</option>
                </select>
            </div>
            <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Supplier Default</label>
                    <select id="inputSupplierMaster" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: var(--text-primary); outline: none; cursor: pointer;">
                        <option value="">-- Pilih Supplier --</option>
                    </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 8px; padding-top: 16px; border-top: 1px solid var(--border);">
                <button type="submit" id="btnSimpan" style="padding: 10px 20px; border-radius: 8px; border: none; background: var(--accent); color: white; font-weight: 600; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="printBarcodeArea">
    <div id="printLabelNama" style="font-size: 12px; font-weight: bold; margin-bottom: 4px;"></div>
    <svg id="printBarcodeCanvas" style="width: 100%; max-height: 60px;"></svg>
    <div id="printLabelHarga" style="font-size: 14px; font-weight: 900; margin-top: 4px;"></div>
</div>

<div id="modalBarcode" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: var(--bg-surface); width: 100%; max-width: 400px; border-radius: 12px; border: 1px solid var(--border); text-align: center;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: var(--text-primary);; font-size: 16px;">Cetak Label</h3>
            <button onclick="document.getElementById('modalBarcode').style.display='none'" style="background: transparent; border: none; color: var(--text-secondary); cursor: pointer;"><i data-lucide="x" style="width: 20px;"></i></button>
        </div>
        <div style="padding: 30px 20px;">
            <div id="barcodeLabelNama" style="color: var(--text-primary); font-size: 16px; font-weight: 700; margin-bottom: 12px;"></div>
            <div style="background: white; padding: 12px; border-radius: 8px; display: inline-block;">
                <svg id="barcodeCanvas"></svg>
            </div>
            <div id="barcodeLabelHarga" style="color: var(--success); font-size: 20px; font-weight: 800; margin-top: 16px;"></div>
        </div>
        <div style="padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: center;">
            <button onclick="window.print()" style="background: var(--accent); color: var(--text-primary);; border: none; padding: 12px 32px; border-radius: 8px; cursor: pointer; font-weight: 700;"><i data-lucide="printer" style="width:18px;"></i> Print Label</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    function formatRupiahGlobal(angka) { return 'Rp ' + parseInt(angka).toLocaleString('id-ID'); }
    window.bukaModalBarcode = function(sku, nama, harga) {
        document.getElementById('modalBarcode').style.display = 'flex';
        document.getElementById('barcodeLabelNama').innerText = nama;
        document.getElementById('barcodeLabelHarga').innerText = formatRupiahGlobal(harga);
        document.getElementById('printLabelNama').innerText = nama;
        document.getElementById('printLabelHarga').innerText = formatRupiahGlobal(harga);
        JsBarcode("#barcodeCanvas", sku, { format: "CODE128", width: 2, height: 60, displayValue: true, fontSize: 16, margin: 0 });
        JsBarcode("#printBarcodeCanvas", sku, { format: "CODE128", width: 2, height: 40, displayValue: true, fontSize: 12, margin: 0 });
    }
</script>

<?php require_once '../../includes/footer.php'; ?>
<script src="../../assets/js/stok.js?v=<?= time() ?>"></script>