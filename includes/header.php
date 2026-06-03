<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include_once menjamin VS Code tidak merah dan mencegah bentrok Error 500
include_once 'config/koneksi.php';

$username = "";
if (isset($_SESSION['user'])) {
    $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM login WHERE user = '" . mysqli_real_escape_string($koneksi, $_SESSION['user']) . "'"));
    if ($data) {
        $fulluser = $data['user'];
        $username = explode("@", $fulluser)[0];
    }
}

// Hitung total item unik di keranjang untuk badge ikon keranjang
$cart_count = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart_count = count($_SESSION['cart']);
}
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
                <i class="fas fa-store me-2"></i>ToKU
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active fw-semibold" aria-current="page" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <form action="index.php" method="GET" class="d-flex">
                            <input class="form-control form-control-sm me-2" type="search" name=\"search\" placeholder="Cari nama produk..." style="width: 220px;" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-outline-primary btn-sm fw-bold" type="submit">Cari</button>
                        </form>
                    </li>
                </ul>

                <div class="navbar-nav align-items-center gap-3">
                    <?php if (isset($_SESSION['login']) && isset($_SESSION['user'])): ?>
                        <a href="keranjang_detail.php" class="btn btn-link position-relative text-dark p-2 me-2" title="Lihat Keranjang">
                            <i class="fas fa-shopping-cart fa-lg text-secondary"></i>
                            <?php if ($cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem;">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <span class="navbar-text fw-bold text-dark mb-0">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($username); ?>
                        </span>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm fw-bold px-3" onclick="return confirm('Apakah kamu ingin keluar?')">
                            <i class="fas fa-sign-out-alt me-1"></i> Keluar
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary btn-sm fw-bold px-4 rounded-pill">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>