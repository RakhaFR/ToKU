<?php
session_start();

// Proteksi login pembeli
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';
include 'includes/header.php';

// Menangkap parameter 'id' dari URL
$id_produk = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';

// Mengambil data produk berdasarkan kolom 'no'
$query_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE no = '$id_produk'") or die(mysqli_error($koneksi));
$produk = mysqli_fetch_assoc($query_produk);

if (!$produk) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

// Variabel penanda untuk memicu modal custom
$checkout_sukses = false;
$id_terakhir = '';

if (isset($_POST['proses_checkout'])) {
    $namabarang  = mysqli_real_escape_string($koneksi, $produk['namaproduk']);
    $pembeli     = mysqli_real_escape_string($koneksi, $_POST['pembeli']);
    $rekbank     = mysqli_real_escape_string($koneksi, $_POST['rekbank']);
    $harga       = $produk['harga'];
    $qty         = (int)$_POST['qty'];
    $total_harga = $harga * $qty;
    $invoice     = "INV-" . rand(10000, 99999);

    // Insert ke tabel checkoutfinish sesuai database
    $query_insert = "INSERT INTO checkoutfinish (namabarang, pembeli, invoice, rekbank, harga, qty, total_harga) 
                     VALUES ('$namabarang', '$pembeli', '$invoice', '$rekbank', '$harga', '$qty', '$total_harga')";
    
    if (mysqli_query($koneksi, $query_insert)) {
        $id_terakhir = mysqli_insert_id($koneksi);
        $checkout_sukses = true;
    } else {
        die("<b>Gagal menyimpan transaksi!</b><br>Pesan Eror Database: " . mysqli_error($koneksi));
    }
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">Detail Produk</h5>
                </div>
                <div class="card-body text-center">
                    <div style="height: 200px; background-color: #f8f9fa;" class="mb-3 rounded overflow-hidden d-flex align-items-center justify-content-center">
                        <img src="assets/images/<?php echo $produk['image']; ?>" class="img-fluid" alt="<?php echo $produk['namaproduk']; ?>" style="max-height: 100%; object-fit: contain;">
                    </div>
                    <h4 class="fw-bold text-dark"><?php echo $produk['namaproduk']; ?></h4>
                    <p class="text-muted small"><?php echo $produk['ket']; ?></p>
                    <h3 class="text-primary fw-bold">Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0 fw-bold">Formulir Pembayaran</h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="pembeli" class="form-label fw-bold">Nama Pembeli</label>
                            <input type="text" name="pembeli" id="pembeli" class="form-control" value="<?php echo $_SESSION['user']; ?>" placeholder="Nama lengkap pembeli" required>
                        </div>

                        <div class="mb-3">
                            <label for="rekbank" class="form-label fw-bold">Rekening Bank</label>
                            <input type="text" name="rekbank" id="rekbank" class="form-control" placeholder="Contoh: BRI 00521123" required>
                        </div>

                        <div class="mb-4">
                            <label for="qty" class="form-label fw-bold">Jumlah Beli (Qty)</label>
                            <input type="number" name="qty" id="qty" class="form-control" value="1" min="1" oninput="hitungTotal(this.value)" required>
                        </div>

                        <div class="bg-light p-3 rounded mb-4 border border-dashed">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Harga Satuan:</span>
                                <span>Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold text-dark fs-5">
                                <span>Estimasi Total:</span>
                                <span id="live-total">Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></span>
                            </div>
                        </div>

                        <button type="submit" name="proses_checkout" class="btn btn-success w-100 py-2.5 fw-bold shadow-sm">Konfirmasi Pembelian</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade <?php echo $checkout_sukses ? 'show d-block' : ''; ?>" id="modalSukses" tabindex="-1" style="<?php echo $checkout_sukses ? 'background: rgba(0,0,0,0.5);' : ''; ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-auto" style="max-width: 400px;">
        <div class="modal-content shadow border-0 rounded-4 text-center p-3">
            <div class="modal-body p-4">
                <div class="text-success mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill d-inline-block" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                </div>
                <h4 class="fw-bold text-dark mb-2">Transaksi Berhasil!</h4>
                <p class="text-muted small mb-4">Data pembelian kamu telah berhasil disimpan ke dalam database.</p>
                
                <a href="checkout_finish.php?id=<?php echo $id_terakhir; ?>" class="btn btn-success w-100 py-2 fw-bold rounded-3 shadow-sm">
                    Lihat Nota Pembayaran &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function hitungTotal(jumlah) {
    const harga = <?php echo $produk['harga']; ?>;
    const total = harga * (jumlah > 0 ? jumlah : 1);
    document.getElementById('live-total').innerText = 'Rp' + total.toLocaleString('id-ID');
}
</script>

<?php 
include 'includes/footer.php'; 
?>