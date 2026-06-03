<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';
include 'includes/header.php';

// PROSES UPDATE QTY JIKA ADA TOMBOL + ATAU - YANG DIKLIK
if (isset($_POST['update_keranjang'])) {
    $id_target = $_POST['id_produk'];
    $aksi      = $_POST['aksi_qty']; // bernilai 'tambah' atau 'kurang'

    if (isset($_SESSION['cart'][$id_target])) {
        if ($aksi === 'tambah') {
            $_SESSION['cart'][$id_target]++;
        } elseif ($aksi === 'kurang') {
            $_SESSION['cart'][$id_target]--;
            // Jika jumlahnya di bawah 1, hapus otomatis produk dari keranjang
            if ($_SESSION['cart'][$id_target] < 1) {
                unset($_SESSION['cart'][$id_target]);
            }
        }
    }
    header("Location: keranjang_detail.php");
    exit;
}

// PROSES HAPUS ITEM SECARA MANUAL
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    if (isset($_SESSION['cart'][$id_hapus])) {
        unset($_SESSION['cart'][$id_hapus]);
    }
    header("Location: keranjang_detail.php");
    exit;
}

$grand_total = 0;
?>

<style>
    .input-qty-custom::-webkit-outer-spin-button,
    .input-qty-custom::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-qty-custom {
        -moz-appearance: textfield;
        text-align: center;
        font-weight: bold;
        width: 50px !important;
    }
</style>

<div class="container my-5">
    <h4 class="fw-bold mb-4 text-dark"><i class="fas fa-shopping-basket text-primary me-2"></i>Keranjang Belanja Anda</h4>

    <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
        <div class="card p-5 text-center shadow-sm border-0 rounded-4">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
            <h5 class="text-secondary fw-semibold">Keranjang belanja Anda masih kosong</h5>
            <p class="text-muted small">Yuk, cari produk menarik dan masukkan ke keranjang belanjaanmu!</p>
            <a href="index.php" class="btn btn-primary btn-sm px-4 fw-bold rounded-pill mt-2">Mulai Belanja</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="bg-light border-bottom text-secondary small fw-bold">
                                <tr>
                                    <th class="p-3">Produk</th>
                                    <th class="p-3">Harga</th>
                                    <th class="p-3 text-center">Jumlah</th>
                                    <th class="p-3 text-end">Total</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($_SESSION['cart'] as $id_produk => $qty):
                                    $id_clean = mysqli_real_escape_string($koneksi, $id_produk);
                                    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE no = '$id_clean'");
                                    $produk = mysqli_fetch_assoc($query);

                                    if ($produk):
                                        $subtotal = $produk['harga'] * $qty;
                                        $grand_total += $subtotal;
                                ?>
                                        <tr class="border-bottom border-light">
                                            <td class="p-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="assets/images/<?php echo $produk['image']; ?>" class="rounded bg-light" style="width: 50px; height: 50px; object-fit: contain;">
                                                    <div>
                                                        <span class="fw-bold text-dark d-block mb-0"><?php echo htmlspecialchars($produk['namaproduk']); ?></span>
                                                        <small class="text-muted" style="font-size: 11px;"><?php echo htmlspecialchars($produk['kode']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-3 text-dark fw-semibold">Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                                            
                                            <td class="p-3">
                                                <form action="" method="POST" class="d-flex justify-content-center">
                                                    <input type="hidden" name="id_produk" value="<?php echo $produk['no']; ?>">
                                                    <input type="hidden" name="update_keranjang" value="1">
                                                    
                                                    <div class="input-group input-group-sm justify-content-center" style="max-width: 110px;">
                                                        <button class="btn btn-outline-secondary" type="submit" name="aksi_qty" value="kurang">
                                                            <i class="fas fa-minus fa-xs"></i>
                                                        </button>
                                                        <input type="number" class="form-control input-qty-custom bg-white" value="<?php echo $qty; ?>" readonly>
                                                        <button class="btn btn-outline-secondary" type="submit" name="aksi_qty" value="tambah">
                                                            <i class="fas fa-plus fa-xs"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            
                                            <td class="p-3 text-end text-primary fw-bold">Rp<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                            <td class="p-3 text-center">
                                                <a href="keranjang_detail.php?hapus=<?php echo $produk['no']; ?>" class="btn btn-link link-danger p-0" onclick="return confirm('Hapus produk ini dari keranjang?')" title="Hapus Item">
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
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 text-dark">Ringkasan Belanja</h5>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Total Tagihan</span>
                        <span class="fw-bold text-success fs-5">Rp<?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="checkout_massal.php" class="btn btn-success fw-bold py-2 rounded-3">
                            <i class="fas fa-shopping-bag me-2"></i>Lanjut Ke Pembayaran
                        </a>
                        <a href="index.php" class="btn btn-outline-primary fw-bold py-2 rounded-3 btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Tambah Produk Lain
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>