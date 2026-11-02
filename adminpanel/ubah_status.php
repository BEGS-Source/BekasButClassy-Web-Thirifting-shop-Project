<?php
require "session.php";
require "../koneksi.php";

if (isset($_POST['id_produk']) && isset($_POST['ketersediaan_stok'])) {
    $id = $_POST['id_produk'];
    $status = $_POST['ketersediaan_stok'];

    $update = mysqli_query($con, "UPDATE produk SET ketersediaan_stok='$status' WHERE id='$id'");

    if ($update) {
        header("Location: produk.php");
        exit;
    } else {
        echo "Gagal mengubah status<br>";
        echo "Query error: " . mysqli_error($con) . "<br>";
        echo "ID: $id | Status: $status";
    }
} else {
    echo "POST tidak lengkap.<br>";
    echo "ID Produk: " . ($_POST['id_produk'] ?? 'null') . "<br>";
    echo "Status: " . ($_POST['ketersediaan_stok'] ?? 'null') . "<br>";
}
