<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['status'] != 0) {
    header("Location: ../login.php");
    exit;
}
include '../config/koneksi.php';
/** @var mysqli $koneksi */

// 1. PROSES TAMBAH PRODUK
if (isset($_POST['proses_tambah_produk'])) {
    $namaproduk = mysqli_real_escape_string($koneksi, $_POST['namaproduk']);
    $kode       = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $ket        = mysqli_real_escape_string($koneksi, $_POST['ket']);
    $harga      = mysqli_real_escape_string($koneksi, $_POST['harga']);

    $filename = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    if ($filename != "") {
        move_uploaded_file($tmp_name, "../assets/images/" . $filename);
    } else {
        $filename = "default.jpg";
    }

    $insert = mysqli_query($koneksi, "INSERT INTO produk (namaproduk, kode, ket, harga, image) VALUES ('$namaproduk', '$kode', '$ket', '$harga', '$filename')");
    if ($insert) {
        echo "<script>alert('Produk baru berhasil ditambahkan!'); window.location='admin_produk.php';</script>";
        exit;
    }
}

// 2. PROSES EDIT PRODUK
if (isset($_POST['proses_edit_produk'])) {
    $id_produk  = mysqli_real_escape_string($koneksi, $_POST['id_produk']);
    $namaproduk = mysqli_real_escape_string($koneksi, $_POST['namaproduk']);
    $kode       = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $ket        = mysqli_real_escape_string($koneksi, $_POST['ket']);
    $harga      = mysqli_real_escape_string($koneksi, $_POST['harga']);

    $filename = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    if ($filename != "") {
        move_uploaded_file($tmp_name, "../assets/images/" . $filename);
        $update = mysqli_query($koneksi, "UPDATE produk SET namaproduk='$namaproduk', kode='$kode', ket='$ket', harga='$harga', image='$filename' WHERE no='$id_produk'");
    } else {
        $update = mysqli_query($koneksi, "UPDATE produk SET namaproduk='$namaproduk', kode='$kode', ket='$ket', harga='$harga' WHERE no='$id_produk'");
    }

    if ($update) {
        echo "<script>alert('Data produk berhasil diperbarui!'); window.location='admin_produk.php';</script>";
        exit;
    }
}

// 3. PROSES HAPUS PRODUK
if (isset($_GET['hapus_produk'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus_produk']);
    $delete = mysqli_query($koneksi, "DELETE FROM produk WHERE no='$id_hapus'");
    if ($delete) {
        echo "<script>alert('Produk berhasil dihapus!'); window.location='admin_produk.php';</script>";
        exit;
    }
}


$query = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY no ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Panel Admin</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin_produk.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/sidebar_admin.php'; ?>

        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm border border-light">
                <div>
                    <h4 class="fw-bold text-dark mb-0">Kelola Produk Toko</h4>
                    <p class="text-muted small mb-0">Atur ketersediaan etalase komoditas jualan dengan efisien</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Produk
                </button>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary border-bottom">
                                <tr>
                                    <th class="ps-3" style="width: 80px;">ID</th>
                                    <th style="width: 80px;">Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori/Kode</th>
                                    <th>Keterangan</th>
                                    <th>Harga Pokok</th>
                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query) > 0) {
                                    while ($row = mysqli_fetch_assoc($query)) { ?>
                                        <tr>
                                            <td class="fw-bold text-secondary ps-3">#<?php echo $row['no']; ?></td>
                                            <td><img src="../assets/images/<?php echo $row['image']; ?>" class="rounded-3 border" style="width: 48px; height: 40px; object-fit: cover;"></td>
                                            <td><strong class="text-dark d-block text-wrap"><?php echo htmlspecialchars($row['namaproduk']); ?></strong></td>
                                            <td>
                                                <span class="badge bg-light text-dark fw-bold border">
                                                    <?php echo htmlspecialchars($row['kode']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <p class="text-muted mb-0 text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($row['ket']); ?>">
                                                    <?php echo htmlspecialchars($row['ket']); ?>
                                                </p>
                                            </td>
                                            <td class="text-dark fw-bold">Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                            <td class="text-center pe-3">
                                                <button class="btn btn-warning btn-sm rounded-3 px-2 me-1 btn-edit-produk" 
                                                        data-id="<?php echo $row['no']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['namaproduk']); ?>"
                                                        data-kode="<?php echo htmlspecialchars($row['kode']); ?>"
                                                        data-ket="<?php echo htmlspecialchars($row['ket']); ?>"
                                                        data-harga="<?php echo $row['harga']; ?>"
                                                        data-img="<?php echo $row['image']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm rounded-3 px-2 btn-delete-produk" 
                                                        data-id="<?php echo $row['no']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['namaproduk']); ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-box-open d-block fs-2 mb-2 text-secondary"></i>Belum ada data produk terdaftar.
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

    <div class="modal fade" id="modalTambahProduk" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-box me-2 text-primary"></i>Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_produk.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Komoditas / Produk</label>
                            <input type="text" name="namaproduk" class="form-control rounded-3" placeholder="Contoh: Kopi Robusta Premium" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Kategori Komoditas</label>
                                <select name="kode" class="form-select rounded-3" required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    <?php 
                                    $q_kat = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY kode ASC");
                                    while($row_kat = mysqli_fetch_assoc($q_kat)) {
                                        echo "<option value='".htmlspecialchars($row_kat['kode'])."'>".htmlspecialchars($row_kat['kode'])." (".htmlspecialchars($row_kat['kategori']).")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Harga Jual (Rupiah)</label>
                                <input type="number" name="harga" class="form-control rounded-3" placeholder="Contoh: 45000" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Deskripsi Singkat / Keterangan</label>
                            <textarea name="ket" class="form-control rounded-3" rows="3" placeholder="Tulis deskripsi spesifikasi produk disini..." required></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary">Pilih Berkas Gambar</label>
                            <input type="file" name="image" id="file-tambah" class="form-control rounded-3" accept="image/*" onchange="previewImage(this, 'preview-tambah', 'container-preview-tambah')">
                        </div>
                        <div id="container-preview-tambah" class="d-none mt-2 border p-2 text-center rounded-3 bg-light">
                            <small class="text-muted d-block mb-1">Pratinjau Gambar Baru:</small>
                            <img id="preview-tambah" src="#" alt="Pratinjau" class="img-fluid rounded border shadow-sm" style="max-height: 140px; object-fit: contain;">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-2">
                        <button type="button" class="btn btn-light rounded-3 px-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="proses_tambah_produk" class="btn btn-primary rounded-3 px-4 fw-bold">Tambah Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditProduk" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit me-2 text-warning"></i>Modifikasi Data Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_produk.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_produk" id="edit_id_produk">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Komoditas / Produk</label>
                            <input type="text" name="namaproduk" id="edit_nama_produk" class="form-control rounded-3" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Kategori Komoditas</label>
                                <select name="kode" id="edit_kode_produk" class="form-select rounded-3" required>
                                    <?php 
                                    // Mengambil ulang query data kategori untuk modal edit
                                    $q_kat_edit = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY kode ASC");
                                    while($row_kat_edit = mysqli_fetch_assoc($q_kat_edit)) {
                                        echo "<option value='".htmlspecialchars($row_kat_edit['kode'])."'>".htmlspecialchars($row_kat_edit['kode'])." (".htmlspecialchars($row_kat_edit['kategori']).")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Harga Jual (Rupiah)</label>
                                <input type="number" name="harga" id="edit_harga_produk" class="form-control rounded-3" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Deskripsi Singkat / Keterangan</label>
                            <textarea name="ket" id="edit_ket_produk" class="form-control rounded-3" rows="3" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary">Ganti File Gambar (Opsional)</label>
                            <input type="file" name="image" id="file-edit" class="form-control rounded-3" accept="image/*" onchange="previewImage(this, 'preview-edit', 'container-preview-edit')">
                        </div>
                        <div id="container-preview-edit" class="mt-2 border p-2 text-center rounded-3 bg-light">
                            <small class="text-muted d-block mb-1">Gambar Terpilih:</small>
                            <img id="preview-edit" src="#" alt="Pratinjau" class="img-fluid rounded border shadow-sm" style="max-height: 140px; object-fit: contain;">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-2">
                        <button type="button" class="btn btn-light rounded-3 px-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="proses_edit_produk" class="btn btn-warning rounded-3 px-4 fw-bold text-dark">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDeleteProduk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-body text-center p-4">
                    <i class="fas fa-exclamation-triangle text-danger fs-1 mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark mb-1">Hapus Komoditas?</h5>
                    <p class="text-muted small mb-4">Apakah Anda yakin ingin melenyapkan produk <strong class="text-dark" id="delete_nama_produk"></strong> dari etalase database?</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-3 w-50 fw-semibold btn-sm" data-bs-dismiss="modal">Batal</button>
                        <a id="btn_konfirmasi_hapus_produk" class="btn btn-danger rounded-3 w-50 fw-semibold btn-sm">Ya, Hapus</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input, previewId, containerId) {
            const container = document.getElementById(containerId);
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        const modalEdit = new bootstrap.Modal(document.getElementById('modalEditProduk'));
        document.querySelectorAll('.btn-edit-produk').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id_produk').value = this.getAttribute('data-id');
                document.getElementById('edit_nama_produk').value = this.getAttribute('data-nama');
                
                // Dropdown select secara otomatis akan memilih value yang cocok dengan attribute data-kode
                document.getElementById('edit_kode_produk').value = this.getAttribute('data-kode');
                
                document.getElementById('edit_ket_produk').value = this.getAttribute('data-get') || this.getAttribute('data-ket');
                document.getElementById('edit_harga_produk').value = this.getAttribute('data-harga');
                
                const currentImg = this.getAttribute('data-img');
                const previewEdit = document.getElementById('preview-edit');
                const containerEdit = document.getElementById('container-preview-edit');
                
                if (currentImg) {
                    previewEdit.src = "../assets/images/" + currentImg;
                    containerEdit.classList.remove('d-none');
                }
                
                modalEdit.show();
            });
        });

        const modalDelete = new bootstrap.Modal(document.getElementById('modalDeleteProduk'));
        document.querySelectorAll('.btn-delete-produk').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('delete_nama_produk').innerText = this.getAttribute('data-nama');
                document.getElementById('btn_konfirmasi_hapus_produk').setAttribute('href', 'admin_produk.php?hapus_produk=' + id);
                modalDelete.show();
            });
        });

        document.getElementById('modalTambahProduk').addEventListener('hidden.bs.modal', function() {
            document.getElementById('file-tambah').value = "";
            document.getElementById('preview-tambah').src = "#";
            document.getElementById('container-preview-tambah').classList.add('d-none');
        });
    </script>
</body>

</html>