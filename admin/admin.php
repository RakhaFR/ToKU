<?php
// Mencegah error jika session sudah dimulai di file include lain
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || $_SESSION['status'] != 0) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';
/** @var mysqli $koneksi */

// 1. Hitung Total Jenis Produk
$res_produk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk");
$tot_produk = $res_produk ? (mysqli_fetch_assoc($res_produk)['total'] ?? 0) : 0;

// 2. Hitung Total Transaksi & Total Pendapatan Keseluruhan
$res_transaksi = mysqli_query($koneksi, "SELECT COUNT(*) as total, SUM(total_harga) as pendapatan FROM checkoutfinish");
$tot_transaksi = 0;
$tot_pendapatan = 0;

if ($res_transaksi) {
    $data_trans = mysqli_fetch_assoc($res_transaksi);
    $tot_transaksi = $data_trans['total'] ?? 0;
    $tot_pendapatan = $data_trans['pendapatan'] ?? 0;
}

// 3. PROSES DATA GRAFIK: Hitung Pendapatan per Hari (Senin - Minggu)
$pendapatan_harian = [
    'Senin'   => 0,
    'Selasa'  => 0,
    'Rabu'    => 0,
    'Kamis'   => 0,
    'Jumat'   => 0,
    'Sabtu'   => 0,
    'Minggu'  => 0
];

// CATATAN: Jika nama kolom tanggalmu di database bukan 'tanggal', ganti kata 'tanggal' di bawah ini:
$query_hari = mysqli_query($koneksi, "
    SELECT 
        DAYNAME(tanggal) as nama_hari, 
        SUM(total_harga) as total 
    FROM checkoutfinish 
    WHERE WEEK(tanggal, 1) = WEEK(CURDATE(), 1) AND YEAR(tanggal) = YEAR(CURDATE())
    GROUP BY DAYOFWEEK(tanggal)
");

// Array bantuan penterjemah hari bahasa Inggris ke Indonesia
$hari_indo = [
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Minggu'
];

if ($query_hari) {
    while ($row = mysqli_fetch_assoc($query_hari)) {
        $hari_eng = $row['nama_hari'];
        if (isset($hari_indo[$hari_eng])) {
            $nama_hari_indo = $hari_indo[$hari_eng];
            $pendapatan_harian[$nama_hari_indo] = (int)$row['total'];
        }
    }
}
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
                    <h5 class="fw-bold text-dark mb-1"><i class="fas fa-chart-line text-primary me-2"></i>Grafik Arus Performa Omset (Minggu Ini)</h5>
                </div>
                <div class="chart-container" style="position: relative; height:300px;">
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
                    label: 'Pendapatan Hari Ini',
                    data: [
                        <?php echo $pendapatan_harian['Senin']; ?>,
                        <?php echo $pendapatan_harian['Selasa']; ?>,
                        <?php echo $pendapatan_harian['Rabu']; ?>,
                        <?php echo $pendapatan_harian['Kamis']; ?>,
                        <?php echo $pendapatan_harian['Jumat']; ?>,
                        <?php echo $pendapatan_harian['Sabtu']; ?>,
                        <?php echo $pendapatan_harian['Minggu']; ?>
                    ],
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                return ' Pendapatan: Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>