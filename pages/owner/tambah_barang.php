<?php 
$page_title = "Tambah Produk Baru";
require_once '../../includes/header_owner.php'; 
?>

<div class="page-content">
    
    <div style="margin-bottom: 8px;">
        <a href="stok.php" style="color: var(--text-muted); text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="arrow-left" style="width: 16px;"></i> Kembali ke Stok
        </a>
        <h2 style="font-size: 24px; color: var(--text-primary); margin-top: 12px;">Tambah Barang Baru</h2>
        <p>ini nanti di sesuaikan dengan db ya tim BE hhehe</p>
    </div>

    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; padding: 32px;">
        <form>
            <div style="margin-bottom: 20px;">
                <label style="display: block; color: var(--text-secondary); font-size: 13px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">Kode SKU / Barcode</label>
                <input type="text" placeholder="Masukkan atau scan barcode..." style="width: 100%; height: 48px; padding: 0 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; color: var(--text-secondary); font-size: 13px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">Nama Produk</label>
                <input type="text" placeholder="Contoh: Minyak Goreng Bimoli 2L" style="width: 100%; height: 48px; padding: 0 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); outline: none;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px;">
                <div>
                    <label style="display: block; color: var(--text-secondary); font-size: 13px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">Harga Jual (Rp)</label>
                    <input type="number" placeholder="0" style="width: 100%; height: 48px; padding: 0 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); outline: none;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-secondary); font-size: 13px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">Stok Awal</label>
                    <input type="number" placeholder="0" style="width: 100%; height: 48px; padding: 0 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 2; height: 52px; background: var(--accent); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">SIMPAN PRODUK</button>
                <button type="button" onclick="history.back()" style="flex: 1; height: 52px; background: transparent; color: var(--text-secondary); border: 1px solid var(--border); border-radius: 8px; font-weight: 600; cursor: pointer;">BATAL</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>