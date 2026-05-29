<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['status'] != 0) {
    header("Location: ../login.php");
    exit;
}
include '../config/koneksi.php';
/** @var mysqli $koneksi */

$query_laporan = mysqli_query($koneksi, "SELECT * FROM checkoutfinish ORDER BY no DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Panel Admin</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/admin_laporan.css">
</head>
<body>

    <div class="wrapper">
    <?php include '../includes/sidebar_admin.php'; ?>

        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded-4 shadow-sm border border-light admin-welcome-box">
                <div>
                    <h4 class="fw-bold text-dark mb-0">Laporan Transaksi</h4>
                    <p class="text-muted small mb-0">Riwayat terekam data invoice riwayat pesanan pembeli.</p>
                </div>
                <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm rounded-3 fw-bold px-3 d-none d-sm-block"><i class="fas fa-external-link-alt me-1"></i> Kunjungi Toko</a>
            </div>

            <div class="card card-custom bg-white shadow-sm border border-light mb-2">
                <div class="p-3 p-md-4 border-bottom">
                    <span class="fw-bold text-dark"><i class="fas fa-file-invoice-dollar text-success me-2"></i>Arsip Transaksi Selesai</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive-custom">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">No. Invoice</th>
                                    <th width="25%">Nama Barang</th>
                                    <th width="15%">Pembeli</th>
                                    <th width="15%">Rek. Bank</th>
                                    <th width="10%">Harga</th>
                                    <th width="5%" class="text-center">QTY</th>
                                    <th width="10%" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                if(mysqli_num_rows($query_laporan) > 0) {
                                    while ($lap = mysqli_fetch_assoc($query_laporan)) {
                                    ?>
                                        <tr>
                                            <td class="text-muted fw-semibold"><?php echo $i++; ?></td>
                                            <td><span class="badge bg-light text-dark border p-2 fw-bold" style="font-size: 0.78rem;"><?php echo htmlspecialchars($lap['invoice']); ?></span></td>
                                            <td><strong class="text-dark d-block text-wrap" style="min-width: 140px;"><?php echo htmlspecialchars($lap['namabarang']); ?></strong></td>
                                            <td><span class="text-dark text-nowrap"><?php echo htmlspecialchars($lap['pembeli']); ?></span></td>
                                            <td><small class="text-muted text-wrap d-block" style="min-width: 100px; font-size: 0.8rem;"><?php echo htmlspecialchars($lap['rekbank']); ?></small></td>
                                            <td class="text-nowrap">Rp<?php echo number_format($lap['harga'], 0, ',', '.'); ?></td>
                                            <td class="text-center fw-bold text-dark"><?php echo $lap['qty']; ?></td>
                                            <td class="text-end fw-bold text-success text-nowrap">Rp<?php echo number_format($lap['total_harga'], 0, ',', '.'); ?></td>
                                        </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center py-5 text-muted'><i class='fas fa-folder-open d-block fs-3 mb-2 text-secondary'></i>Belum ada rekaman invoice pembeli yang tersimpan.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> </div> <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>