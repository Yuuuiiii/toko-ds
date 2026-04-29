<?php
$page_title = "Kategori Barang";
$current_page = "kategori"; 
require_once '../../includes/header_gudang.php';
?>

<div class="page-content" style="padding-bottom: 50px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px;">
        <div class="search-bar-wrap" style="width: 100%; max-width: 1000px; position: relative;">
             <input type="text" id="searchInput" placeholder="Cari nama kategori..." style="width: 100%; padding: 16px 20px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-surface); color: white; outline: none; box-sizing: border-box; font-family: inherit;">
        </div>
        <button id="btnTambah" style="background: var(--accent); color: white; border: none; padding: 16px 47.3px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s; white-space: nowrap;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Tambah Kategori
        </button>
    </div>

    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; overflow-y: auto; overflow-x: auto; max-height: calc(100vh - 200px);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 600px;">
            <thead style="position: sticky; top: 0; background: var(--bg-surface); z-index: 10; box-shadow: 0 1px 0 var(--border);">
                <tr>
                    <th style="padding: 16px 20px; font-size: 12px; color: var(--text-muted); text-transform: uppercase;">ID Kategori</th>
                    <th style="padding: 16px 20px; font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Nama Kategori</th>
                    <th style="padding: 16px 20px; font-size: 12px; color: var(--text-muted); text-transform: uppercase; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr><td colspan="3" style="text-align: center; padding: 40px; color: var(--text-secondary);">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div id="modalKategori" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: var(--bg-surface); width: 100%; max-width: 400px; border-radius: 12px; border: 1px solid var(--border); padding: 24px;">
        <h3 id="modalTitle" style="margin: 0 0 20px 0; color: white;">Tambah Kategori</h3>
        <form id="formKategori">
            <input type="hidden" id="formMode" value="add">
            <input type="hidden" id="inputId">
            <label style="display: block; margin-bottom: 8px; font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Nama Kategori</label>
            <input type="text" id="inputNama" required placeholder="Contoh: Sembako" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; margin-bottom: 24px; outline: none;">
            
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" id="btnBatal" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border); background: transparent; color: var(--text-secondary); font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" id="btnSimpan" style="padding: 10px 20px; border-radius: 8px; border: none; background: var(--accent); color: white; font-weight: 600; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
<script src="../../assets/js/kategori.js?v=<?= time() ?>"></script>