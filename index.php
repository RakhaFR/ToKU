<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Sinkronisasi file agar tidak tabrakan data database
include_once 'config/koneksi.php';
include 'includes/header.php';

$where_clause = "";
$info_pencarian = "";

// 1. Kondisi jika Search Engine Header Aktif
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $keyword = mysqli_real_escape_string($koneksi, trim($_GET['search']));
    $where_clause = "WHERE produk.namaproduk LIKE '%$keyword%'";
    $info_pencarian = "Hasil pencarian untuk: <strong>\"" . htmlspecialchars($keyword) . "\"</strong>";
} 
// 2. Kondisi jika Filter Kategori dari Dropdown Aktif
elseif (isset($_GET['kategori']) && !empty(trim($_GET['kategori']))) {
    $kat_pilihan = mysqli_real_escape_string($koneksi, trim($_GET['kategori']));
    $where_clause = "WHERE produk.kode = '$kat_pilihan'";
    $info_pencarian = "Menampilkan Kategori: <strong>\"" . htmlspecialchars($kat_pilihan) . "\"</strong>";
}

// Query mengambil data produk
$query_produk = mysqli_query($koneksi, "SELECT produk.*, kategori.kategori 
                                        FROM produk 
                                        LEFT JOIN kategori ON produk.kode = kategori.kode 
                                        $where_clause 
                                        ORDER BY produk.no ASC");

$query_carousel = mysqli_query($koneksi, "SELECT * FROM carousel");
$query_video    = mysqli_query($koneksi, "SELECT * FROM youtube");
?>

<div class="container my-5">

    <div id="promoCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="carousel-inner rounded-3 shadow-sm" style="max-height: 300px;">
            <?php
            $aktif = true;
            while ($row_carousel = mysqli_fetch_assoc($query_carousel)) {
            ?>
                <div class="carousel-item <?php echo $aktif ? 'active' : ''; ?>">
                    <img src="assets/images/<?php echo $row_carousel['namapic']; ?>" class="d-block w-100" alt="..." style="object-fit: cover; height: 300px;">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                        <h5><?php echo $row_carousel['title']; ?></h5>
                    </div>
                </div>
            <?php 
                $aktif = false;
            } ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <?php if (!empty($info_pencarian)): ?>
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-4 shadow-sm border-0">
            <span><?php echo $info_pencarian; ?></span>
            <a href="index.php" class="btn btn-secondary btn-sm fw-bold">Reset / Tampilkan Semua</a>
        </div>
    <?php endif; ?>

    <h4 class="fw-bold mb-3">Katalog Produk Unggulan</h4>
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-5">
        <?php 
        if(mysqli_num_rows($query_produk) > 0) {
            while ($row_produk = mysqli_fetch_assoc($query_produk)) {
            ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 rounded-3">
                        <img src="assets/images/<?php echo $row_produk['image']; ?>" class="card-img-top p-2" alt="..." style="height: 180px; object-fit: contain; background: #f8f9fa;">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-light text-muted align-self-start mb-2 small border"><?php echo htmlspecialchars($row_produk['kategori'] ?? 'Umum'); ?></span>
                            <h6 class="card-title fw-bold text-dark mb-1"><?php echo $row_produk['namaproduk']; ?></h6>
                            <p class="card-text text-muted small text-truncate mb-3"><?php echo $row_produk['ket']; ?></p>
                            <div class="mt-auto">
                                <h5 class="text-primary fw-bold mb-2">Rp<?php echo number_format($row_produk['harga'], 0, ',', '.'); ?></h5>
                                <a href="checkout.php?id=<?php echo $row_produk['no']; ?>" class="btn btn-primary btn-sm w-100 fw-bold rounded-2">
                                    Beli Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
            }
        } else { ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Produk tidak ditemukan atau tidak tersedia.</p>
            </div>
        <?php } ?>
    </div>

    <h4 class="fw-bold mb-3">Review & Panduan Video</h4>
    <div class="row g-4 bg-light p-3 rounded border mb-5">
        <div class="col-lg-7">
            <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm bg-dark">
                <iframe id="iframeVideoUtama" src="" title="YouTube player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="list-group overflow-auto" style="max-height: 315px;">
                <?php
                $first_video_url = "";
                $is_first = true;

                while ($row_video = mysqli_fetch_assoc($query_video)) {
                    if ($is_first) {
                        $first_video_url = $row_video['video_link'];
                        $is_first = false;
                    }
                ?>
                    <button type="button" class="list-group-item list-group-item-action p-2 d-flex align-items-center gap-2" 
                            onclick="gantiVideo('<?php echo $row_video['video_link']; ?>')">
                        <div class="bg-danger text-white rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                            ▶
                        </div>
                        <div class="text-truncate">
                            <span class="fw-semibold text-dark small d-block text-truncate"><?php echo $row_video['title']; ?></span>
                            <small class="text-muted" style="font-size: 11px;">Klik untuk memutar</small>
                        </div>
                    </button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
function gantiVideo(urlEmbed) {
    if(urlEmbed !== "") {
        document.getElementById('iframeVideoUtama').src = urlEmbed;
    }
}
document.addEventListener("DOMContentLoaded", function() {
    var videoPertama = "<?php echo $first_video_url; ?>";
    if(videoPertama !== "") {
        gantiVideo(videoPertama);
    }
});
</script>

<?php include 'includes/footer.php'; ?>