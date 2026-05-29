<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['status'] != 0) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';
/** @var mysqli $koneksi */

if (isset($_POST['proses_edit_video'])) {
    $id_video = mysqli_real_escape_string($koneksi, $_POST['id_video']);
    $title    = mysqli_real_escape_string($koneksi, $_POST['title']);
    $link     = mysqli_real_escape_string($koneksi, $_POST['video_link']);

    $update = mysqli_query($koneksi, "UPDATE youtube SET title='$title', video_link='$link' WHERE no='$id_video'");

    if ($update) {
        $_SESSION['edit_sukses'] = "Data playlist video YouTube kamu telah berhasil diperbarui.";
        header("Location: admin_media.php");
        exit;
    } else {
        die("Gagal memperbarui data video: " . mysqli_error($koneksi));
    }
}

if (isset($_POST['proses_edit_carousel'])) {
    $id_carousel = mysqli_real_escape_string($koneksi, $_POST['id_carousel']);
    $title_car   = mysqli_real_escape_string($koneksi, $_POST['title']);
    
    if ($_FILES['image_file']['name'] != "") {
        $filename = $_FILES['image_file']['name'];
        $tempname = $_FILES['image_file']['tmp_name'];
        $folder   = "../assets/images/" . $filename;
        
        if (move_uploaded_file($tempname, $folder)) {
            $update_car = mysqli_query($koneksi, "UPDATE carousel SET title='$title_car', namapic='$filename' WHERE no='$id_carousel'");
        } else {
            die("Gagal mengunggah berkas gambar carousel.");
        }
    } else {
        $update_car = mysqli_query($koneksi, "UPDATE carousel SET title='$title_car' WHERE no='$id_carousel'");
    }

    if ($update_car) {
        $_SESSION['edit_sukses'] = "Data banner promosi carousel telah berhasil diperbarui.";
        header("Location: admin_media.php");
        exit;
    } else {
        die("Gagal memperbarui data carousel: " . mysqli_error($koneksi));
    }
}

$query_video = mysqli_query($koneksi, "SELECT * FROM youtube ORDER BY no ASC");
$query_carousel = mysqli_query($koneksi, "SELECT * FROM carousel ORDER BY no ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Media Utama - Panel Admin</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin_media.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body>

    <div class="wrapper">
    <?php include '../includes/sidebar_admin.php'; ?>

        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm border border-light">
                <div>
                    <h4 class="fw-bold text-dark mb-0">Kelola Media Penjualan</h4>
                    <p class="text-muted small mb-0">Konfigurasi visual klip video edukasi dan banner slider utama index toko.</p>
                </div>
            </div>

            <div class="card card-custom bg-white shadow-sm border border-light mb-4">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-images text-primary me-2"></i>Kelola Banner Carousel Utama</h5>
                    <span class="badge bg-light text-primary border rounded-pill fw-bold">Slide Aktif: <?php echo mysqli_num_rows($query_carousel); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="8%">No</th>
                                    <th width="18%">Pratinjau Gambar</th>
                                    <th width="59%">Judul / Keterangan Promosi Banner</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no_c = 1;
                                while ($row_c = mysqli_fetch_assoc($query_carousel)) {
                                ?>
                                    <tr>
                                        <td><strong>#<?php echo $no_c++; ?></strong></td>
                                        <td><img src="../assets/images/<?php echo $row_c['namapic']; ?>" class="img-preview shadow-sm" alt="Banner"></td>
                                        <td><span class="fw-semibold text-dark"><?php echo htmlspecialchars($row_c['title']); ?></span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold btn-edit-carousel"
                                                data-id="<?php echo $row_c['no']; ?>"
                                                data-title="<?php echo htmlspecialchars($row_c['title']); ?>"
                                                data-img="<?php echo $row_c['namapic']; ?>">
                                                <i class="fas fa-sliders-h me-1"></i>Edit Banner
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card card-custom bg-white shadow-sm border border-light mb-2">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-dark mb-0"><i class="fab fa-youtube text-danger me-2"></i>Kelola Playlist Media Video</h5>
                    <span class="badge bg-light text-danger border rounded-pill fw-bold">Tersimpan: <?php echo mysqli_num_rows($query_video); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="8%">No</th>
                                    <th width="42%">Judul Klip Informasi</th>
                                    <th width="35%">Link URL Source</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no_v = 1;
                                while ($row_v = mysqli_fetch_assoc($query_video)) {
                                ?>
                                    <tr>
                                        <td><strong>#<?php echo $no_v++; ?></strong></td>
                                        <td><span class="fw-semibold text-dark"><?php echo htmlspecialchars($row_v['title']); ?></span></td>
                                        <td><code class="text-truncate d-block" style="max-width: 260px;"><?php echo htmlspecialchars($row_v['video_link']); ?></code></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary rounded-3 px-3 fw-bold btn-edit-video"
                                                data-id="<?php echo $row_v['no']; ?>"
                                                data-title="<?php echo htmlspecialchars($row_v['title']); ?>"
                                                data-link="<?php echo htmlspecialchars($row_v['video_link']); ?>">
                                                <i class="fas fa-edit me-1"></i>Edit Link
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditVideo" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header bg-light border-bottom-0" style="border-radius: 14px 14px 0 0;">
                    <h5 class="modal-title fw-bold text-dark"><i class="fab fa-youtube text-danger me-2"></i>Edit Link Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_media.php" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_video" id="edit_id_video">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Judul Informasi Klip</label>
                            <input type="text" name="title" id="edit_title_video" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Link URL Source Youtube</label>
                            <input type="url" name="video_link" id="edit_link_video" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light p-3" style="border-radius: 0 0 14px 14px;">
                        <button type="button" class="btn btn-light rounded-3 px-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="proses_edit_video" class="btn btn-primary rounded-3 px-4 fw-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditCarousel" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header bg-light border-bottom-0" style="border-radius: 14px 14px 0 0;">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-images text-primary me-2"></i>Edit Slider Carousel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_media.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_carousel" id="edit_id_carousel">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Judul Promosi Banner</label>
                            <input type="text" name="title" id="edit_title_carousel" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary d-block">Gambar Banner Saat Ini</label>
                            <img src="" id="preview_img_carousel" class="w-100 rounded-3 mb-2 border shadow-sm" style="height: 160px; object-fit: cover;">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary">Ganti Gambar Baru <small class="text-muted fw-normal">(Kosongkan jika tidak diganti)</small></label>
                            <input type="file" name="image_file" class="form-control rounded-3" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light p-3" style="border-radius: 0 0 14px 14px;">
                        <button type="button" class="btn btn-light rounded-3 px-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="proses_edit_carousel" class="btn btn-primary rounded-3 px-4 fw-bold">Update Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSukses" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow text-center p-4" style="border-radius: 16px;">
                <div class="mx-auto my-3 text-success d-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle" style="width: 60px; height: 60px;">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Berhasil!</h5>
                <p class="text-muted small mb-4" id="teks_sukses">Data media jualan kamu telah berhasil diperbarui.</p>
                <button type="button" class="btn btn-success w-100 rounded-3 fw-bold py-2" data-bs-dismiss="modal">Oke, Paham</button>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        const modalEditVid = new bootstrap.Modal(document.getElementById('modalEditVideo'));
        document.querySelectorAll('.btn-edit-video').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id_video').value = this.getAttribute('data-id');
                document.getElementById('edit_title_video').value = this.getAttribute('data-title');
                document.getElementById('edit_link_video').value = this.getAttribute('data-link');
                modalEditVid.show();
            });
        });

        const modalEditCar = new bootstrap.Modal(document.getElementById('modalEditCarousel'));
        document.querySelectorAll('.btn-edit-carousel').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id_carousel').value = this.getAttribute('data-id');
                document.getElementById('edit_title_carousel').value = this.getAttribute('data-title');
                document.getElementById('preview_img_carousel').src = "../assets/images/" + this.getAttribute('data-img');
                modalEditCar.show();
            });
        });

        <?php if (isset($_SESSION['edit_sukses'])) : ?>
            document.getElementById('teks_sukses').innerText = "<?php echo $_SESSION['edit_sukses']; ?>";
            const modalSukses = new bootstrap.Modal(document.getElementById('modalSukses'));
            modalSukses.show();
            <?php unset($_SESSION['edit_sukses']); ?>
        <?php endif; ?>
    </script>
</body>

</html>