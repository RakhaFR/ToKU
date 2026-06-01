<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';
include 'includes/header.php';

// Menangkap parameter ID Produk
$id_produk = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';

$query_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE no = '$id_produk'");
$produk = mysqli_fetch_assoc($query_produk);

if (!$produk) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

if (isset($_POST['proses_checkout'])) {
    $namabarang  = mysqli_real_escape_string($koneksi, $produk['namaproduk']);
    $pembeli     = mysqli_real_escape_string($koneksi, $_POST['pembeli']);
    $rekbank     = mysqli_real_escape_string($koneksi, $_POST['rekbank']);
    $harga       = $produk['harga'];
    $qty         = (int)$_POST['qty'];
    $total_harga = $harga * $qty;
    $invoice     = "INV-" . rand(10000, 99999);

    if (empty(trim($pembeli)) || empty(trim($rekbank))) {
        echo "<script>
                alert('Gagal! Nama Pembeli dan Metode Pembayaran wajib diisi.');
                window.history.back();
              </script>";
        exit;
    }

    $query_insert = "INSERT INTO checkoutfinish (namabarang, pembeli, invoice, rekbank, harga, qty, total_harga) 
                     VALUES ('$namabarang', '$pembeli', '$invoice', '$rekbank', '$harga', '$qty', '$total_harga')";

    if (mysqli_query($koneksi, $query_insert)) {
        $id_terakhir = mysqli_insert_id($koneksi);
        echo "<script>
                alert('Transaksi Berhasil Disimpan!');
                window.location.href = 'checkout_finish.php?id=" . $id_terakhir . "';
              </script>";
        exit;
    } else {
        die("Gagal menyimpan transaksi: " . mysqli_error($koneksi));
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5 mb-4">
            <div class="card p-3 shadow-sm text-center">
                <img src="assets/images/<?php echo $produk['image']; ?>" class="img-fluid mx-auto mb-3" style="max-height: 200px; object-fit: contain;">
                <h4 class="fw-bold"><?php echo $produk['namaproduk']; ?></h4>
                <p class="text-muted small"><?php echo $produk['ket']; ?></p>
                <h3 class="text-primary fw-bold">Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></h3>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card p-4 shadow-sm">
                <h5 class="fw-bold mb-3">Formulir Pembelian</h5>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jumlah Beli</label>
                        <input type="number" name="qty" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Pembeli</label>
                        <input type="text" name="pembeli" class="form-control" placeholder="Masukkan nama Anda" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Metode Pembayaran</label>
                        <input type="text" name="rekbank" class="form-control" placeholder="Contoh: Transfer BCA" required>
                    </div>
                    <button type="submit" name="proses_checkout" class="btn btn-success w-100 fw-bold py-2 mt-2">
                        Bayar Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>