<?php
$page_title = "Manajemen Pengguna";
$current_page = "pengguna"; 
require_once '../../includes/header_owner.php';
?>

<style>
    @keyframes slideDown { to { transform: translateY(0); } }
    select option { background-color: var(--bg-surface); color: white; }
</style>

<div class="page-content" style="padding-bottom: 50px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px;">
        <div class="search-bar-wrap" style="width: 100%; max-width: 1000px; position: relative;">
             <input type="text" id="searchInput" placeholder="Cari nama atau Username..." style="width: 100%; padding: 16px 20px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-surface); color: white; outline: none; box-sizing: border-box; font-family: inherit;">
        </div>
        <button id="btnTambah" style="background: var(--accent); color: white; border: none; padding: 16px 42px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s; white-space: nowrap;">
            <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i> Tambah Pengguna
        </button>
    </div>

    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 12px; overflow-y: auto; overflow-x: auto; max-height: calc(100vh - 200px);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 800px;">
            <thead style="position: sticky; top: 0; background: var(--bg-surface); z-index: 10; box-shadow: 0 1px 0 var(--border);">
                <tr>
                    <th style="padding: 16px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Nama Lengkap</th>
                    <th style="padding: 16px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Username</th>
                    <th style="padding: 16px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Peran Akses</th>
                    <th style="padding: 16px; font-size: 12px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr><td colspan="4" style="text-align: center; padding: 40px; color: var(--text-secondary); font-size: 14px;">Memuat data pengguna...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div id="modalPengguna" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: var(--bg-surface); width: 100%; max-width: 450px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 20px 40px rgba(0,0,0,0.5); overflow: hidden; transform: translateY(-20px); transition: 0.3s; animation: slideDown 0.3s forwards;">
        
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
            <h3 id="modalTitle" style="margin: 0; color: white; font-size: 16px; font-weight: 700;">Tambah Pengguna Baru</h3>
            <button id="btnTutupModal" style="background: transparent; border: none; color: var(--text-secondary); cursor: pointer; transition: 0.2s;"><i data-lucide="x" style="width: 20px; height: 20px;"></i></button>
        </div>

        <form id="formPengguna" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
            <input type="hidden" id="formMode" value="add">
            <input type="hidden" id="inputId">
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Nama Lengkap</label>
                <input type="text" id="inputNama" required placeholder="Contoh: Dewi Lestari" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; box-sizing: border-box; font-family: inherit;">
            </div>
            
            <div style="display: flex; gap: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Username Login</label>
                    <input type="text" id="inputUsername" required placeholder="Contoh: kasir_dewi" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; box-sizing: border-box; font-family: inherit;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Peran / Akses</label>
                    <select id="inputPeran" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; box-sizing: border-box; font-family: inherit; cursor: pointer;">
                        <option value="">-- Pilih --</option>
                        <option value="Admin">Admin (Owner)</option>
                        <option value="Kasir">Kasir</option>
                        <option value="Gudang">Staf Gudang</option>
                    </select>
                </div>
            </div>

            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Password</label>
                <input type="password" id="inputPassword" placeholder="Buat password baru..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-body); color: white; outline: none; box-sizing: border-box; font-family: inherit;">
                <div id="passwordHint" style="font-size: 11px; color: #f59e0b; margin-top: 6px; display: none;">*Kosongkan kolom password ini jika tidak ingin mengubah password lama.</div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border);">
                <button type="button" id="btnBatal" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border); background: transparent; color: var(--text-secondary); font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" id="btnSimpan" style="padding: 10px 20px; border-radius: 8px; border: none; background: var(--accent); color: white; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;"><i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
<script src="../../assets/js/pengguna.js?v=<?= time() ?>"></script>