<?php
require "session.php";
require "../koneksi.php";

$id = $_GET['id'];

// Cek apakah kategori masih digunakan di tabel produk
$cekProduk = mysqli_query($con, "SELECT COUNT(*) AS total FROM produk WHERE kategori_id = '$id'");
$dataCek = mysqli_fetch_assoc($cekProduk);

if ($dataCek['total'] > 0) {
    // Jika kategori masih digunakan, tampilkan pesan error dan jangan hapus
    echo "<script>
        alert('Kategori tidak dapat dihapus karena masih digunakan oleh produk.');
        window.location='kategori.php';
    </script>";
    exit;
}

// Jika tidak digunakan, lanjut hapus
$query = mysqli_query($con, "DELETE FROM kategori WHERE id='$id'");

if ($query) {
    echo "<script>
        alert('Kategori berhasil dihapus');
        window.location='kategori.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal menghapus kategori');
        window.location='kategori.php';
    </script>";
}
?>
