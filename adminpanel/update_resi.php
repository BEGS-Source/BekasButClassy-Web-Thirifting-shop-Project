<?php
require "session.php";
require "../koneksi.php";
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = $_POST['kode'];
    $no_resi = mysqli_real_escape_string($con, $_POST['no_resi']);
 
    mysqli_query($con, "
        UPDATE pesanan 
        SET no_resi='$no_resi', status_pesanan='dikirim' 
        WHERE kode_pesanan='$kode'
    ");
 
    header("Location: pesanan.php?resi=success");
    exit;
}
?>