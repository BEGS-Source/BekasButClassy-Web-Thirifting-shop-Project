<?php
require "session.php";
require "../koneksi.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$query = mysqli_query($con, "SELECT * FROM produk WHERE id=$id");
$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan');window.location='produk.php';</script>";
    exit();
}

// Ambil semua kategori
$queryKategori = mysqli_query($con, "SELECT * FROM kategori");

$message = "";

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim(mysqli_real_escape_string($con, $_POST['nama']));
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $detail = trim(mysqli_real_escape_string($con, $_POST['detail']));
    $stok = $_POST['ketersediaan_stok'];

    if ($nama && $kategori && $harga && $stok) {
        // Jika upload foto baru
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $file_name = $_FILES['foto']['name'];
            $file_tmp = $_FILES['foto']['tmp_name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $new_file_name = uniqid('produk_', true) . "." . $ext;
                $upload_dir = '../uploads/produk/';
                $upload_path = $upload_dir . $new_file_name;

                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // hapus foto lama
                    if ($produk['foto'] && file_exists($upload_dir . $produk['foto'])) {
                        unlink($upload_dir . $produk['foto']);
                    }
                    // update dengan foto baru
                    $sql = "UPDATE produk SET nama='$nama', kategori_id='$kategori', harga='$harga', detail='$detail', foto='$new_file_name', ketersediaan_stok='$stok' WHERE id=$id";
                } else {
                    $message = "<div class='alert alert-danger'>Upload foto gagal.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>Format file tidak didukung.</div>";
            }
        } else {
            // update tanpa ganti foto
            $sql = "UPDATE produk SET nama='$nama', kategori_id='$kategori', harga='$harga', detail='$detail', ketersediaan_stok='$stok' WHERE id=$id";
        }

        if (mysqli_query($con, $sql)) {
            echo "<script>alert('Produk berhasil diperbarui');window.location='produk.php';</script>";
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Gagal memperbarui produk.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Semua field wajib diisi.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="../bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <h3 class="mb-4">Edit Produk</h3>
    <?= $message ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Nama Produk</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($produk['nama']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Kategori</label>
            <select name="kategori" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($k = mysqli_fetch_assoc($queryKategori)) : ?>
                    <option value="<?= $k['id'] ?>" <?= $k['id'] == $produk['kategori_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" value="<?= $produk['harga'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Foto Produk (kosongkan jika tidak ganti)</label>
            <input type="file" name="foto" class="form-control">
            <?php if ($produk['foto']) : ?>
                <img src="../uploads/produk/<?= $produk['foto'] ?>" class="mt-2" style="width:100px;">
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label>Detail</label>
            <textarea name="detail" class="form-control" rows="6"><?= htmlspecialchars($produk['detail']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Ketersediaan Stok</label>
            <select name="ketersediaan_stok" class="form-control" required>
                <option value="Ready" <?= $produk['ketersediaan_stok'] == 'Ready' ? 'selected' : '' ?>>Ready</option>
                <option value="Sold" <?= $produk['ketersediaan_stok'] == 'Sold' ? 'selected' : '' ?>>Sold</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
        <a href="produk.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script src="../bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
<script src="../fontawesome/js/all.min.js"></script>
</body>
</html>
