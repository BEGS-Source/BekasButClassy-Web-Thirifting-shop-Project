<?php
require "session.php";
require "../koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | BekasButClassy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="../bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc; /* Warna background utama lebih soft */
            font-family: 'Inter', sans-serif; /* Opsional: pakai font modern */
        }

        /* --- SIDEBAR REVAMP --- */
        .sidebar {
            width: 260px;
            position: fixed;
            height: 94vh; /* Kasih sedikit nafas di bawah */
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
            text-decoration: none; /* Hilangin underline */
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

        /* --- TOPBAR REVAMP (Biar sinkron dengan Sidebar) --- */
        .topbar {
            position: fixed;
            top: 3vh; /* Harus sama dengan top sidebar */
            left: 300px; /* Lebar sidebar (260) + posisi left (20) + gap (20) */
            right: 20px;
            padding: 0 30px;
            height: 70px;
            background: rgba(255, 255, 255, 0.8); /* Efek glassmorphism putih */
            backdrop-filter: blur(10px); /* Efek blur kaca */
            color: #1e293b;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.3);
            z-index: 999;
        }

/* --- CONTENT AREA (PENTING) --- */
        .content {
            margin-left: 300px; /* Sesuaikan dengan posisi topbar/sidebar */
            padding-top: calc(3vh + 90px); /* Space buat topbar */
            padding-right: 25px;
            padding-bottom: 30px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4><i class="fas fa-store me-2"></i>Thrift Admin</h4>
    <hr class="text-white">
    <ul class="nav flex-column">
        <li class="nav-item"><a href="index.php" class="nav-link active"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a href="kategori.php" class="nav-link "><i class="fas fa-list me-2"></i> Kategori</a></li>
        <li class="nav-item"><a href="produk.php" class="nav-link "><i class="fas fa-box-open me-2"></i> Produk</a></li>
        <li class="nav-item"><a href="pesanan.php" class="nav-link "><i class="fas fa-receipt me-2"></i> Pesanan</a></li>
        <li class="nav-item"><a href="penawaran.php" class="nav-link "><i class="fas fa-hand-holding-usd me-2"></i> Penawaran</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link "><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<!-- Topbar -->
<div class="topbar d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Dashboard</h5>
    <div>
        <span class="me-3">Halo, admin</span>
    </div>
</div>

<!-- Konten -->
<div class="content">
    <h4>Selamat datang di halaman admin <b>BekasButClassy</b>!</h4>
    <p class="mt-2 text-muted">Pantau ringkasan data thrift shop Anda hari ini.</p>

    <div class="row mt-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-list fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <?php $jumlahKategori = mysqli_num_rows(mysqli_query($con, "SELECT * FROM kategori")); ?>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">Total Kategori</p>
                        <h4 class="mb-0 fw-bold"><?= $jumlahKategori ?></h4>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-3 pt-0">
                    <a href="kategori.php" class="text-decoration-none text-primary" style="font-size: 0.85rem;">Kelola Kategori <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-box-open fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <?php $jumlahProduk = mysqli_num_rows(mysqli_query($con, "SELECT * FROM produk")); ?>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">Total Produk</p>
                        <h4 class="mb-0 fw-bold"><?= $jumlahProduk ?></h4>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-3 pt-0">
                    <a href="produk.php" class="text-decoration-none text-success" style="font-size: 0.85rem;">Kelola Produk <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-receipt fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <?php $jumlahPesanan = mysqli_num_rows(mysqli_query($con, "SELECT * FROM pesanan")); ?>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">Total Pesanan</p>
                        <h4 class="mb-0 fw-bold"><?= $jumlahPesanan ?></h4>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-3 pt-0">
                    <a href="pesanan.php" class="text-decoration-none text-warning" style="font-size: 0.85rem;">Cek Pesanan <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-hand-holding-usd fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <?php $jumlahPenawaran = mysqli_num_rows(mysqli_query($con, "SELECT * FROM penawaran")); ?>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">Total Penawaran</p>
                        <h4 class="mb-0 fw-bold"><?= $jumlahPenawaran ?></h4>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-3 pt-0">
                    <a href="penawaran.php" class="text-decoration-none text-danger" style="font-size: 0.85rem;">Cek Penawaran <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="../bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
<script src="../fontawesome/js/all.min.js"></script>
</body>
</html>


