<?php
require "session.php";
require "../koneksi.php";
 
$kode = $_GET['kode'];
mysqli_query($con, "UPDATE pesanan SET status_pesanan='selesai' WHERE kode_pesanan='$kode'");
header("Location: pesanan.php");
exit;
?>