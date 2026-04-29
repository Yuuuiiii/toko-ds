<?php
$page_title = "Riwayat Barang Masuk";
$current_page = "riwayat_inbound"; 
require_once '../../includes/header_gudang.php';
?>

<div class="page-content" style="padding-bottom: 50px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;">
        <div class="search-bar-wrap" style="flex: 1; min-width: 300px;">
            <input type="text" id="searchInput" placeholder="Cari nama barang, pencatat, supplier, atau keterangan..." style="width: 100%; padding: 16px 20px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-surface); color: white; outline: none; font-family: inherit;">
        </div>
        <button onclick="window.print()" style="background: var(--bg-surface); color: white; border: 1px solid var(--border); padding: 14px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s;">       
            <i data-lucide="printer" style="width: 18px; height: 18px; color: var(--text-secondary);"></i> Cetak Laporan
        </button>
    </div>

    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; overflow-y: auto; overflow-x: auto; max-height: calc(100vh - 180px);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 1000px;">
            <thead style="position: sticky; top: 0; background: var(--bg-surface); z-index: 10; box-shadow: 0 1px 0 var(--border);">
                <tr>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">WAKTU MASUK</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">NAMA BARANG & SKU</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">JUMLAH</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">SUPPLIER ASAL</th>
                    <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">PENCATAT</th>
                     <th style="padding: 16px; font-size: 12px; color: var(--text-secondary); font-weight: 600;">KETERANGAN</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">Memuat riwayat barang masuk...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Sembunyikan elemen selain tabel saat di-print */
    @media print {
        body * { visibility: hidden; }
        .page-content, .page-content * { visibility: visible; }
        .page-content { position: absolute; left: 0; top: 0; width: 100%; }
        .search-bar-wrap, button { display: none !important; }
    }
</style>

<?php require_once '../../includes/footer.php'; ?>
<script src="../../assets/js/riwayat_inbound.js?v=<?= time() ?>"></script>