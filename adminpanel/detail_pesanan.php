<?php
require "session.php";
require "../koneksi.php";

if (!isset($_GET['kode'])) {
    header("Location: pesanan.php");
    exit;
}

$kode = mysqli_real_escape_string($con, $_GET['kode']);

// Tarik data pesanan spesifik beserta detail produknya
$query = mysqli_query($con, "
    SELECT ps.*, pr.nama as nama_produk, pr.harga as harga_produk, pr.ketersediaan_stok, pr.foto, kr.nama_kurir 
    FROM pesanan ps
    JOIN produk pr ON ps.id_produk = pr.id
    LEFT JOIN kurir kr ON ps.id_kurir = kr.id_kurir
    WHERE ps.kode_pesanan = '$kode'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data pesanan tidak ditemukan!'); window.location='pesanan.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan | BekasButClassy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .content-box { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="p-4">

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-file-invoice me-2"></i> Detail Pesanan: <b><?= htmlspecialchars($data['kode_pesanan']); ?></b></h4>
        <a href="pesanan.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="content-box h-100">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-user text-primary me-2"></i>Informasi Pelanggan</h5>
                <table class="table table-borderless table-sm">
                    <tr><td width="120" class="text-muted">Nama Pembeli</td><td>: <b><?= htmlspecialchars($data['nama_pembeli']); ?></b></td></tr>
                    <tr><td class="text-muted">No. HP</td><td>: <?= htmlspecialchars($data['no_hp']); ?></td></tr>
                    <tr><td class="text-muted">Status Pesanan</td><td>: <span class="badge bg-dark"><?= strtoupper($data['status_pesanan']); ?></span></td></tr>
                    <tr><td class="text-muted">Nomor Resi</td><td>: <b><?= htmlspecialchars($data['no_resi'] ?? 'Belum ada resi'); ?></b></td></tr>
                </table>

                <h5 class="border-bottom pb-2 mt-4 mb-3"><i class="fas fa-map-marker-alt text-danger me-2"></i>Alamat Pengiriman</h5>
                <p class="mb-0 text-dark">
                    <?= isset($data['alamat']) ? nl2br(htmlspecialchars($data['alamat'])) : '<i class="text-muted">Pelanggan belum mengisi alamat</i>'; ?>
                </p>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="content-box h-100">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-box-open text-success me-2"></i>Detail Produk</h5>
                <div class="d-flex align-items-center mb-3">
                    <?php if ($data['foto'] && file_exists('../uploads/produk/' . $data['foto'])): ?>
                        <img src="../uploads/produk/<?= $data['foto'] ?>" alt="Produk" class="rounded" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd;">
                    <?php else: ?>
                        <div class="bg-light rounded d-flex justify-content-center align-items-center" style="width: 80px; height: 80px; border: 1px solid #ddd;">
                            <i class="fas fa-image text-muted fs-4"></i>
                        </div>
                    <?php endif; ?>
                    <div class="ms-3">
                        <h6 class="mb-1"><?= htmlspecialchars($data['nama_produk']); ?></h6>
                        <small class="text-muted">Stok: <?= htmlspecialchars($data['ketersediaan_stok']); ?></small>
                    </div>
                </div>

                <h5 class="border-bottom pb-2 mt-4 mb-3"><i class="fas fa-money-bill-wave text-warning me-2"></i>Rincian Biaya</h5>
                <table class="table table-borderless table-sm mb-0">
                    <?php
                        $total_bayar = $data['total_harga'];
                        $ongkir = $data['ongkir'];
                        $harga_barang = $total_bayar - $ongkir;
                    ?>
                    <tr><td class="text-muted">Harga Barang</td><td class="text-end">Rp <?= number_format($harga_barang, 0, ',', '.'); ?></td></tr>
                    <tr><td class="text-muted">Ongkos Kirim (<?= htmlspecialchars($data['nama_kurir'] ?? '-'); ?>)</td><td class="text-end">Rp <?= number_format($ongkir, 0, ',', '.'); ?></td></tr>
                    <tr class="border-top"><td class="fw-bold fs-6 pt-2">Total Pembayaran</td><td class="text-end fw-bold text-success fs-6 pt-2">Rp <?= number_format($total_bayar, 0, ',', '.'); ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>