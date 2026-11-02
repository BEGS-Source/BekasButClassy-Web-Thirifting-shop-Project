<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
require "session.php";
require "../koneksi.php";
 
$id = $_GET['id'];
$query = mysqli_query($con, "SELECT * FROM kategori WHERE id=$id");
$data = mysqli_fetch_assoc($query);
 
$pesan = '';
 
if (isset($_POST['edit_kategori'])) {
    $kategori_baru = htmlspecialchars(trim($_POST['kategori']));
 
    if ($kategori_baru == '') {
        $pesan = '<div class="alert alert-danger mt-3">Kategori tidak boleh kosong.</div>';
    } else {
        // Cek apakah nama sudah digunakan kategori lain
        $cekDuplikat = mysqli_query($con, "SELECT * FROM kategori WHERE nama='$kategori_baru' AND id != $id");
 
        if (mysqli_num_rows($cekDuplikat) > 0) {
            $pesan = '<div class="alert alert-warning mt-3">Nama kategori sudah digunakan!</div>';
        } else {
            $update = mysqli_query($con, "UPDATE kategori SET nama='$kategori_baru' WHERE id=$id");
 
            if ($update) {
                $pesan = '<div class="alert alert-success mt-3">Kategori berhasil diubah.</div>';
                echo '<meta http-equiv="refresh" content="1; url=kategori.php">';
            } else {
                $pesan = '<div class="alert alert-danger mt-3">Gagal mengubah kategori.</div>';
            }
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
    <link rel="stylesheet" href="../bootstrap-5.3.5-dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h3>Edit Kategori</h3>
    <form method="POST">
        <div class="mb-3">
            <label for="kategori" class="form-label">Nama Kategori</label>
            <input type="text" name="kategori" id="kategori" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
        </div>
        <button type="submit" name="edit_kategori" class="btn btn-primary">Simpan Perubahan</button>
        <a href="kategori.php" class="btn btn-secondary">Kembali</a>
    </form>
    <?= $pesan ?>
</div>
</body>
</html>