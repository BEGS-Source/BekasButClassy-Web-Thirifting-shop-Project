<?php
require "session.php";
require "../koneksi.php";

// Proses tambah kategori
$pesan = '';
if (isset($_POST['simpan_kategori'])) {
    $kategori = trim(htmlspecialchars($_POST['kategori']));
    if ($kategori == '') {
        $pesan = '<div class="alert alert-danger mt-3">Nama kategori tidak boleh kosong.</div>';
    } else {
        $cek = mysqli_query($con, "SELECT * FROM kategori WHERE nama='$kategori'");
        if (mysqli_num_rows($cek) > 0) {
            $pesan = '<div class="alert alert-warning mt-3">Kategori sudah ada!</div>';
        } else {
            $insert = mysqli_query($con, "INSERT INTO kategori (nama) VALUES ('$kategori')");
            if ($insert) {
                $pesan = '<div class="alert alert-success mt-3">Kategori berhasil ditambahkan!</div>';
                echo '<meta http-equiv="refresh" content="1; url=kategori.php">';
            } else {
                $pesan = '<div class="alert alert-danger mt-3">Gagal menambahkan kategori!</div>';
            }
        }
    }
}

// Proses pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$queryKategori = ($search != '')
    ? mysqli_query($con, "SELECT * FROM kategori WHERE nama LIKE '%$search%' ORDER BY id DESC")
    : mysqli_query($con, "SELECT * FROM kategori ORDER BY id DESC");
$jumlahKategori = mysqli_num_rows($queryKategori);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Kelola Kategori | BekasButClassy</title>
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
        <li class="nav-item"><a href="index.php" class="nav-link "><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a href="kategori.php" class="nav-link active"><i class="fas fa-list me-2"></i> Kategori</a></li>
        <li class="nav-item"><a href="produk.php" class="nav-link "><i class="fas fa-box-open me-2"></i> Produk</a></li>
        <li class="nav-item"><a href="pesanan.php" class="nav-link "><i class="fas fa-receipt me-2"></i> Pesanan</a></li>
        <li class="nav-item"><a href="penawaran.php" class="nav-link "><i class="fas fa-hand-holding-usd me-2"></i> Penawaran</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link "><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<!-- Topbar -->
<div class="topbar d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Kategori</h5>
    <div>
        <span class="me-3">Halo, admin</span>
    </div>
</div>

<!-- Konten Utama -->
<div class="content">

    <!-- Tambah Kategori -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>List Kategori</h4>
        <form method="POST" class="d-flex gap-2">
            <input type="text" name="kategori" class="form-control" placeholder="Kategori baru" required>
            <button type="submit" name="simpan_kategori" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
        </form>
    </div>

    <?= $pesan ?>

    <!-- Pencarian -->
    <form method="GET" class="mb-3 d-flex justify-content-end">
        <input type="text" name="search" class="form-control w-25 me-2" placeholder="Cari kategori..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    </form>

    <!-- Tabel Kategori -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Kategori</th>
                    <th style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($jumlahKategori == 0): ?>
                    <tr><td colspan="3" class="text-center">Belum ada kategori.</td></tr>
                <?php else: ?>
                    <?php $no = 1; while ($data = mysqli_fetch_array($queryKategori)) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($data['nama']) ?></td>
                        <td>
                            <a href="edit-kategori.php?id=<?= $data['id'] ?>" class="btn btn-warning btn-sm me-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="hapus-kategori.php?id=<?= $data['id'] ?>" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Script -->
<script src="../bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
<script src="../fontawesome/js/all.min.js"></script>
<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) alert.remove();
    }, 3000);
</script>
</body>
</html>
