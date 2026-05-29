<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['status'] != 0) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';
/** @var mysqli $koneksi */

$res_produk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk");
$tot_produk = mysqli_fetch_assoc($res_produk)['total'] ?? 0;

$res_transaksi = mysqli_query($koneksi, "SELECT COUNT(*) as total, SUM(total_harga) as pendapatan FROM checkoutfinish");
$data_trans = mysqli_fetch_assoc($res_transaksi);
$tot_transaksi = $data_trans['total'] ?? 0;
$tot_pendapatan = $data_trans['pendapatan'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Modern Panel</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body>

    <div class="wrapper">

    <?php include '../includes/sidebar_admin.php'; ?>

        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded-4 shadow-sm border border-light">
                <div>
                    <h4 class="fw-bold text-dark mb-0">Statistik Penjualan</h4>
                    <p class="text-muted small mb-0">Selamat datang kembali, <b><?php echo htmlspecialchars($_SESSION['user'] ?? 'Admin'); ?></b></p>
                </div>
                <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm rounded-3 fw-bold px-3 d-none d-sm-block"><i class="fas fa-external-link-alt me-1"></i> Kunjungi Toko</a>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-custom bg-primary text-white shadow-sm p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white-50 small mb-1 fw-bold">TOTAL PRODUK</p>
                                <h2 class="fw-bold mb-0"><?php echo $tot_produk; ?> <span class="fs-6 fw-normal">Item</span></h2>
                            </div>
                            <div class="icon-shape"><i class="fas fa-boxes"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-custom bg-success text-white shadow-sm p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white-50 small mb-1 fw-bold">ORDER SELESAI</p>
                                <h2 class="fw-bold mb-0"><?php echo $tot_transaksi; ?> <span class="fs-6 fw-normal">Transaksi</span></h2>
                            </div>
                            <div class="icon-shape"><i class="fas fa-shopping-bag"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card card-custom bg-dark text-white shadow-sm p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white-50 small mb-1 fw-bold">TOTAL PENDAPATAN</p>
                                <h3 class="fw-bold mb-0">Rp<?php echo number_format($tot_pendapatan, 0, ',', '.'); ?></h3>
                            </div>
                            <div class="icon-shape text-warning"><i class="fas fa-wallet"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom bg-white p-3 p-md-4 shadow-sm border border-light mb-3">
                <div class="mb-2">
                    <h5 class="fw-bold text-dark mb-1"><i class="fas fa-chart-line text-primary me-2"></i>Grafik Arus Performa Omset</h5>
                </div>
                <div class="chart-container">
                    <canvas id="canvasOmset"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        const ctx = document.getElementById('canvasOmset').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                datasets: [{
                    label: 'Estimasi Pendapatan harian',
                    data: [0, 150000, 300000, <?php echo $tot_pendapatan; ?>, <?php echo $tot_pendapatan + 50000; ?>, <?php echo $tot_pendapatan + 120000; ?>, <?php echo $tot_pendapatan + 200000; ?>],
                    backgroundColor: 'rgba(13, 110, 253, 0.06)',
                    borderColor: '#0d6efd',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#0d6efd',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>

</html>