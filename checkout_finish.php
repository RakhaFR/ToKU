<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';
include 'includes/header.php';

// Ambil parameter id (untuk beli langsung) atau invoice (untuk checkout massal)
$id_order = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';
$inv_order = isset($_GET['invoice']) ? mysqli_real_escape_string($koneksi, $_GET['invoice']) : '';

if (!empty($id_order)) {
    // JALUR NOTA TUNGGAL (Beli Sekarang)
    $query_order = mysqli_query($koneksi, "SELECT * FROM checkoutfinish WHERE no = '$id_order'") or die(mysqli_error($koneksi));
    $order = mysqli_fetch_assoc($query_order);
    
    if (!$order) {
        echo "<script>window.location.href = 'index.php';</script>";
        exit;
    }
    // Buat variabel dummy array agar strukturnya sama dengan massal di bawah
    $data_nota = [$order]; 
    $grand_total = $order['total_harga'];
    $invoice_tampil = $order['invoice'];
    $pembeli_tampil = $order['pembeli'];
    $rekbank_tampil = $order['rekbank'];

} elseif (!empty($inv_order)) {
    // JALUR NOTA MASSAL (Dari Keranjang)
    $query_order = mysqli_query($koneksi, "SELECT * FROM checkoutfinish WHERE invoice = '$inv_order'") or die(mysqli_error($koneksi));
    
    if (mysqli_num_rows($query_order) == 0) {
        echo "<script>window.location.href = 'index.php';</script>";
        exit;
    }

    $data_nota = [];
    $grand_total = 0;
    while ($row = mysqli_fetch_assoc($query_order)) {
        $data_nota[] = $row;
        $grand_total += $row['total_harga'];
        // Ambil info utama dari baris pertama
        $invoice_tampil = $row['invoice'];
        $pembeli_tampil = $row['pembeli'];
        $rekbank_tampil = $row['rekbank'];
    }
} else {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm border-0 p-4 bg-white rounded-4">
                <div class="card-body">
                    <div class="mb-4 text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" fill="currentColor" class="bi bi-shield-fill-check d-inline-block" viewBox="0 0 16 16">
                            <path d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.775 11.775 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.54 1.54 0 0 0-1.044-1.263 62.439 62.439 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm2.146 5.146a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L6.5 7.793l2.646-2.647z"/>
                        </svg>
                    </div>
                    
                    <h2 class="fw-bold text-dark mb-2">Terima Kasih!</h2>
                    <p class="text-muted mb-4">Pembayaran Anda terverifikasi. Berikut adalah bukti transaksi Anda:</p>
                    
                    <div class="border rounded-3 p-4 text-start bg-light mb-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary text-center">NOTA TRANSAKSI</h6>
                        
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">No. Invoice:</span>
                            <span class="fw-bold text-dark"><?php echo $invoice_tampil; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Nama Pembeli:</span>
                            <span class="text-dark"><?php echo $pembeli_tampil; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Rekening Bank:</span>
                            <span class="text-dark"><?php echo $rekbank_tampil; ?></span>
                        </div>
                        
                        <div class="my-3 border-top pt-2">
                            <span class="text-muted small d-block mb-1">Daftar Produk:</span>
                            <?php foreach ($data_nota as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="text-dark fw-semibold">- <?php echo $item['namabarang']; ?> (<?php echo $item['qty']; ?>x)</span>
                                    <span class="text-muted">Rp<?php echo number_format($item['total_harga'], 0, ',', '.'); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Total Pembayaran:</span>
                            <span class="fw-bold text-primary fs-5">Rp<?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <a href="index.php" class="btn btn-outline-primary px-4 py-2 fw-bold w-100 rounded-3">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>