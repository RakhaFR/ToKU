<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include_once menjamin VS Code tidak merah dan mencegah bentrok Error 500
include_once 'config/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToKU - Toko Online</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary mb-0 me-4" href="index.php">
                <i class="fas fa-shopping-bag me-2"></i>ToKU
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="index.php">Beranda</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold" href="#" id="dropKat" role="button" data-bs-toggle="dropdown">
                            Kategori Produk
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php">Semua Produk</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php 
                            $query_nav_kat = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY kategori ASC");
                            while($row_nav_kat = mysqli_fetch_assoc($query_nav_kat)) { 
                            ?>
                                <li>
                                    <a class="dropdown-item" href="index.php?kategori=<?php echo urlencode($row_nav_kat['kode']); ?>">
                                        <?php echo htmlspecialchars($row_nav_kat['kategori']); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>

                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <form action="index.php" method="GET" class="d-flex">
                            <input class="form-control form-control-sm me-2" type="search" name="search" placeholder="Cari nama produk..." style="width: 220px;" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-outline-primary btn-sm fw-bold" type="submit">Cari</button>
                        </form>
                    </li>
                </ul>

                <div class="navbar-nav align-items-center gap-3">
                    <?php if (isset($_SESSION['user'])): ?>
                        <span class="navbar-text fw-bold text-dark mb-0">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']); ?>
                        </span>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm fw-bold px-3" onclick="return confirm('Apakah kamu ingin keluar?')">
                            <i class="fas fa-sign-out-alt me-1"></i> Keluar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-2"></div>