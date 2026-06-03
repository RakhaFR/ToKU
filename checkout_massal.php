<?php
session_start();

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';
include 'includes/header.php';

// Jika keranjang kosong, tidak boleh akses halaman ini
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>
            alert('Keranjang belanja kamu kosong, silakan pilih produk terlebih dahulu!');
            window.location.href = 'index.php';
          </script>";
    exit;
}

// PROSES SIMPAN KE DATABASE (CHECKOUT FINISH)
if (isset($_POST['proses_checkout_massal'])) {
    $pembeli = mysqli_real_escape_string($koneksi, $_POST['pembeli']);
    
    // Mengambil pilihan bank dan nomor rekening lalu menggabungkannya
    $pilihan_bank = isset($_POST['pilihan_bank']) ? trim($_POST['pilihan_bank']) : '';
    $nomor_rek    = isset($_POST['nomor_rek']) ? trim($_POST['nomor_rek']) : '';
    $rekbank_gabung = "Transfer " . $pilihan_bank . " - " . $nomor_rek;
    
    $rekbank = mysqli_real_escape_string($koneksi, $rekbank_gabung);

    // Membuat nomor invoice unik untuk menandai bahwa item-item ini dibeli barengan
    $invoice = "INV-" . date('Ymd') . "-" . strtoupper(substr(md5(time()), 0, 5));

    if (empty(trim($pembeli)) || empty($pilihan_bank) || empty($nomor_rek)) {
        echo "<script>
                alert('Gagal! Nama Pembeli, Pilihan Bank, dan Nomor Rekening wajib diisi.');
                window.history.back();
              </script>";
        exit;
    }

    $sukses_simpan = true;
    $pesan_error = "";

    // Loop keranjang session untuk mendaftarkan setiap item ke DB
    foreach ($_SESSION['cart'] as $id_produk => $qty) {
        $id_clean = mysqli_real_escape_string($koneksi, $id_produk);

        // Ambil detail data produk dari DB
        $query_p = mysqli_query($koneksi, "SELECT * FROM produk WHERE no = '$id_clean'");
        $produk  = mysqli_fetch_assoc($query_p);

        if ($produk) {
            $namabarang  = mysqli_real_escape_string($koneksi, $produk['namaproduk']); 
            $harga       = $produk['harga'];
            $total_harga = $harga * $qty;

            // Query INSERT sesuai database ter-update
            $insert_query = "INSERT INTO checkoutfinish (namabarang, pembeli, invoice, rekbank, harga, qty, total_harga, tanggal) 
                             VALUES ('$namabarang', '$pembeli', '$invoice', '$rekbank', '$harga', '$qty', '$total_harga', NOW())";

            if (!mysqli_query($koneksi, $insert_query)) {
                $sukses_simpan = false;
                $pesan_error = mysqli_error($koneksi);
            }
        }
    }

    // REDIRECT SELESAI - Diarahkan dengan parameter invoice menuju checkout_finish
    if ($sukses_simpan) {
        unset($_SESSION['cart']); // Kosongkan session keranjang
        echo "<script>
                alert('Selesai! Semua produk di keranjang berhasil dibayar.');
                window.location.href = 'checkout_finish.php?invoice=" . $invoice . "';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Gagal memproses pesanan. Error Database: " . $pesan_error . "');
              </script>";
    }
}

// Hitung Grand Total untuk keperluan visual rincian harga di form
$grand_total = 0;
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h4 class="fw-bold mb-4 text-dark">
                <i class="fas fa-credit-card text-success me-2"></i>Konfirmasi Pembayaran Massal
            </h4>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-light p-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-list me-2"></i>Rincian Barang yang Dibeli</h6>
                </div>
                <ul class="list-group list-group-flush">
                    <?php
                    foreach ($_SESSION['cart'] as $id_produk => $qty):
                        $id_clean = mysqli_real_escape_string($koneksi, $id_produk);
                        $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE no = '$id_clean'");
                        $produk = mysqli_fetch_assoc($query);

                        if ($produk):
                            $total_harga = $produk['harga'] * $qty;
                            $grand_total += $total_harga;
                    ?>
                            <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($produk['namaproduk']); ?></span>
                                    <small class="text-muted"><?php echo $qty; ?> x Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></small>
                                </div>
                                <span class="fw-bold text-primary">Rp<?php echo number_format($total_harga, 0, ',', '.'); ?></span>
                            </li>
                    <?php
                        endif;
                    endforeach;
                    ?>
                    <li class="list-group-item p-3 bg-light d-flex justify-content-between align-items-center fw-bold fs-5 text-dark">
                        <span>Total Keseluruhan:</span>
                        <span class="text-success">Rp<?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                    </li>
                </ul>
            </div>

            <div class="card shadow-sm border-0 rounded-4 p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Pembeli</label>
                        <input type="text" name="pembeli" class="form-control" value="<?php echo isset($_SESSION['user']) ? htmlspecialchars(explode("@", $_SESSION['user'])[0]) : ''; ?>" placeholder="Nama lengkap pembeli" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Bank Pembayaran</label>
                        <select name="pilihan_bank" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Bank --</option>
                            <option value="BCA">BCA (Bank Central Asia)</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BRI">BRI (Bank Rakyat Indonesia)</option>
                            <option value="BNI">BNI (Bank Negara Indonesia)</option>
                            <option value="BSI">BSI (Bank Syariah Indonesia)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nomor Rekening Anda</label>
                        <input type="text" name="nomor_rek" class="form-control" placeholder="Masukkan nomor rekening pembayaran" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        <div class="form-text small text-muted">Satu metode pembayaran ini akan digunakan untuk melunasi seluruh item di atas.</div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="keranjang_detail.php" class="btn btn-outline-secondary px-4 py-2 fw-semibold rounded-3">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Keranjang
                        </a>
                        <button type="submit" name="proses_checkout_massal" class="btn btn-success px-4 py-2 fw-bold rounded-3 shadow-sm" onclick="return confirm('Apakah Anda yakin data pembayaran sudah benar?')">
                            <i class="fas fa-check-circle me-2"></i>Konfirmasi & Bayar Sekarang
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>