<?php
require "session.php";
require "../koneksi.php";

$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "
    SELECT ps.*, pr.nama as nama_produk, kr.nama_kurir 
    FROM pesanan ps
    JOIN produk pr ON ps.id_produk = pr.id
    LEFT JOIN kurir kr ON ps.id_kurir = kr.id_kurir
";

if ($filter_status != '') {
    $sql .= " WHERE ps.status_pesanan = '$filter_status'";
}

$sql .= " ORDER BY ps.id_pesanan DESC";
$query = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan | BekasButClassy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            width: 260px;
            position: fixed;
            height: 94vh;
            top: 3vh;
            left: 20px;
            background: linear-gradient(165deg, #1e293b 0%, #0f172a 100%);
            padding: 30px 20px;
            color: white;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar h4 {
            font-weight: 800;
            font-size: 1.2rem;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 40px;
            padding-left: 15px;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            border-radius: 14px;
            padding: 12px 18px;
            margin-bottom: 8px;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #60a5fa;
            padding-left: 25px;
        }

        .sidebar .nav-link.active {
            background: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
            font-weight: 600;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .topbar {
            position: fixed;
            top: 3vh;
            left: 300px;
            right: 20px;
            padding: 0 30px;
            height: 70px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            color: #1e293b;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.3);
            z-index: 999;
        }
         .content {
            margin-left: 300px;
            padding-top: calc(3vh + 90px);
            padding-right: 25px;
            padding-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4><i class="fas fa-store me-2"></i>Thrift Admin</h4>
    <hr class="text-white">
    <ul class="nav flex-column">
        <li class="nav-item"><a href="index.php" class="nav-link "><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a href="kategori.php" class="nav-link "><i class="fas fa-list me-2"></i> Kategori</a></li>
        <li class="nav-item"><a href="produk.php" class="nav-link "><i class="fas fa-box-open me-2"></i> Produk</a></li>
        <li class="nav-item"><a href="pesanan.php" class="nav-link active "><i class="fas fa-receipt me-2"></i> Pesanan</a></li>
        <li class="nav-item"><a href="penawaran.php" class="nav-link "><i class="fas fa-hand-holding-usd me-2"></i> Penawaran</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link "><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<div class="topbar">
    <h5 class="mb-0">Pesanan</h5><span>Halo, admin</span>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📦 Data Pesanan</h4>
        <form method="GET" class="d-flex">
            <select name="status" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">-- Semua Pesanan --</option>
                <option value="menunggu_konfirmasi" <?= $filter_status == 'menunggu_konfirmasi' ? 'selected' : '' ?>>Menunggu Konfirmasi Alamat</option>
                <option value="menunggu_pembayaran" <?= $filter_status == 'menunggu_pembayaran' ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                <option value="diproses" <?= $filter_status == 'diproses' ? 'selected' : '' ?>>Diproses (Lunas)</option>
                <option value="dikirim" <?= $filter_status == 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                <option value="selesai" <?= $filter_status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                <option value="dibatalkan" <?= $filter_status == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
            </select>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Pembeli</th>
                            <th>Status</th>
                            <th>Harga Barang</th>
                            <th>Ongkir</th>
                            <th>Total Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($data = mysqli_fetch_assoc($query)) { 
                            $status = $data['status_pesanan'] ?? '-';
                            $warna = 'secondary';
                            if ($status == 'menunggu_konfirmasi') $warna = 'warning';
                            if ($status == 'menunggu_pembayaran') $warna = 'info';
                            if ($status == 'diproses') $warna = 'primary';
                            if ($status == 'dikirim') $warna = 'dark';
                            if ($status == 'selesai') $warna = 'success';
                            if ($status == 'dibatalkan') $warna = 'danger';
                            
                            // LOGIKA MATEMATIKA YANG BENAR
                            $total_bayar = $data['total_harga'];
                            $ongkir = $data['ongkir'];
                            $harga_barang = $total_bayar - $ongkir;
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($data['kode_pesanan']); ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($data['nama_produk']); ?></small>
                                    </div>
                                    <a href="detail_pesanan.php?kode=<?= $data['kode_pesanan']; ?>" class="btn btn-info btn-sm text-white px-2 py-1 ms-2 shadow-sm" title="Lihat Detail Pesanan">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <?= htmlspecialchars($data['nama_pembeli']); ?><br>
                                <small class="text-muted"><?= htmlspecialchars($data['no_hp']); ?></small>
                            </td>
                            <td><span class="badge bg-<?= $warna ?>"><?= $status ?></span></td>
                            
                            <td>Rp <?= number_format($harga_barang, 0, ',', '.'); ?></td>
                            <td>Rp <?= number_format($ongkir, 0, ',', '.'); ?>
                                <span class="badge bg-secondary"><?= htmlspecialchars($data['nama_kurir'] ?? '-'); ?></span>
                            </td>
                            
                            <td class="fw-bold text-success">Rp <?= number_format($total_bayar, 0, ',', '.'); ?></td>
                            
                            <td>
                                <?php if($status == 'menunggu_konfirmasi') { ?>
                                    <span class="text-warning small"><i class="fas fa-clock"></i> Nunggu User Isi Alamat</span>
                                <?php } elseif ($status == 'menunggu_pembayaran') { ?>
                                    <span class="text-info small"><i class="fas fa-clock"></i> Nunggu Dibayar (Midtrans)</span>
                                <?php } elseif ($status == 'diproses') { ?>
                                    <form method="POST" action="update_resi.php">
                                        <input type="hidden" name="kode" value="<?= $data['kode_pesanan']; ?>">
                                        <input type="text" name="no_resi" placeholder="Input Resi" class="form-control mb-1" required>
                                        <button class="btn btn-warning btn-sm w-100">Kirim Paket</button>
                                    </form>
                                <?php } elseif ($status == 'dikirim') { ?>
                                    <a href="selesai.php?kode=<?= $data['kode_pesanan']; ?>" class="btn btn-success btn-sm w-100" onclick="return confirm('Yakin pesanan selesai?')">Selesai</a>
                                    <small class="text-muted d-block mt-1">Resi: <?= htmlspecialchars($data['no_resi']); ?></small>
                                <?php } else { ?>
                                    <span class="text-muted"><i class="fas fa-check"></i> Selesai/Batal</span>
                                <?php } ?>

                                <?php if($status == 'menunggu_konfirmasi' || $status == 'menunggu_pembayaran') { ?>
                                    <a href="batal_pesanan.php?kode=<?= $data['kode_pesanan']; ?>" class="btn btn-outline-danger btn-sm w-100 mt-2" onclick="return confirm('Batalkan pesanan?')">✖ Batal</a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>