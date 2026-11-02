<?php
require "../koneksi.php";

$id   = $_GET['id'];
$aksi = $_GET['aksi'];

// Ambil data penawaran
$data = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT * FROM penawaran WHERE id='$id'
"));

$id_produk = $data['id_produk'];

if ($aksi == 'terima') {

    $nama  = $data['nama_penawar'];
    $no_hp = $data['no_hp'];
    $harga = $data['harga_tawaran'];

    // 1. Update penawaran ini jadi diterima
    mysqli_query($con, "
        UPDATE penawaran 
        SET status_penawaran='diterima' 
        WHERE id='$id'
    ");

    // 2. Tolak semua penawaran lain untuk produk yang sama
    mysqli_query($con, "
        UPDATE penawaran 
        SET status_penawaran='ditolak' 
        WHERE id_produk='$id_produk' AND id!='$id'
    ");

    // 3. Batalkan semua pesanan lama untuk produk ini
    mysqli_query($con, "
        UPDATE pesanan 
        SET status_pesanan='dibatalkan'
        WHERE id_produk='$id_produk'
    ");

    // 4. Cek apakah user ini sudah punya pesanan untuk produk ini
    $cek = mysqli_query($con, "
        SELECT * FROM pesanan 
        WHERE id_produk='$id_produk' 
        AND no_hp='$no_hp'
    ");

    if (mysqli_num_rows($cek) > 0) {

        // Sudah ada → aktifkan kembali pesanannya
        mysqli_query($con, "
            UPDATE pesanan 
            SET status_pesanan='menunggu_konfirmasi',
                total_harga='$harga',
                alamat='-',
                id_kurir=NULL,
                ongkir='0',
                snap_token=NULL
            WHERE id_produk='$id_produk' 
            AND no_hp='$no_hp'
        ");

    } else {

        // Belum ada → buat pesanan baru
        // Pakai id_kurir (integer/NULL), bukan kolom 'kurir' (teks)
        $kode = 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6));

        mysqli_query($con, "
            INSERT INTO pesanan 
            (id_produk, nama_pembeli, no_hp, alamat, id_kurir, ongkir, total_harga, status_pesanan, kode_pesanan, snap_token) 
            VALUES 
            ('$id_produk', '$nama', '$no_hp', '-', NULL, '0', '$harga', 'menunggu_konfirmasi', '$kode', '')
        ");

        // Debug: kalau insert gagal, tampilkan errornya
        if (mysqli_error($con)) {
            die("ERROR INSERT: " . mysqli_error($con));
        }
    }

    // 5. Ubah stok produk jadi Reserved
    mysqli_query($con, "
        UPDATE produk 
        SET ketersediaan_stok='Reserved' 
        WHERE id='$id_produk'
    ");

} elseif ($aksi == 'tolak') {

    mysqli_query($con, "
        UPDATE penawaran 
        SET status_penawaran='ditolak' 
        WHERE id='$id'
    ");
}

header("Location: penawaran.php");