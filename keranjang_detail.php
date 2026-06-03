<?php
session_start();

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';
include 'includes/header.php';

// Fitur Hapus Item dari Keranjang jika ada parameter 'hapus' di URL
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    if (isset($_SESSION['cart'][$id_hapus])) {
        unset($_SESSION['cart'][$id_hapus]);
    }
    echo "<script>window.location.href = 'keranjang_detail.php';</script>";
    exit;
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold mb-4 text-dark">
                <i class="fas fa-shopping-basket text-primary me-2"></i>Keranjang Belanja Kamu
            </h4>

            <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
                <div class="card shadow-sm border-0 text-center p-5 rounded-4">
                    <div class="card-body">
                        <div class="text-muted mb-3">
                            <i class="fas fa-shopping-cart fa-4x text-light-emphasis"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Keranjang Belanjaanmu Kosong</h5>
                        <p class="text-muted small">Kamu belum menambahkan produk apa pun ke keranjang.</p>
                        <a href="index.php" class="btn btn-primary px-4 py-2 fw-bold rounded-3 shadow-sm mt-2">
                            <i class="fas fa-arrow-left me-2"></i>Mulai Belanja
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary fw-semibold">
                                <tr>
                                    <th class="ps-4">Produk</th>
                                    <th>Harga Satuan</th>
                                    <th class="text-center" style="width: 120px;">Jumlah (Qty)</th>
                                    <th>Total Harga</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grand_total = 0;
                                // Loop semua item yang ada di session keranjang
                                foreach ($_SESSION['cart'] as $id_produk => $qty):
                                    // Ambil detail data produk dari DB berdasarkan ID dari session
                                    $id_clean = mysqli_real_escape_string($koneksi, $id_produk);
                                    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE no = '$id_clean'");
                                    $produk = mysqli_fetch_assoc($query);

                                    if ($produk):
                                        $total_harga = $produk['harga'] * $qty;
                                        $grand_total += $total_harga;
                                ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/<?php echo $produk['image']; ?>" class="rounded border" alt="<?php echo $produk['namaproduk']; ?>" style="width: 50px; height: 50px; object-fit: contain; background-color: #f8f9fa;">
                                                    <div class="ms-3">
                                                        <h6 class="fw-bold text-dark mb-0"><?php echo $produk['namaproduk']; ?></h6>
                                                        <small class="text-muted"><?php echo substr($produk['ket'], 0, 50) . '...'; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                                            <td class="text-center fw-bold text-dark"><?php echo $qty; ?></td>
                                            <td class="fw-bold text-primary">Rp<?php echo number_format($total_harga, 0, ',', '.'); ?></td>
                                            <td class="text-center">
                                                <a href="keranjang_detail.php?hapus=<?php echo $produk['no']; ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('Hapus produk ini dari keranjang?')" title="Hapus Item">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer bg-white p-4 border-top">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <a href="index.php" class="btn btn-outline-secondary fw-semibold rounded-3 btn-sm">
                                    <i class="fas fa-chevron-left me-2"></i>Lanjut Pilih Produk Lain
                                </a>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="mb-3">
                                    <span class="text-muted me-2">Total Seluruhnya:</span>
                                    <span class="fs-4 fw-bold text-success">Rp<?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                                </div>
                                <a href="checkout_massal.php" class="btn btn-success fw-bold px-4 py-2.5 rounded-3 shadow-sm">
                                    Proses Bayar Semua &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>