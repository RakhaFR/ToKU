<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';
include 'includes/header.php';

$where_clause = "";
$keyword = "";
$kat_pilihan = "";

// 1. Kondisi jika Search Engine Aktif
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $keyword = mysqli_real_escape_string($koneksi, trim($_GET['search']));
    $where_clause = "WHERE produk.namaproduk LIKE '%$keyword%'";
} 
// 2. Kondisi jika Filter Kategori dari Dropdown Aktif
elseif (isset($_GET['kategori']) && !empty(trim($_GET['kategori']))) {
    $kat_pilihan = mysqli_real_escape_string($koneksi, trim($_GET['kategori']));
    $where_clause = "WHERE produk.kode = '$kat_pilihan'";
}

$query_produk = mysqli_query($koneksi, "SELECT produk.*, kategori.kategori 
                                        FROM produk 
                                        LEFT JOIN kategori ON produk.kode = kategori.kode 
                                        $where_clause 
                                        ORDER BY produk.no ASC");

$nama_kategori_aktif = "Kategori";
if (!empty($kat_pilihan)) {
    $cek_kat = mysqli_query($koneksi, "SELECT kategori FROM kategori WHERE kode = '$kat_pilihan'");
    if ($res_kat = mysqli_fetch_assoc($cek_kat)) {
        $nama_kategori_aktif = $res_kat['kategori'];
    }
}

$query_carousel = mysqli_query($koneksi, "SELECT * FROM carousel");
$query_video    = mysqli_query($koneksi, "SELECT * FROM youtube");
?>

<div class="container my-5">

    <!-- JELAJAH BANNER CAROUSEL -->
    <div id="carouselExampleCaptions" class="carousel slide custom-carousel mb-5" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $active = true;
            if(mysqli_num_rows($query_carousel) > 0) {
                while ($row_carousel = mysqli_fetch_assoc($query_carousel)) {
                ?>
                    <div class="carousel-item bg-white <?php echo $active ? 'active' : ''; ?>">
                        <img src="assets/images/<?php echo $row_carousel['namapic']; ?>" class="d-block w-100 h-100" alt="<?php echo $row_carousel['title']; ?>">
                        <div class="carousel-overlay"></div>
                        <div class="carousel-caption d-none d-md-block text-start mb-4 px-4">
                            <span class="badge bg-primary mb-2 px-3 py-2 text-uppercase tracking-wider fw-bold fs-8 rounded-pill">Rekomendasi Utama</span>
                            <h2 class="fw-bold text-white display-6 mb-2"><?php echo $row_carousel['title']; ?></h2>
                            <p class="text-white-50 fs-6 mb-0">Dapatkan kualitas produk terbaik dengan promo terbatas minggu ini.</p>
                        </div>
                    </div>
                <?php 
                    $active = false;
                }
            } else { ?>
                <div class="carousel-item active d-flex align-items-center justify-content-center">
                    <div class="text-center text-white p-5">
                        <h4 class="fw-bold">Selamat Datang di ToKU</h4>
                        <p class="text-muted small">Banner promosi dapat dikonfigurasi melalui tabel database carousel.</p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- TARGET JANGKAR KATALOG PRODUK -->
    <div id="katalog" class="mb-5 pt-3">
        
        <!-- ALERT STATUS PENCARIAN ATAU FILTER KATEGORI -->
        <?php if (!empty($keyword)): ?>
            <div class="alert alert-light border shadow-sm rounded-4 d-flex justify-content-between align-items-center p-3 mb-4">
                <div>
                    <i class="fas fa-search text-primary me-2"></i>
                    Hasil pencarian untuk kata kunci: <strong class="text-dark">"<?php echo htmlspecialchars($keyword); ?>"</strong>
                </div>
                <a href="index.php#katalog" class="btn btn-sm btn-secondary rounded-pill px-3 text-decoration-none">
                    <i class="fas fa-times me-1"></i> Bersihkan Search
                </a>
            </div>
        <?php elseif (!empty($kat_pilihan)): ?>
            <div class="alert alert-light border shadow-sm rounded-4 d-flex justify-content-between align-items-center p-3 mb-4">
                <div>
                    <i class="fas fa-tag text-success me-2"></i>
                    Menampilkan produk dengan kategori: <strong class="text-success">"<?php echo htmlspecialchars($nama_kategori_aktif); ?>"</strong>
                </div>
                <a href="index.php#katalog" class="btn btn-sm btn-secondary rounded-pill px-3 text-decoration-none">
                    <i class="fas fa-times me-1"></i> Hapus Filter
                </a>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-title mb-0">Katalog Produk Unggulan</h3>
            <span class="badge bg-white text-primary border shadow-sm px-3 py-2 rounded-pill fw-bold fs-7">
                <i class="fas fa-box me-1"></i> Tersedia: <?php echo mysqli_num_rows($query_produk); ?> Produk
            </span>
        </div>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php 
            if(mysqli_num_rows($query_produk) > 0) {
                while ($row_produk = mysqli_fetch_assoc($query_produk)) {
                ?>
                    <div class="col">
                        <div class="card h-100 product-card shadow-sm">
                            <div class="product-img-wrapper">
                                <img src="assets/images/<?php echo $row_produk['image']; ?>" class="product-img" alt="<?php echo $row_produk['namaproduk']; ?>">
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                
                                <!-- BADGE KATEGORI GREEN SOFT -->
                                <div class="mb-2">
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 fs-8 fw-bold border border-success border-opacity-20">
                                        <i class="fas fa-tag me-1"></i>
                                        <?php echo (!empty($row_produk['kategori'])) ? htmlspecialchars($row_produk['kategori']) : 'Umum'; ?>
                                    </span>
                                </div>

                                <h6 class="fw-bold text-dark text-truncate mb-2" title="<?php echo $row_produk['namaproduk']; ?>" style="font-size: 15px;">
                                    <?php echo $row_produk['namaproduk']; ?>
                                </h6>
                                <p class="text-muted fs-7 flex-grow-1 mb-3" style="line-height: 1.5;">
                                    <?php echo (strlen($row_produk['ket']) > 60) ? substr($row_produk['ket'], 0, 57) . '...' : $row_produk['ket']; ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                                    <div>
                                        <small class="text-muted d-block fs-8 mb-0">Harga Spesial</small>
                                        <span class="text-primary fw-bold fs-5">Rp<?php echo number_format($row_produk['harga'], 0, ',', '.'); ?></span>
                                    </div>
                                    <a href="checkout.php?id=<?php echo $row_produk['no']; ?>" class="btn btn-primary rounded-3 px-3 py-2 btn-sm fw-bold shadow-sm">
                                        <i class="fas fa-shopping-cart me-1"></i> Beli
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                }
            } else { ?>
                <div class="col-12 w-100 text-center py-5 bg-white rounded-4 border shadow-sm my-2">
                    <i class="fas fa-search-minus fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-dark fw-bold mb-1">Produk Tidak Ditemukan</h5>
                    <p class="text-muted small mb-0">Maaf, barang dalam kategori atau pencarian tersebut belum tersedia.</p>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- AREA PLAYLIST VIDEO EDURIFIKASI -->
    <div id="edukasi" class="video-container p-4 mb-4">
        <div class="mb-4">
            <h3 class="section-title mb-1">Review & Panduan Video</h3>
            <p class="text-muted small mb-0 mt-4">Pilih klip rekaman di bilah kanan untuk langsung diputar tanpa memuat ulang halaman.</p>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7">
                <?php
                mysqli_data_seek($query_video, 0);
                $first_video = mysqli_fetch_assoc($query_video);
                
                $first_embed = "";
                if ($first_video) {
                    $raw_link = $first_video['video_link'];
                    if (strpos($raw_link, 'embed/') !== false) {
                        $first_embed = $raw_link;
                    } elseif (strpos($raw_link, 'youtu.be/') !== false) {
                        $parts = explode('youtu.be/', $raw_link);
                        $video_id = explode('?', $parts[1])[0];
                        $first_embed = "https://www.youtube.com/embed/" . $video_id;
                    } elseif (strpos($raw_link, 'watch?v=') !== false) {
                        $parts = explode('watch?v=', $raw_link);
                        $video_id = explode('&', $parts[1])[0];
                        $first_embed = "https://www.youtube.com/embed/" . $video_id;
                    } else {
                        $first_embed = $raw_link;
                    }
                ?>
                    <div class="ratio ratio-16x9 rounded-4 overflow-hidden border shadow-sm bg-dark">
                        <iframe id="mainVideoFrame" src="<?php echo $first_embed; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                <?php } else { ?>
                    <div class="bg-light text-muted d-flex flex-column align-items-center justify-content-center rounded-4 border border-dashed" style="height: 350px;">
                        <i class="fab fa-youtube fs-1 mb-2 text-danger"></i>
                        <span class="fw-medium">Belum ada video edukasi di database.</span>
                    </div>
                <?php } ?>
            </div>
            
            <div class="col-lg-5">
                <div class="playlist-card p-3 h-100 d-flex flex-column">
                    <h6 class="fw-bold text-dark px-2 mb-3"><i class="fab fa-youtube text-danger me-2"></i>Daftar Putar Info</h6>
                    <div class="list-group overflow-auto flex-grow-1 pe-1" style="max-height: 290px;">
                        <?php
                        mysqli_data_seek($query_video, 0);
                        if(mysqli_num_rows($query_video) > 0) {
                            while ($row_video = mysqli_fetch_assoc($query_video)) {
                                $loop_link = $row_video['video_link'];
                                $loop_id = "";

                                if (strpos($loop_link, 'embed/') !== false) {
                                    $parts = explode('embed/', $loop_link);
                                    $loop_id = explode('?', $parts[1])[0];
                                } elseif (strpos($loop_link, 'youtu.be/') !== false) {
                                    $parts = explode('youtu.be/', $loop_link);
                                    $loop_id = explode('?', $parts[1])[0];
                                } elseif (strpos($loop_link, 'watch?v=') !== false) {
                                    $parts = explode('watch?v=', $loop_link);
                                    $loop_id = explode('&', $parts[1])[0];
                                }

                                $embed_url = "https://www.youtube.com/embed/" . $loop_id;
                                $thumbnail_url = (!empty($loop_id)) ? "https://img.youtube.com/vi/" . $loop_id . "/mqdefault.jpg" : "assets/images/no-video.jpg";
                            ?>
                                <div class="list-group-item playlist-item d-flex align-items-center p-2 shadow-sm bg-white mb-2 video-selector" 
                                     data-embed="<?php echo $embed_url; ?>" 
                                     style="cursor: pointer;">
                                    
                                    <div class="me-3 position-relative" style="width: 90px; min-width: 90px; height: 55px;">
                                        <img src="<?php echo $thumbnail_url; ?>" class="w-100 h-100 rounded border" style="object-fit: cover;" alt="Thumbnail">
                                        <div class="position-absolute top-50 start-50 translate-middle text-white bg-dark bg-opacity-75 rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; font-size: 9px;">
                                            <i class="fas fa-play"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="text-truncate">
                                        <p class="mb-0 fw-semibold text-dark text-truncate fs-7" title="<?php echo $row_video['title']; ?>">
                                            <?php echo $row_video['title']; ?>
                                        </p>
                                        <small class="text-muted fs-8"><i class="fab fa-youtube text-danger me-1"></i>Klik untuk putar</small>
                                    </div>
                                </div>
                            <?php 
                            }
                        } else {
                            echo "<p class='text-muted small text-center my-auto py-4'>Tidak ada daftar putar video.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const playlistItems = document.querySelectorAll('.video-selector');
    const mainVideoFrame = document.getElementById('mainVideoFrame');

    playlistItems.forEach(item => {
        item.addEventListener('click', function() {
            const newEmbedUrl = this.getAttribute('data-embed');
            if(mainVideoFrame && newEmbedUrl) {
                mainVideoFrame.src = newEmbedUrl + "?autoplay=1";
                playlistItems.forEach(i => i.style.borderLeftColor = 'transparent');
                this.style.borderLeftColor = '#0d6efd';
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>