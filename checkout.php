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
    
    // Mengambil pilihan bank dan nomor rekening lalu menggabungkannya
    $pilihan_bank = isset($_POST['pilihan_bank']) ? trim($_POST['pilihan_bank']) : '';
    $nomor_rek    = isset($_POST['nomor_rek']) ? trim($_POST['nomor_rek']) : '';
    $rekbank_gabung = "Transfer " . $pilihan_bank . " - " . $nomor_rek;
    
    $rekbank     = mysqli_real_escape_string($koneksi, $rekbank_gabung);
    $harga       = $produk['harga'];
    $qty         = (int)$_POST['qty'];
    $total_harga = $harga * $qty;
    $invoice     = "INV-" . date("Ymd") .  "-" . rand(10000, 99999);

    if (empty(trim($pembeli)) || empty($pilihan_bank) || empty($nomor_rek)) {
        echo "<script>
                alert('Gagal! Nama Pembeli, Pilihan Bank, dan Nomor Rekening wajib diisi.');
                window.history.back();
              </script>";
        exit;
    }

    $query_insert = "INSERT INTO checkoutfinish (namabarang, pembeli, invoice, rekbank, harga, qty, total_harga, tanggal) 
                 VALUES ('$namabarang', '$pembeli', '$invoice', '$rekbank', '$harga', '$qty', '$total_harga', NOW())";

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

$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM login WHERE user = '" . mysqli_real_escape_string($koneksi, $_SESSION['user']) . "'"));
$fulluser = $data['user'];
$username = explode("@", $fulluser)[0];
?>

<style>
    .produk-overlay-wrapper:hover .produk-caption-layer {
        opacity: 1;
        visibility: visible;
    }
    .produk-caption-layer {
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
</style>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5 mb-4">
            <div class="card p-3 shadow-sm text-center">
                <div class="position-relative produk-overlay-wrapper overflow-hidden rounded">
                    <img src="assets/images/<?php echo $produk['image']; ?>" class="img-fluid d-block mx-auto mb-3" style="max-height: 200px; object-fit: contain;">

                    <div class="produk-caption-layer position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center bg-dark bg-opacity-75 text-white p-3 text-center">
                        <p class="small mb-0"><?php echo htmlspecialchars($produk['ket']); ?></p>
                    </div>
                </div>
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
                        <input type="text" name="pembeli" class="form-control" placeholder="Masukkan nama Anda" required value="<?php echo isset($_SESSION['user']) ? htmlspecialchars(explode("@", $_SESSION['user'])[0]) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Bank Pembayaran</label>
                        <select name="pilihan_bank" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Bank --</option>
                            <option value="BCA">BCA (Bank Central Asia)</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BRI">BRI (Bank Rakyat Indonesia)</option>
                            <option value="BNI">BNI (Bank Negara Indonesia)</option>
                            <option value="BSI">BSI (Bank Syariah Indonesia)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor Rekening Anda</label>
                        <input type="text" name="nomor_rek" class="form-control" placeholder="Masukkan nomor rekening" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        <div class="form-text text-muted" style="font-size: 11px;">Hanya menerima angka tanpa spasi/tanda baca.</div>
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