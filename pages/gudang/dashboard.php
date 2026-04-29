<?php
$page_title = "Beranda Gudang";
$current_page = "dashboard"; 
require_once '../../includes/header_gudang.php';
?>

<div class="page-content">
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); padding: 24px; border-radius: 12px; margin-bottom: 24px;">
        <h2 style="margin: 0 0 8px 0; color: #10b981;">Selamat Datang di Panel Gudang!</h2>
        <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">Di sini Anda bisa memantau ketersediaan stok, menambahkan barang baru, mencetak label barcode, dan melakukan opname (penyesuaian fisik barang).</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="background: var(--bg-surface); padding: 20px; border-radius: 12px; border: 1px solid var(--border);">
            <div style="font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Total SKU Barang</div>
            <div id="statTotal" style="font-size: 32px; font-weight: 800; color: var(--text-primary);">0</div>
        </div>
        <div style="background: var(--bg-surface); padding: 20px; border-radius: 12px; border: 1px solid rgba(245, 158, 11, 0.3);">
            <div style="font-size: 12px; color: #f59e0b; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Stok Menipis</div>
            <div id="statMenipis" style="font-size: 32px; font-weight: 800; color: #f59e0b;">0</div>
        </div>
        <div style="background: var(--bg-surface); padding: 20px; border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.3);">
            <div style="font-size: 12px; color: #ef4444; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Stok Habis</div>
            <div id="statHabis" style="font-size: 32px; font-weight: 800; color: #ef4444;">0</div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    if (window.lucide) lucide.createIcons();
    
    // Tarik nama user
    if (typeof parseJwt === "function") {
        const jwtData = parseJwt(localStorage.getItem('jwt_token'));
        if (jwtData) {
            document.getElementById('ownerName').innerText = jwtData.nama || jwtData.username;
            document.getElementById('ownerAvatar').innerText = jwtData.username.charAt(0).toUpperCase();
        }
    }

    // Tarik statistik ringkas dari API Barang
    try {
        const res = await fetch('../../api/barang.php', { headers: { 'Authorization': `Bearer ${localStorage.getItem('jwt_token')}` } });
        const json = await res.json();
        if(json.status === 'success') {
            const barang = json.data;
            document.getElementById('statTotal').innerText = barang.length;
            document.getElementById('statMenipis').innerText = barang.filter(b => b.Status_Stok !== 'AMAN' && b.Status_Stok !== 'HABIS').length;
            document.getElementById('statHabis').innerText = barang.filter(b => b.Status_Stok === 'HABIS').length;
        }
    } catch(e) {}
});
</script>