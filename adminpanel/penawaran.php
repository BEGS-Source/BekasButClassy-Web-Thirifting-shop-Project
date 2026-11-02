<?php
require "session.php";
require "../koneksi.php";

// Nangkep status filter (kalau ada)
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "
    SELECT p.*, pr.nama as nama_produk 
    FROM penawaran p
    JOIN produk pr ON p.id_produk = pr.id
";

// Kalau admin milih status tertentu, tambahin filter WHERE
if ($filter_status != '') {
    $sql .= " WHERE p.status_penawaran = '$filter_status'";
}

$sql .= " ORDER BY p.id DESC";

$query = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penawaran | BekasButClassy</title>
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
        <li class="nav-item"><a href="pesanan.php" class="nav-link "><i class="fas fa-receipt me-2"></i> Pesanan</a></li>
        <li class="nav-item"><a href="penawaran.php" class="nav-link active"><i class="fas fa-hand-holding-usd me-2"></i> Penawaran</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link "><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<div class="topbar d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Penawaran</h5>
    <span>Halo, admin</span>
</div>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📊 Data Penawaran</h4>
        
        <form method="GET" class="d-flex">
            <select name="status" class="form-select shadow-sm" style="border-radius: 10px; cursor: pointer;" onchange="this.form.submit()">
                <option value="">-- Semua Penawaran --</option>
                <option value="menunggu" <?= $filter_status == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                <option value="diterima" <?= $filter_status == 'diterima' ? 'selected' : '' ?>>Diterima</option>
                <option value="ditolak" <?= $filter_status == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
            </select>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(mysqli_num_rows($query) == 0) { ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada penawaran dengan status ini.
                                </td>
                            </tr>
                            <?php } ?>

                        <?php while($data = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($data['nama_produk']); ?></td>
                            <td><?= htmlspecialchars($data['nama_penawar']); ?></td>
                            <td><?= htmlspecialchars($data['no_hp']); ?></td>
                            <td>Rp <?= isset($data['harga_tawaran']) ? number_format($data['harga_tawaran'],0,',','.') : '-' ?></td>
                            
                            <td>
                                <?php
                                    $status = isset($data['status_penawaran']) ? $data['status_penawaran'] : 'menunggu';
                                    $warna = 'secondary';

                                    if ($status == 'menunggu') $warna = 'warning';
                                    if ($status == 'diterima') $warna = 'success';
                                    if ($status == 'ditolak') $warna = 'danger';
                                ?>

                                <span class="badge bg-<?= $warna ?>">
                                    <?= $status ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($status == 'menunggu') { ?>
                                    <a href="aksi_penawaran.php?id=<?= $data['id']; ?>&aksi=terima" class="btn btn-success btn-sm">
                                       ✔ Terima
                                    </a>

                                    <a href="aksi_penawaran.php?id=<?= $data['id']; ?>&aksi=tolak" class="btn btn-danger btn-sm">
                                       ✖ Tolak
                                    </a>
                                <?php } else { ?>
                                    <span class="text-muted small">Sudah direspons</span>
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

<script src="../bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
<script src="../fontawesome/js/all.min.js"></script>

</body>
</html>