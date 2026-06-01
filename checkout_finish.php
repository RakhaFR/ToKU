<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';
include 'includes/header.php';

$id_order = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';

if (empty($id_order)) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}


$query_order = mysqli_query($koneksi, "SELECT * FROM checkoutfinish WHERE no = '$id_order'");
$order = mysqli_fetch_assoc($query_order);

if (!$order) {
    echo "<script>alert('Data transaksi tidak ditemukan.'); window.location.href = 'index.php';</script>";
    exit;
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4 bg-white text-center">
                <h3 class="fw-bold text-success mb-2">Pembayaran Berhasil!</h3>
                <p class="text-muted small mb-4">Simpan bukti transaksi halaman nota di bawah ini.</p>
                
                <div class="border rounded text-start p-3 bg-light mb-4">
                    <h6 class="fw-bold text-center border-bottom pb-2 mb-3 text-secondary">NOTA TRANSAKSI</h6>
                    
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">No. Invoice:</span>
                        <span class="fw-bold text-dark"><?php echo $order['invoice']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Nama Pembeli:</span>
                        <span class="text-dark"><?php echo htmlspecialchars($order['pembeli']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Metode Bayar:</span>
                        <span class="text-dark"><?php echo htmlspecialchars($order['rekbank']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Produk:</span>
                        <span class="text-dark fw-bold"><?php echo htmlspecialchars($order['namabarang']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Jumlah Item:</span>
                        <span class="text-dark"><?php echo $order['qty']; ?>x</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">Total Pembayaran:</span>
                        <span class="fw-bold text-primary fs-5">Rp<?php echo number_format($order['total_harga'], 0, ',', '.'); ?></span>
                    </div>
                </div>

                <a href="index.php" class="btn btn-primary w-100 fw-bold">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>