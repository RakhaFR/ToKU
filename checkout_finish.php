<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';
include 'includes/header.php';

// Menangkap parameter, bisa berupa ID tunggal atau nomor Invoice massal
$id_order = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';
$invoice  = isset($_GET['invoice']) ? mysqli_real_escape_string($koneksi, $_GET['invoice']) : '';

$order_items = [];
$info_utama = [];

if (!empty($id_order)) {
    // Jalur A: Jika datang dari Beli Langsung (Menggunakan ID)
    $query_order = mysqli_query($koneksi, "SELECT * FROM checkoutfinish WHERE no = '$id_order'");
    $data = mysqli_fetch_assoc($query_order);
    if ($data) {
        $order_items[] = $data; // Masukkan ke array penampung item
        $info_utama = $data;    // Ambil info pembeli & invoice dari data ini
    }
} elseif (!empty($invoice)) {
    // Jalur B: Jika datang dari Checkout Massal (Menggunakan Nomor Invoice)
    $query_order = mysqli_query($koneksi, "SELECT * FROM checkoutfinish WHERE invoice = '$invoice' ORDER BY no ASC");
    while ($row = mysqli_fetch_assoc($query_order)) {
        $order_items[] = $row;  // Tampung semua produk dengan invoice yang sama
    }
    if (!empty($order_items)) {
        $info_utama = $order_items[0]; // Ambil sampel info pembeli & invoice dari item pertama
    }
}

// Jika data transaksi sama sekali tidak ditemukan di database
if (empty($order_items)) {
    echo "<script>alert('Data transaksi tidak ditemukan.'); window.location.href = 'index.php';</script>";
    exit;
}

// Hitung akumulasi grand total untuk tampilan struk
$grand_total_harga = 0;
?>

<style>
    /* CSS khusus untuk menyembunyikan tombol navigasi saat struk dicetak */
    @media print {
        .no-print, .navbar, footer {
            display: none !important;
        }
        body {
            background-color: #fff !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
    .struk-box {
        border: 1px dashed #ccc;
        background-color: #fafafa;
    }
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-4 p-4 bg-white text-center">
                
                <div class="no-print mb-3 text-success">
                    <i class="fas fa-check-circle fa-3x"></i>
                    <h3 class="fw-bold text-success mt-2 mb-1">Pembayaran Berhasil!</h3>
                    <p class="text-muted small mb-4">Simpan bukti transaksi halaman nota di bawah ini.</p>
                </div>
                
                <div class="struk-box rounded-4 text-start p-4 mb-4">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold text-dark mb-1"><i class="fas fa-shopping-bag me-2 text-primary"></i>ToKU ONLINE SHOP</h5>
                        <small class="text-muted">Bukti Pembayaran Sah / Struk Belanja</small>
                    </div>
                    
                    <div class="row g-2 mb-3 border-bottom pb-3 small text-dark">
                        <div class="col-4 text-muted">No. Invoice</div>
                        <div class="col-8 fw-bold">: <?php echo htmlspecialchars($info_utama['invoice']); ?></div>
                        
                        <div class="col-4 text-muted">Nama Pembeli</div>
                        <div class="col-8 fw-semibold">: <?php echo htmlspecialchars($info_utama['pembeli']); ?></div>
                        
                        <div class="col-4 text-muted">Metode Bayar</div>
                        <div class="col-8">: <?php echo htmlspecialchars($info_utama['rekbank']); ?></div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 14px;">
                            <thead>
                                <tr class="border-bottom text-muted">
                                    <th style="width: 55%;">Nama Produk</th>
                                    <th class="text-center" style="width: 15%;">Qty</th>
                                    <th class="text-end" style="width: 30%;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($order_items as $item): 
                                    $grand_total_harga += $item['total_harga'];
                                ?>
                                <tr>
                                    <td class="text-dark py-2 fw-medium"><?php echo htmlspecialchars($item['namabarang']); ?></td>
                                    <td class="text-center text-secondary py-2"><?php echo $item['qty']; ?>x</td>
                                    <td class="text-end text-dark fw-bold py-2">Rp<?php echo number_format($item['total_harga'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="border-top pt-3 mt-2">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <span class="fw-bold text-dark fs-6">Total Pembayaran</span>
                            </div>
                            <div class="col-6 text-end">
                                <span class="fw-bold text-primary fs-4">Rp<?php echo number_format($grand_total_harga, 0, ',', '.'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 no-print">
                    <div class="col-6">
                        <button onclick="window.print()" class="btn btn-outline-secondary w-100 fw-bold py-2 rounded-3">
                            <i class="fas fa-print me-2"></i>Cetak Struk
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="index.php" class="btn btn-primary w-100 fw-bold py-2 rounded-3">
                            <i class="fas fa-home me-1"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>