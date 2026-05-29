<?php
// Mengambil nama file yang sedang diakses untuk class active otomatis
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- NAVBAR MOBILE -->
<nav class="navbar navbar-expand-md navbar-dark mobile-navbar sticky-top shadow-sm">
    <a class="navbar-brand fw-bold text-white mb-0 text-xl-center" href="#"><span class="text-info">To</span>KU Admin</a>
    <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#adminMobileMenu">
        <span class="navbar-toggler-icon" style="width: 1.25rem; height: 1.25rem;"></span>
    </button>
    <div class="collapse navbar-collapse mt-2" id="adminMobileMenu">
        <ul class="navbar-nav me-auto mb-1">
            <li class="nav-item">
                <a class="nav-link py-2 <?= ($current_page == 'admin.php') ? 'active text-info' : 'text-white-50' ?>" href="admin.php">
                    <i class="fas fa-chart-pie me-2"></i>Ringkasan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2 <?= ($current_page == 'admin_kategori.php') ? 'active text-info' : 'text-white-50' ?>" href="admin_kategori.php">
                    <i class="fas fa-tags me-2"></i>Kelola Kategori
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2 <?= ($current_page == 'admin_produk.php') ? 'active text-info' : 'text-white-50' ?>" href="admin_produk.php">
                    <i class="fas fa-box me-2"></i>Kelola Produk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2 <?= ($current_page == 'admin_media.php') ? 'active text-info' : 'text-white-50' ?>" href="admin_media.php">
                    <i class="fas fa-photo-video me-2"></i>Kelola Media
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2 <?= ($current_page == 'admin_laporan.php') ? 'active text-info' : 'text-white-50' ?>" href="admin_laporan.php">
                    <i class="fas fa-file-invoice me-2"></i>Laporan Order
                </a>
            </li>
            <li class="nav-item mt-2 border-top border-secondary pt-2">
                <a class="nav-link text-danger py-2" href="logout.php" onclick="return confirm('Keluar dari panel admin?')">
                    <i class="fas fa-sign-out-alt me-2"></i>Keluar
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- SIDEBAR DESKTOP -->
<div class="sidebar shadow-sm flex-shrink-0">
    <div class="p-4 text-center border-bottom border-secondary mb-3">
        <h5 class="fw-bold mb-0 text-white"><i class="fas fa-store me-2 text-info"></i>ToKU</h5>
        <small class="text-white text-uppercase tracking-wider" style="font-size: 0.72rem;">Administrator</small>
    </div>
    <div class="nav flex-column">
        <a class="nav-link <?= ($current_page == 'admin.php') ? 'active' : '' ?>" href="admin.php">
            <i class="fas fa-chart-pie me-2"></i> Ringkasan
        </a>
        <a class="nav-link <?= ($current_page == 'admin_kategori.php') ? 'active' : '' ?>" href="admin_kategori.php">
            <i class="fas fa-tags me-2"></i> Kelola Kategori
        </a>
        <a class="nav-link <?= ($current_page == 'admin_produk.php') ? 'active' : '' ?>" href="admin_produk.php">
            <i class="fas fa-box me-2"></i> Kelola Produk
        </a>
        <a class="nav-link <?= ($current_page == 'admin_media.php') ? 'active' : '' ?>" href="admin_media.php">
            <i class="fas fa-photo-video me-2"></i> Kelola Media
        </a>
        <a class="nav-link <?= ($current_page == 'admin_laporan.php') ? 'active' : '' ?>" href="admin_laporan.php">
            <i class="fas fa-file-invoice me-2"></i> Laporan Order
        </a>
        <hr class="mx-3 text-secondary my-2">
        <a class="nav-link text-danger mt-3" href="logout.php" onclick="return confirm('Keluar dari panel admin?')">
            <i class="fas fa-sign-out-alt me-2"></i> Keluar
        </a>
    </div>
</div>