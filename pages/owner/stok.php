<?php 
// 1. Tentukan judul halaman sebelum memanggil header
$page_title = "Manajemen Stok Barang";

// 2. Panggil Header Owner
require_once '../../includes/header_owner.php'; 
?>

<div class="page-content">
    
    <div style="margin-bottom: 8px; width: 100%;">
        <a href="tambah_barang.php" style="text-decoration: none;">
            <button style="width: 100%; height: 56px; border-radius: 8px; background: var(--accent); color: white; border: none; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: all 0.2s ease;">
                <i data-lucide="plus" style="width: 20px; height: 20px;"></i>
                TAMBAH BARANG BARU
            </button>
        </a>
    </div>

    <div class="table-card" style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <div style="padding: 16px; border-bottom: 1px solid var(--border); display: flex; gap: 12px;">
            <div style="flex: 1; position: relative; display: flex; align-items: center;">
                <i data-lucide="search" style="position: absolute; left: 16px; color: var(--text-muted); width: 18px; height: 18px; pointer-events: none; z-index: 10;"></i>
                
                <input type="text" placeholder="Cari SKU atau Nama Barang..." style="width: 100%; height: 40px; padding: 0 16px 0 48px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); font-family: 'Inter', sans-serif; outline: none; transition: all 0.2s ease;">
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255,255,255,0.01); border-bottom: 1px solid var(--border);">
                    <th style="padding: 16px; color: var(--text-secondary); font-size: 12px; text-transform: uppercase; font-weight: 600;">SKU</th>
                    <th style="padding: 16px; color: var(--text-secondary); font-size: 12px; text-transform: uppercase; font-weight: 600;">Nama Barang</th>
                    <th style="padding: 16px; color: var(--text-secondary); font-size: 12px; text-transform: uppercase; font-weight: 600;">Harga Jual</th>
                    <th style="padding: 16px; color: var(--text-secondary); font-size: 12px; text-transform: uppercase; font-weight: 600;">Stok Saat Ini</th>
                    <th style="padding: 16px; color: var(--text-secondary); font-size: 12px; text-transform: uppercase; font-weight: 600; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 16px; color: var(--text-primary); font-family: monospace;">89999881234</td>
                    <td style="padding: 16px; color: var(--text-primary); font-weight: 500;">Minyak Goreng Bimoli 2L</td>
                    <td style="padding: 16px; color: var(--text-primary);">Rp 35.000</td>
                    <td style="padding: 16px;">
                        <span style="background: rgba(34, 200, 122, 0.1); color: var(--success); padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 13px;">50 Unit</span>
                    </td>
                    
                    <td style="padding: 16px; text-align: center; display: flex; gap: 8px; justify-content: center;">
                        <a href="edit_barang.php?sku=89999881234" class="btn-icon-action" style="background: transparent; border: none; padding: 0; cursor: pointer; color: var(--accent); display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; transition: all 0.2s ease; text-decoration: none;">
                            <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                        </a>
                        <button class="btn-icon-action" style="background: transparent; border: none; padding: 0; cursor: pointer; color: var(--danger); display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; transition: all 0.2s ease;" onclick="alert('Konfirmasi Hapus SKU 89999881234')">
                            <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                        </button>
                    </td>
                </tr>
                
                <?php for ($i = 0; $i < 4; $i++): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 16px; color: var(--text-muted); font-family: monospace;">---</td>
                    <td style="padding: 16px; color: var(--text-muted);">Contoh Produk (Empty Row)</td>
                    <td style="padding: 16px; color: var(--text-muted);">Rp 0</td>
                    <td style="padding: 16px;"><span style="background: rgba(255,255,255,0.03); color: var(--text-muted); padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 13px;">-- Unit</span></td>
                    <td style="padding: 16px;"></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
require_once '../../includes/footer.php'; 
?>

<script>
    // Memaksa Lucide untuk me-render ikon setelah elemen HTML dimuat
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>