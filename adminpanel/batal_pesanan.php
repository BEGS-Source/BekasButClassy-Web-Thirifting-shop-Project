<?php
require "session.php";
require "../koneksi.php";
 
$kode = $_GET['kode'];
 
// 1. Cari ID produknya
$query_pesanan = mysqli_query($con, "SELECT id_produk FROM pesanan WHERE kode_pesanan='$kode'");
$data_pesanan = mysqli_fetch_assoc($query_pesanan);
 
if ($data_pesanan) {
    $id_produk = $data_pesanan['id_produk'];
 
    // 2. Balikin stok jadi Ready
    mysqli_query($con, "UPDATE produk SET ketersediaan_stok='Ready' WHERE id='$id_produk'");
 
    // 3. Batalkan pesanan
    mysqli_query($con, "UPDATE pesanan SET status_pesanan='dibatalkan' WHERE kode_pesanan='$kode'");
}
 
header("Location: pesanan.php");
exit;
?>