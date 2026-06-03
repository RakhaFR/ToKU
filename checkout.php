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

// ==========================================
// OPSI A: LOGIKA MASUKKAN KE KERANJANG DIRECT
// ==========================================
if (isset($_POST['proses_masuk_keranjang'])) {
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id_produk])) {
        $_SESSION['cart'][$id_produk] += $qty;
    } else {
        $_SESSION['cart'][$id_produk] = $qty;
    }

    echo "<script>
            alert('Produk berhasil dimasukkan ke keranjang belanja!');
            window.location.href = 'index.php';
          </script>";
    exit;
}

// ==========================================
// OPSI B: LOGIKA BELI LANGSUNG SEKARANG
// ==========================================
if (isset($_POST['proses_checkout_langsung'])) {
    $namabarang  = mysqli_real_escape_string($koneksi, $produk['namaproduk']);
    $pembeli     = mysqli_real_escape_string($koneksi, $_POST['pembeli']);

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

    $query_insert = "INSERT INTO checkoutfinish (namabarang, pembeli, invoice, rekbank, harga, qty, total_harga) 
                     VALUES ('$namabarang', '$pembeli', '$invoice', '$rekbank', '$harga', '$qty', '$total_harga')";

    if (mysqli_query($koneksi, $query_insert)) {
        $id_terakhir = mysqli_insert_id($koneksi);
        // Pastikan tidak ada spasi renggang di bagian redirect .id_terakhir
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

    /* Menghilangkan tanda panah bawaan input number */
    .input-qty-custom::-webkit-outer-spin-button,
    .input-qty-custom::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .input-qty-custom {
        -moz-appearance: textfield;
        text-align: center;
        font-weight: bold;
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
                <h5 class="fw-bold mb-3">Formulir Pilihan Pembelian</h5>

                <form method="POST" id="formCheckout" action="">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jumlah Beli</label>
                        <div class="input-group" style="max-width: 140px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="ubahQty(-1)">
                                <i class="fas fa-minus fa-xs"></i>
                            </button>
                            <input type="number" name="qty" id="inputQty" class="form-control input-qty-custom bg-white" value="1" min="1" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="ubahQty(1)">
                                <i class="fas fa-plus fa-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Pembeli</label>
                        <input type="text" name="pembeli" class="form-control" placeholder="Masukkan nama Anda" required value="<?php echo isset($_SESSION['user']) ? htmlspecialchars(explode("@", $_SESSION['user'])[0]) : ''; ?>">
                    </div>

                    <div class="border p-3 rounded bg-light mb-3">
                        <p class="small fw-bold text-secondary mb-2"><i class="fas fa-info-circle me-1"></i>Isian Rekening (Untuk Beli Langsung)</p>
                        <div class="mb-2">
                            <label class="form-label text-muted mb-1" style="font-size: 11px;">Pilih Bank Pembayaran</label>
                            <select name="pilihan_bank" id="pilihanBank" class="form-select form-select-sm">
                                <option value="" disabled selected>-- Pilih Bank --</option>
                                <option value="BCA">BCA (Bank Central Asia)</option>
                                <option value="Mandiri">Mandiri</option>
                                <option value="BRI">BRI (Bank Rakyat Indonesia)</option>
                                <option value="BNI">BNI (Bank Negara Indonesia)</option>
                                <option value="BSI">BSI (Bank Syariah Indonesia)</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label text-muted mb-1" style="font-size: 11px;">Nomor Rekening Anda</label>
                            <input type="text" name="nomor_rek" id="nomorRek" class="form-control form-control-sm" placeholder="Masukkan nomor rekening" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-primary w-100 fw-bold btn-sm py-2" onclick="submitSebagaiKeranjang()">
                                <i class="fas fa-cart-plus me-1"></i> + Keranjang
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="proses_checkout_langsung" class="btn btn-success w-100 fw-bold btn-sm py-2" onclick="return validasiLangsung()">
                                <i class="fas fa-bolt me-1"></i> Beli Langsung
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function ubahQty(nilai) {
        var input = document.getElementById('inputQty');
        var qtySaatIni = parseInt(input.value);
        var qtyBaru = qtySaatIni + nilai;

        if (qtyBaru >= 1) {
            input.value = qtyBaru;
        }
    }

    function submitSebagaiKeranjang() {
        var form = document.getElementById('formCheckout');
        var inputHidden = document.createElement('input');
        inputHidden.type = 'hidden';
        inputHidden.name = 'proses_masuk_keranjang';
        inputHidden.value = '1';
        form.appendChild(inputHidden);

        document.getElementById('pilihanBank').required = false;
        document.getElementById('nomorRek').required = false;
        form.submit();
    }

    function validasiLangsung() {
        var bank = document.getElementById('pilihanBank').value;
        var rek = document.getElementById('nomorRek').value;
        if (bank === "" || rek === "") {
            alert("Harap lengkapi bank pembayaran dan nomor rekening terlebih dahulu untuk melakukan pembelian langsung!");
            return false;
        }
        return true;
    }
</script>

<?php include 'includes/footer.php'; ?>