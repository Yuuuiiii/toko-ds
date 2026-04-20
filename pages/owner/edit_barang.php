<?php 
// Simulasi penangkapan parameter SKU dari URL (GET Request)
$sku_edit = isset($_GET['sku']) ? $_GET['sku'] : '';

$page_title = "Edit Data Barang";
require_once '../../includes/header_owner.php'; 
?>

<div class="page-content">
    
    <div style="margin-bottom: 8px;">
        <a href="stok.php" style="color: var(--text-muted); text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="arrow-left" style="width: 16px;"></i> Batal & Kembali ke Stok
        </a>
        <h2 style="font-size: 24px; color: var(--text-primary); margin-top: 12px;">Edit Data Barang</h2>
        <p style="color: var(--text-muted); font-size: 14px;">Mengubah data untuk SKU: <strong><?= htmlspecialchars($sku_edit) ?></strong></p>
    </div>

    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; padding: 32px;">
        <form>
            <input type="hidden" name="id_barang_asli" value="1">

            <div style="margin-bottom: 20px;">
                <label style="display: block; color: var(--text-secondary); font-size: 13px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">Kode SKU / Barcode</label>
                <input type="text" value="<?= htmlspecialchars($sku_edit) ?>" readonly style="width: 100%; height: 48px; padding: 0 16px; background: rgba(255,255,255,0.05); border: 1px solid var(--border); border-radius: 8px; color: var(--text-muted); outline: none; cursor: not-allowed;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; color: var(--text-secondary); font-size: 13px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">Nama Produk</label>
                <input type="text" value="Minyak Goreng Bimoli 2L" style="width: 100%; height: 48px; padding: 0 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--accent); border-radius: 8px; color: var(--text-primary); outline: none; box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.1);">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px;">
                <div>
                    <label style="display: block; color: var(--text-secondary); font-size: 13px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">Harga Jual (Rp)</label>
                    <input type="number" value="35000" style="width: 100%; height: 48px; padding: 0 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); outline: none;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-secondary); font-size: 13px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">Stok Saat Ini</label>
                    <input type="number" value="50" style="width: 100%; height: 48px; padding: 0 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 2; height: 52px; background: var(--accent); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s;">SIMPAN PERUBAHAN</button>
                <button type="button" onclick="history.back()" style="flex: 1; height: 52px; background: transparent; color: var(--text-secondary); border: 1px solid var(--border); border-radius: 8px; font-weight: 600; cursor: pointer;">BATAL</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>