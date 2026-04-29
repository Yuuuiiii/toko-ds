<?php 
$page_title = "Laporan Keuangan & Audit";
$current_page = "laporan";
require_once '../../includes/header_owner.php'; 
?>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; opacity: 0.7; transition: 0.2s; }
    input[type="date"]::-webkit-calendar-picker-indicator:hover { opacity: 1; }
    html[data-theme="light"] input[type="date"]::-webkit-calendar-picker-indicator,
    body.light-mode input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(0); }
    @keyframes slideDown { to { transform: translateY(0); } }
</style>

<div class="page-content" style="padding-bottom: 50px;">

    <div style="background: var(--bg-surface); padding: 20px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px; display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label style="font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Dari Tanggal</label>
            <input type="date" id="filterStart" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: var(--text-primary); outline: none; font-family: inherit;">
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label style="font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Sampai Tanggal</label>
            <input type="date" id="filterEnd" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: var(--text-primary); outline: none; font-family: inherit;">
        </div>

        <button id="btnFilter" style="background: var(--accent); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s;">
            <i data-lucide="filter" style="width: 18px; height: 18px;"></i> Filter
        </button>

        <div style="flex-grow: 1;"></div> 
        
        <button id="btnTambahPengeluaran" style="background: #f59e0b; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);">
            <i data-lucide="minus-circle" style="width: 18px; height: 18px;"></i> Input Pengeluaran
        </button>

        <button id="btnExportPDF" style="background: transparent; color: var(--danger); border: 1px solid var(--danger); padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s;">
            <i data-lucide="file-down" style="width: 18px; height: 18px;"></i> PDF
        </button>
        <button id="btnExportExcel" style="background: transparent; color: var(--success); border: 1px solid var(--success); padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s;">
            <i data-lucide="sheet" style="width: 18px; height: 18px;"></i> Excel
        </button>
    </div>

    <div style="display: flex; gap: 20px; margin-bottom: 24px; flex-wrap: wrap;">
        <div style="background: var(--bg-surface); padding: 20px; border-radius: 12px; border: 1px solid var(--border); flex: 1; min-width: 250px;">
            <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase;">Total Pemasukan (Omzet)</div>
            <div style="font-size: 24px; font-weight: 700; color: var(--success);" id="summaryPendapatan">Rp 0</div>
        </div>
        <div style="background: var(--bg-surface); padding: 20px; border-radius: 12px; border: 1px solid var(--border); flex: 1; min-width: 250px;">
            <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase;">Total Pengeluaran</div>
            <div style="font-size: 24px; font-weight: 700; color: #ef4444;" id="summaryPengeluaran">Rp 0</div>
        </div>
        <div style="background: var(--bg-surface); padding: 20px; border-radius: 12px; border: 2px solid var(--accent); flex: 1; min-width: 250px; box-shadow: 0 4px 15px rgba(67, 97, 238, 0.15);">
            <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase;">Laba Bersih</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-primary);" id="summaryLaba">Rp 0</div>
        </div>
    </div>

    <h4 style="color: var(--text-primary); margin-bottom: 12px;">Data Pemasukan</h4>
    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; overflow-x: auto; overflow-y: auto; min-height: 400px; max-height: 800px; margin-bottom: 30px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 800px;">
            <thead style="position: sticky; top: 0; background: var(--bg-surface); z-index: 10; box-shadow: 0 1px 0 var(--border);">
                <tr>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">ID Penjualan</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Waktu Transaksi</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Kasir</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Metode</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-align: right; text-transform: uppercase;">Tagihan</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-align: right; text-transform: uppercase;">Aksi</th>
                </tr>
            </thead>
            <tbody id="laporan-body">
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>

    <h4 style="color: var(--text-primary); margin-bottom: 12px;">Data Pengeluaran Operasional</h4>
    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; overflow-x: auto; overflow-y: auto; min-height: 300px; max-height: 500px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 800px;">
            <thead style="position: sticky; top: 0; background: var(--bg-surface); z-index: 10; box-shadow: 0 1px 0 var(--border);">
                <tr>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Tanggal</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Keterangan Pengeluaran</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-align: right; text-transform: uppercase;">Nominal Pengeluaran</th>
                </tr>
            </thead>
            <tbody id="pengeluaran-body">
                <tr><td colspan="3" style="text-align: center; padding: 40px; color: var(--text-secondary);">Memuat data pengeluaran...</td></tr>
            </tbody>
        </table>
    </div>

</div>

<div id="modalPengeluaran" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: var(--bg-surface); width: 100%; max-width: 450px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 20px 40px rgba(0,0,0,0.5); animation: slideDown 0.3s forwards;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: white; font-size: 16px;"><i data-lucide="minus-circle" style="color: #f59e0b; vertical-align:-3px; margin-right:4px;"></i> Catat Pengeluaran</h3>
            <button onclick="document.getElementById('modalPengeluaran').style.display='none'" style="background: transparent; border: none; color: var(--text-secondary); cursor: pointer;"><i data-lucide="x" style="width: 20px;"></i></button>
        </div>
        <form id="formPengeluaran" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Tanggal Pengeluaran</label>
                <input type="date" id="inputTglPengeluaran" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Keterangan (Contoh: Bayar Listrik Gudang)</label>
                <input type="text" id="inputKetPengeluaran" required autocomplete="off" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">Nominal (Rp)</label>
                <input type="text" id="inputNominalPengeluaran" required autocomplete="off" placeholder="Contoh: 150.000" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; font-size: 16px; font-weight: bold;">            </div>
            <div style="display: flex; justify-content: flex-end; padding-top: 16px; border-top: 1px solid var(--border);">
                <button type="submit" id="btnSimpanPengeluaran" style="background: #f59e0b; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer;">Simpan Pengeluaran</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDetail" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: var(--bg-surface); width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 20px 40px rgba(0,0,0,0.5); overflow: hidden; animation: slideDown 0.3s forwards;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
            <h3 style="margin: 0; color: var(--text-primary); font-size: 16px; font-weight: 700;">Detail Transaksi: <span id="detailTrxId" style="color: var(--accent);"></span></h3>
            <button onclick="document.getElementById('modalDetail').style.display='none'" style="background: transparent; border: none; color: var(--text-secondary); cursor: pointer;"><i data-lucide="x" style="width: 20px; height: 20px;"></i></button>
        </div>
        <div style="padding: 20px; overflow-y: auto; max-height: 400px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background: var(--bg-body); position: sticky; top: 0;">
                    <tr>
                        <th style="padding: 12px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Barang</th>
                        <th style="padding: 12px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Harga</th>
                        <th style="padding: 12px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Qty</th>
                        <th style="padding: 12px; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="detail-body">
                    <tr><td colspan="4" style="text-align: center; padding: 20px;">Memuat detail...</td></tr>
                </tbody>
                <tfoot style="border-top: 2px solid var(--border);">
                    <tr>
                        <td colspan="3" style="padding: 16px 12px; text-align: right; font-size: 13px; font-weight: 700; color: var(--text-secondary);">TOTAL:</td>
                        <td id="detailTotal" style="padding: 16px 12px; text-align: right; font-size: 15px; font-weight: 800; color: var(--success);">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<?php require_once '../../includes/footer.php'; ?>
<script src="../../assets/js/laporan.js?v=<?= time() ?>"></script>