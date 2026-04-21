<?php
// Manggil header untuk halaman owner/admin
require_once '../../includes/header_owner.php'; 
?>

<link rel="stylesheet" href="../../assets/css/owner.css">

<div class="container-supplier">
    <h2>Data Supplier Toko DS</h2>
    <p>Kelola daftar pemasok barang di sini.</p>

    <div class="form-section">
        <h3>Tambah Supplier Baru</h3>
        <form action="../../api/supplier.php" method="POST">
            <div class="form-group">
                <label for="nama_supplier">Nama Supplier:</label>
                <input type="text" id="nama_supplier" name="nama_supplier" placeholder="Masukkan nama perusahaan/orang" required>
            </div>

            <div class="form-group">
                <label for="no_hp">No. Handphone:</label>
                <input type="text" id="no_hp" name="no_hp" pattern="[0-9]{10,13}" title="Masukkan 10-13 digit angka valid" placeholder="Contoh: 081234567890" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat Lengkap:</label>
                <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap..." required></textarea>
            </div>

            <button type="submit" name="submit_supplier" class="btn-submit">Simpan Data</button>
        </form>
    </div>

    <div class="table-section">
        <h3>Daftar Supplier Saat Ini</h3>
        <table width="100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Supplier</th>
                    <th>No. HP</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>PT. Siliwangi Jaya</td>
                    <td>081122223333</td>
                    <td>Jl. Siliwangi, Tasikmalaya</td>
                    <td>
                        <button class="btn-edit">Edit</button>
                        <button class="btn-delete">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
// Manggil footer
require_once '../../includes/footer.php'; 
?>