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
        move_uploaded_file($tmp_name, "../../assets/images/" . $filename);
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

// 3. PROSES DELETE PRODUK
if (isset($_GET['hapus_produk'])) {
    $id_hapus = $_GET['hapus_produk'];
    $query_hapus = mysqli_query($koneksi, "DELETE FROM produk WHERE no = '$id_hapus'");
    if ($query_hapus) {
        echo "<script>alert('Produk berhasil dihapus!'); window.location='admin_produk.php';</script>";
        exit;
    }
}


$query_produk = mysqli_query($koneksi, "SELECT produk.*, kategori.kategori FROM produk LEFT JOIN kategori ON produk.kode = kategori.kode ORDER BY produk.no ASC");
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
            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded-4 shadow-sm border border-light">
                <div>
                    <h4 class="fw-bold text-dark mb-0">Manajemen Produk</h4>
                    <p class="text-muted small mb-0">Kelola katalog komoditas barang dagangan.</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm rounded-3 fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Produk Baru
                </button>
            </div>

            <div class="card card-custom bg-white shadow-sm border border-light mb-2">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">ID</th>
                                    <th width="12%">Foto</th>
                                    <th width="20%">Nama Produk</th>
                                    <th width="15%">Kategori (Kode)</th>
                                    <th width="20%">Keterangan</th>
                                    <th width="12%">Harga</th>
                                    <th width="13%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($query_produk)) { ?>
                                    <tr>
                                        <td class="fw-bold text-secondary">#<?php echo $row['no']; ?></td>
                                        <td><img src="../assets/images/<?php echo $row['image']; ?>" class="rounded-3 border" style="width: 48px; height: 40px; object-fit: cover;"></td>
                                        <td><strong class="text-dark d-block text-wrap"><?php echo htmlspecialchars($row['namaproduk']); ?></strong></td>
                                        <td>
                                            <span class="badge bg-info text-dark fw-bold">
                                                <?php echo htmlspecialchars($row['kategori'] ?? 'Tanpa Kategori'); ?>
                                                (<?php echo htmlspecialchars($row['kode']); ?>)
                                            </span>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-0 text-truncate" style="max-width: 180px;"><?php echo htmlspecialchars($row['ket']); ?></p>
                                        </td>
                                        <td class="text-primary fw-bold">Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-warning px-2 py-1 btn-edit-produk"
                                                    data-id="<?php echo $row['no']; ?>"
                                                    data-nama="<?php echo htmlspecialchars($row['namaproduk']); ?>"
                                                    data-kode="<?php echo htmlspecialchars($row['kode']); ?>"
                                                    data-ket="<?php echo htmlspecialchars($row['ket']); ?>"
                                                    data-harga="<?php echo $row['harga']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1 btn-delete-produk"
                                                    data-id="<?php echo $row['no']; ?>"
                                                    data-nama="<?php echo htmlspecialchars($row['namaproduk']); ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
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
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_produk.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Produk</label>
                            <input type="text" name="namaproduk" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Kategori Barang</label>
                            <select name="kode" class="form-select rounded-3" required>
                                <option value="">-- Pilih Jenis Kategori --</option>
                                <?php
                                $opt_kat = mysqli_query($koneksi, "SELECT * FROM kategori");
                                while ($ok = mysqli_fetch_assoc($opt_kat)) {
                                    echo "<option value='" . $ok['kode'] . "'>" . $ok['kategori'] . " (" . $ok['kode'] . ")</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Keterangan</label>
                            <textarea name="ket" rows="3" class="form-control rounded-3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Harga Pokok (Rp)</label>
                            <input type="number" name="harga" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Foto Produk</label>
                            <div class="upload-dropzone border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative" id="dropzone-tambah" style="cursor: pointer;">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2 d-block"></i>
                                <span class="small text-muted d-block font-monospace">Drag & Drop gambar ke sini, klik untuk memilih, atau tekan <kbd>Ctrl + V</kbd> untuk paste</span>
                                <input type="file" name="image" id="file-tambah" class="form-control mt-2 position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="image/*" style="cursor: pointer;">
                            </div>
                            <div class="mt-2 text-center d-none" id="container-preview-tambah">
                                <img id="preview-tambah" src="#" class="img-thumbnail rounded-3" style="max-height: 150px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light p-3">
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
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-warning me-2"></i>Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_produk.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_produk" id="edit_id_produk">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Produk</label>
                            <input type="text" name="namaproduk" id="edit_nama_produk" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Kategori Barang</label>
                            <select name="kode" id="edit_kode_produk" class="form-select rounded-3" required>
                                <?php
                                mysqli_data_seek($opt_kat, 0);
                                while ($ok = mysqli_fetch_assoc($opt_kat)) {
                                    echo "<option value='" . $ok['kode'] . "'>" . $ok['kategori'] . " (" . $ok['kode'] . ")</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Keterangan</label>
                            <textarea name="ket" id="edit_ket_produk" rows="3" class="form-control rounded-3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Harga Pokok (Rp)</label>
                            <input type="number" name="harga" id="edit_harga_produk" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Ubah Foto</label>
                            <div class="upload-dropzone border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative" id="dropzone-edit" style="cursor: pointer;">
                                <i class="fas fa-edit fa-2x text-muted mb-2 d-block"></i>
                                <span class="small text-muted d-block font-monospace">Drag & Drop gambar baru ke sini, klik untuk memilih, atau tekan <kbd>Ctrl + V</kbd> untuk paste</span>
                                <input type="file" name="image" id="file-edit" class="form-control mt-2 position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="image/*" style="cursor: pointer;">
                            </div>
                            <div class="mt-2 text-center d-none" id="container-preview-edit">
                                <img id="preview-edit" src="#" class="img-thumbnail rounded-3" style="max-height: 150px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light p-3">
                        <button type="button" class="btn btn-light rounded-3 px-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="proses_edit_produk" class="btn btn-warning text-white rounded-3 px-4 fw-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDeleteProduk" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-body p-4 text-center">
                    <div class="text-danger mb-3"><i class="fas fa-exclamation-circle fa-3x"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Apakah Anda Yakin?</h5>
                    <p class="text-muted small mb-0">Produk <strong id="delete_nama_display" class="text-dark"></strong> akan dihapus permanen.</p>
                </div>
                <div class="modal-footer border-top-0 bg-light p-2 d-flex justify-content-stretch" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-light rounded-3 w-50 fw-semibold btn-sm" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="btn_konfirmasi_hapus" class="btn btn-danger rounded-3 w-50 fw-semibold btn-sm">Ya, Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalEditProduk = new bootstrap.Modal(document.getElementById('modalEditProduk'));
        document.querySelectorAll('.btn-edit-produk').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id_produk').value = this.getAttribute('data-id');
                document.getElementById('edit_nama_produk').value = this.getAttribute('data-nama');
                document.getElementById('edit_kode_produk').value = this.getAttribute('data-kode');
                document.getElementById('edit_ket_produk').value = this.getAttribute('data-ket');
                document.getElementById('edit_harga_produk').value = this.getAttribute('data-harga');
                modalEditProduk.show();
            });
        });

        const modalDeleteProduk = new bootstrap.Modal(document.getElementById('modalDeleteProduk'));
        document.querySelectorAll('.btn-delete-produk').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('delete_nama_display').innerText = this.getAttribute('data-nama');
                document.getElementById('btn_konfirmasi_hapus').setAttribute('href', 'admin_produk.php?hapus_produk=' + id);
                modalDeleteProduk.show();
            });
        });

        // Fungsi pembantu untuk memproses interaksi upload gambar
        function inisialisasiUploadModern(idModal, idDropzone, idInput, idPreview, idContainerPreview) {
            const modalEl = document.getElementById(idModal);
            const dropzone = document.getElementById(idDropzone);
            const fileInput = document.getElementById(idInput);
            const previewImg = document.getElementById(idPreview);
            const previewContainer = document.getElementById(idContainerPreview);

            // 1. Validasi & Tampilkan Pratinjau File Gambar
            function handleFile(file) {
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewContainer.classList.remove('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            }

            // Ambil file saat pengguna memilih via jendela file browser bawaan biasa
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleFile(this.files[0]);
                }
            });

            // 2. Efek Visual Drag & Drop (Seret dan Lepas)
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropzone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('dragover');
                }, false);
            });

            // Tangkap ile yang dijatuhkan di dropzone
            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    fileInput.files = files; // Salin file drop ke input asli form
                    handleFile(files[0]);
                }
            });

            // 3. Menangani Fitur Paste (Salin-Tempel dari Clipboard)
            // Fitur paste hanya aktif saat modal yang bersangkutan sedang terbuka/aktif
            window.addEventListefner('paste', (e) => {
                if (modalEl.classList.contains('show')) {
                    const items = (e.clipboardData || e.originalEvent.clipboardData).items;
                    for (let i = 0; i < items.length; i++) {
                        if (items[i].type.indexOf('image') !== -1) {
                            const blob = items[i].getAsFile();

                            // Buat penampung file objek tiruan agar terbaca oleh elemen form standar
                            const fileMaju = new File([blob], "pasted-image-" + Date.now() + ".png", {
                                type: blob.type
                            });

                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(fileMaju);

                            fileInput.files = dataTransfer.files; // Rekatkan file ke input form
                            handleFile(fileMaju);
                            break;
                        }
                    }
                }
            });

            // Reset pratinjau ketika modal ditutup agar kembali bersih
            modalEl.addEventListener('hidden.bs.modal', function() {
                fileInput.value = "";
                previewImg.src = "#";
                previewContainer.classList.add('d-none');
            });
        }

        // Jalankan fungsi untuk kedua modal (Tambah & Edit) setelah dokumen siap
        document.addEventListener("DOMContentLoaded", function() {
            inisialisasiUploadModern('modalTambahProduk', 'dropzone-tambah', 'file-tambah', 'preview-tambah', 'container-preview-tambah');
            inisialisasiUploadModern('modalEditProduk', 'dropzone-edit', 'file-edit', 'preview-edit', 'container-preview-edit');
        });
    </script>
</body>

</html>