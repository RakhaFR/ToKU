<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config/koneksi.php';

// Hitung total item unik atau kuantitas total yang ada di keranjang session
$jumlah_keranjang = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Menghitung total seluruh jumlah qty barang di keranjang
    $jumlah_keranjang = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToKU - Platform Belanja Modern</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <style>
        .custom-navbar {
            background-color: #ffffff !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            padding: 15px 0;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-size: 1.5rem;
            letter-spacing: 0.5px;
        }

        .custom-navbar .nav-link {
            color: #475569 !important;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s ease;
            position: relative;
        }

        .custom-navbar .nav-link:hover,
        .custom-navbar .nav-link.active {
            color: #0d6efd !important;
        }

        /* Efek garis bawah halus saat hover menu */
        .custom-navbar .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 50%;
            color: #0d6efd;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .custom-navbar .nav-link:hover::after {
            width: 70%;
        }

        /* Input Search Premium */
        .search-box {
            border-radius: 50px;
            padding-left: 15px;
            padding-right: 15px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .search-box:focus {
            background-color: #ffffff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        /* Desain Tombol Keranjang Lingkaran */
        .cart-shortcut {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: #475569;
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
        }

        .cart-shortcut:hover {
            background-color: #e2e8f0;
            color: #0d6efd;
            transform: scale(1.05);
        }

        .member-badge {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.88rem;
        }

        .fs-7 {
            font-size: 0.85rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light custom-navbar fixed-top-static">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="index.php">
                <span class="text-primary"><i class="fas fa-shopping-bag me-2"></i>To</span>KU
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="index.php"><i class="fas fa-home me-1 small text-muted"></i> Beranda</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle mx-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-boxes me-1 small text-muted"></i> Produk
                        </a>
                        <ul class="dropdown-menu border-0 shadow-lg p-2 rounded-3" aria-labelledby="navbarDropdown" style="min-width: 200px;">
                            <li>
                                <a class="dropdown-item py-2 rounded-2 fs-7" href="index.php#katalog">
                                    <i class="fas fa-border-all me-2 text-muted"></i> Semua Kategori
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider opacity-50">
                            </li>
                            <?php
                            $query_nav_kat = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY kategori ASC");
                            if (mysqli_num_rows($query_nav_kat) > 0) {
                                while ($row_nav_kat = mysqli_fetch_assoc($query_nav_kat)) {
                            ?>
                                    <li>
                                        <a class="dropdown-item py-2 rounded-2 fs-7" href="index.php?kategori=<?php echo urlencode($row_nav_kat['kode']); ?>#katalog">
                                            <i class="fas fa-tag me-2 text-success opacity-75"></i> <?php echo htmlspecialchars($row_nav_kat['kategori']); ?>
                                        </a>
                                    </li>
                            <?php
                                }
                            } else {
                                echo "<li><a class='dropdown-item disabled text-muted fs-7' href='#'>Belum ada kategori</a></li>";
                            }
                            ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link mx-2" href="#edukasi"><i class="fas fa-video me-1 small text-muted"></i> Video Panduan</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3 ms-auto">
                    <form action="index.php#katalog" method="GET" class="mb-0 position-relative">
                        <input class="form-control form-control-sm search-box" type="search" name="search" placeholder="Cari barang impian..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </form>

                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="d-flex align-items-center gap-2">
                            <a href="keranjang_detail.php" class="cart-shortcut position-relative text-decoration-none" title="Lihat Keranjang">
                                <i class="fas fa-shopping-cart small"></i>
                                <?php if ($jumlah_keranjang > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.65rem; padding: 3px 6px;">
                                        <?php echo $jumlah_keranjang; ?>
                                    </span>
                                <?php endif; ?>
                            </a>

                            <span class="navbar-text fw-bold member-badge text-nowrap">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['user']; ?>
                            </span>
                            <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 text-nowrap fw-bold" onclick="return confirm('Apakah kamu ingin keluar dari sesi aplikasi?')">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-2"></div>