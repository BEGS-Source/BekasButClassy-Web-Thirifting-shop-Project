<?php
require "session.php";
require "../koneksi.php";

// Proses tambah produk
$message = "";

$editMode = false;
$produkEdit = [];

if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $editQuery = mysqli_query($con, "SELECT * FROM produk WHERE id='$editId'");
    if (mysqli_num_rows($editQuery) > 0) {
        $editMode = true;
        $produkEdit = mysqli_fetch_assoc($editQuery);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim(mysqli_real_escape_string($con, $_POST['nama']));
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $detail = str_replace("\\n", "\n", $_POST['detail']);
    $detail = trim(mysqli_real_escape_string($con, $detail));
    $stok = $_POST['ketersediaan_stok'];
    $brand = trim(mysqli_real_escape_string($con, $_POST['brand']));
    $size = trim(mysqli_real_escape_string($con, $_POST['size']));

    if ($nama == "" || $kategori == "" || $harga == "" || $stok == "") {
        $message = "<div class='alert alert-danger mt-3'>Semua field wajib diisi kecuali foto.</div>";
    } else {
        $foto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $file_name = $_FILES['foto']['name'];
            $file_tmp = $_FILES['foto']['tmp_name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $new_file_name = uniqid('produk_', true) . "." . $ext;
                $upload_dir = '../uploads/produk/';
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0755, true);
                $upload_path = $upload_dir . $new_file_name;
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $foto = $new_file_name;
                } else {
                    $message = "<div class='alert alert-danger mt-3'>Upload foto gagal.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger mt-3'>Format file tidak didukung.</div>";
            }
        }

        if ($editMode) {
            $sql = "UPDATE produk SET nama='$nama', kategori_id='$kategori', brand='$brand', size='$size', harga='$harga', detail='$detail', ketersediaan_stok='$stok'";
            if ($foto)
                $sql .= ", foto='$foto'";
            $sql .= " WHERE id='" . $produkEdit['id'] . "'";
            $result = mysqli_query($con, $sql);
            if ($result) {
                header("Location: produk.php?update=success");
                exit;
            } else {
                $message = "<div class='alert alert-danger mt-3'>Gagal mengupdate produk.</div>";
            }

        } else {
            $sql = $foto ?
                "INSERT INTO produk (nama, kategori_id, brand, size, harga, foto, detail, ketersediaan_stok) VALUES ('$nama', '$kategori', '$brand', '$size', '$harga', '$foto', '$detail', '$stok')":
                "INSERT INTO produk (nama, kategori_id, brand, size, harga, detail, ketersediaan_stok) VALUES ('$nama', '$kategori', '$brand', '$size', '$harga', '$detail', '$stok')";
            $result = mysqli_query($con, $sql);
            if ($result) {
                header("Location: produk.php?add=success");
                exit;
            } else {
                $message = "<div class='alert alert-danger mt-3'>Gagal menyimpan produk.</div>";
            }

        }
    }
}

if (isset($_GET['delete'])) {
    $idDelete = (int) $_GET['delete'];
    $sqlFoto = mysqli_query($con, "SELECT foto FROM produk WHERE id = $idDelete");
    $fotoData = mysqli_fetch_assoc($sqlFoto);
    if ($fotoData && $fotoData['foto']) {
        $fileFoto = '../uploads/produk/' . $fotoData['foto'];
        if (file_exists($fileFoto))
            unlink($fileFoto);
    }
    mysqli_query($con, "DELETE FROM produk WHERE id = $idDelete");
    header("Location: produk.php");
    exit();
}

$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$queryProduk = mysqli_query($con, "SELECT p.*, k.nama AS kategori_nama FROM produk p LEFT JOIN kategori k ON p.kategori_id = k.id ORDER BY p.id DESC LIMIT $start, $limit");
$totalProduk = mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) as total FROM produk"))['total'];
$totalPages = ceil($totalProduk / $limit);

$queryKategori = mysqli_query($con, "SELECT * FROM kategori");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Produk - Admin</title>
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

        .foto-produk {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
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
        <li class="nav-item"><a href="kategori.php" class="nav-link "><i class="fas fa-list me-2"></i> Kategori</a></li>
        <li class="nav-item"><a href="produk.php" class="nav-link active"><i class="fas fa-box-open me-2"></i> Produk</a></li>
        <li class="nav-item"><a href="pesanan.php" class="nav-link "><i class="fas fa-receipt me-2"></i> Pesanan</a></li>
        <li class="nav-item"><a href="penawaran.php" class="nav-link "><i class="fas fa-hand-holding-usd me-2"></i> Penawaran</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link "><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Topbar -->
    <div class="topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 me-3">Produk</h5>
        </div>
        <div>
            <span class="me-3">Halo, admin</span>
        </div>
    </div>

    <!-- Konten -->
    <div class="content">
        <?php
        if (isset($_GET['update']) && $_GET['update'] === 'success') {
            echo "<div class='alert alert-success'>Produk berhasil diperbarui.</div>";
        } elseif (isset($_GET['add']) && $_GET['add'] === 'success') {
            echo "<div class='alert alert-success'>Produk berhasil ditambahkan.</div>";
        }
        ?>


        <button class="btn btn-primary mb-3" onclick="toggleForm()">
            <i class="fas fa-plus me-1"></i> Tambah Produk
        </button>
        <!-- Form Tambah/Edit Produk -->
        <div class="mt-3 d-none" id="formTambahProduk">
            <h5><?= $editMode ? 'Form Edit Produk' : 'Form Tambah Produk' ?></h5>
            <?= $message ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-2">
                    <label>Nama Produk</label>
                    <input type="text" name="nama" class="form-control" required
                        value="<?= $editMode ? htmlspecialchars($produkEdit['nama']) : '' ?>">
                </div>
                <div class="mb-2">
                    <label>Kategori</label>
                    <select name="kategori" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <?php while ($k = mysqli_fetch_array($queryKategori)): ?>
                            <option value="<?= $k['id'] ?>" <?= $editMode && $produkEdit['kategori_id'] == $k['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Brand</label>
                    <input type="text" name="brand" class="form-control"
                        value="<?= $editMode ? htmlspecialchars($produkEdit['brand']) : '' ?>">
                </div>

                <div class="mb-2">
                    <label>Size</label>
                    <input type="text" name="size" class="form-control"
                        placeholder="Contoh: M / L / XL"
                        value="<?= $editMode ? htmlspecialchars($produkEdit['size']) : '' ?>">
                </div>
                <div class="mb-2">
                    <label>Harga</label>
                    <input type="number" name="harga" class="form-control" required
                        value="<?= $editMode ? $produkEdit['harga'] : '' ?>">
                </div>
                <div class="mb-2">
                    <label>Foto Produk <?= $editMode ? '(kosongkan jika tidak diubah)' : '' ?></label>
                    <input type="file" name="foto" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Detail</label>
                    <textarea name="detail" class="form-control" rows="5"
                        placeholder="Contoh:\n- Bahan katun\n- Ukuran M fit to L\n- Adem dipakai"><?= $editMode ? htmlspecialchars($produkEdit['detail']) : '' ?></textarea>
                </div>
                <div class="mb-3">
                    <label>Ketersediaan Stok</label>
                    <select name="ketersediaan_stok" class="form-control" required>
                        <option value="Ready" <?= $editMode && $produkEdit['ketersediaan_stok'] == 'Ready' ? 'selected' : '' ?>>Ready</option>
                        <option value="Sold" <?= $editMode && $produkEdit['ketersediaan_stok'] == 'Sold' ? 'selected' : '' ?>>Sold</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-<?= $editMode ? 'warning' : 'success' ?>">
                    <i class="fas fa-<?= $editMode ? 'edit' : 'plus' ?>"></i>
                    <?= $editMode ? 'Update Produk' : 'Tambah' ?>
                </button>
                <?php if ($editMode): ?>
                    <a href="produk.php" class="btn btn-secondary ms-2">Batal Edit</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabel Produk -->
        <div class="mt-4">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalProduk == 0): ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada produk.</td>
                        </tr>
                    <?php else:
                        $no = $start + 1;
                        while ($row = mysqli_fetch_assoc($queryProduk)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <?php if ($row['foto'] && file_exists('../uploads/produk/' . $row['foto'])): ?>
                                        <img src="../uploads/produk/<?= $row['foto'] ?>" class="foto-produk">
                                    <?php else: ?>
                                        <span class="text-muted">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['kategori_nama']) ?></td>
                                <td><?= !empty($row['brand']) ? htmlspecialchars($row['brand']) : '-' ?></td>
                                <td><?= !empty($row['size']) ? htmlspecialchars($row['size']) : '-' ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['ketersediaan_stok']); ?></td>
                                <td>
                                    <a href="produk.php?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus produk ini?')"
                                        class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a href="?page=<?= $i ?>" class="page-link"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <script src="../bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../fontawesome/js/all.min.js"></script>
    <script>
        function toggleForm() {
            const form = document.getElementById('formTambahProduk');
            form.classList.toggle('d-none');
        }
        <?php if ($editMode): ?>
            document.addEventListener("DOMContentLoaded", function () {
                document.getElementById('formTambahProduk').classList.remove('d-none');
            });
        <?php endif; ?>
    </script>


</body>

</html>