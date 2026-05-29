<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['status'] != 0) {
    header("Location: ../login.php");
    exit;
}
include '../config/koneksi.php';
/** @var mysqli $koneksi */
// 1. PROSES TAMBAH KATEGORI
if (isset($_POST['proses_tambah_kategori'])) {
    $kode     = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);

    $insert = mysqli_query($koneksi, "INSERT INTO kategori (kode, kategori) VALUES ('$kode', '$kategori')");
    if ($insert) {
        echo "<script>alert('Kategori baru berhasil ditambahkan!'); window.location='admin_kategori.php';</script>";
        exit;
    }
}

// 2. PROSES EDIT KATEGORI
if (isset($_POST['proses_edit_kategori'])) {
    $no       = mysqli_real_escape_string($koneksi, $_POST['no']);
    $kode     = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);

    $update = mysqli_query($koneksi, "UPDATE kategori SET kode='$kode', kategori='$kategori' WHERE no='$no'");
    if ($update) {
        echo "<script>alert('Kategori berhasil diperbarui!'); window.location='admin_kategori.php';</script>";
        exit;
    }
}

// 3. PROSES DELETE KATEGORI
if (isset($_GET['hapus_kategori'])) {
    $id_hapus = $_GET['hapus_kategori'];
    $query_hapus = mysqli_query($koneksi, "DELETE FROM kategori WHERE no = '$id_hapus'");
    if ($query_hapus) {
        echo "<script>alert('Kategori berhasil dihapus!'); window.location='admin_kategori.php';</script>";
        exit;
    }
}

$query_kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY no ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Panel Admin</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/admin_kategori.css">
</head>
<body>

    <div class="wrapper">
    <?php include '../includes/sidebar_admin.php'; ?>

        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded-4 shadow-sm border border-light">
                <div>
                    <h4 class="fw-bold text-dark mb-0">Manajemen Kategori</h4>
                    <p class="text-muted small mb-0">Atur pengelompokan jenis kode barang jualan.</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm rounded-3 fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Kategori
                </button>
            </div>

            <div class="card card-custom bg-white shadow-sm border border-light mb-2">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Kode Kategori</th>
                                    <th width="45%">Nama Kategori</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($query_kategori)) { ?>
                                    <tr>
                                        <td><strong>#<?php echo $row['no']; ?></strong></td>
                                        <td><span class="badge bg-secondary px-3 py-2 fs-6 fw-bold"><?php echo htmlspecialchars($row['kode']); ?></span></td>
                                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['kategori']); ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-warning px-2 py-1 btn-edit-kategori"
                                                        data-id="<?php echo $row['no']; ?>"
                                                        data-kode="<?php echo htmlspecialchars($row['kode']); ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['kategori']); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1 btn-delete-kategori" 
                                                        data-id="<?php echo $row['no']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['kategori']); ?>">
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

    <div class="modal fade" id="modalTambahKategori" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_kategori.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Kode Kategori</label>
                            <input type="text" name="kode" class="form-control rounded-3" placeholder="Contoh: k01, k02" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Kategori</label>
                            <input type="text" name="kategori" class="form-control rounded-3" placeholder="Contoh: elektronik, musik" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light p-3">
                        <button type="button" class="btn btn-light rounded-3 px-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="proses_tambah_kategori" class="btn btn-primary rounded-3 px-4 fw-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditKategori" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-warning me-2"></i>Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_kategori.php" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="no" id="edit_id_kategori">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Kode Kategori</label>
                            <input type="text" name="kode" id="edit_kode_kategori" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Kategori</label>
                            <input type="text" name="kategori" id="edit_nama_kategori" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light p-3">
                        <button type="button" class="btn btn-light rounded-3 px-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="proses_edit_kategori" class="btn btn-warning text-white rounded-3 px-4 fw-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDeleteKategori" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-body p-4 text-center">
                    <div class="text-danger mb-3"><i class="fas fa-exclamation-circle fa-3x"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Hapus Kategori?</h5>
                    <p class="text-muted small mb-0">Kategori <strong id="delete_nama_kategori" class="text-dark"></strong> akan dihapus permanen.</p>
                </div>
                <div class="modal-footer border-top-0 bg-light p-2 d-flex justify-content-stretch" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-light rounded-3 w-50 fw-semibold btn-sm" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="btn_konfirmasi_hapus_kategori" class="btn btn-danger rounded-3 w-50 fw-semibold btn-sm">Ya, Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalEdit = new bootstrap.Modal(document.getElementById('modalEditKategori'));
        document.querySelectorAll('.btn-edit-kategori').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id_kategori').value = this.getAttribute('data-id');
                document.getElementById('edit_kode_kategori').value = this.getAttribute('data-kode');
                document.getElementById('edit_nama_kategori').value = this.getAttribute('data-nama');
                modalEdit.show();
            });
        });

        const modalDelete = new bootstrap.Modal(document.getElementById('modalDeleteKategori'));
        document.querySelectorAll('.btn-delete-kategori').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('delete_nama_kategori').innerText = this.getAttribute('data-nama');
                document.getElementById('btn_konfirmasi_hapus_kategori').setAttribute('href', 'admin_kategori.php?hapus_kategori=' + id);
                modalDelete.show();
            });
        });
    </script>
</body>
</html>