<?php
session_start();

// Jika user sudah login, langsung lempar ke halaman utama
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include 'config/koneksi.php';
/** @var mysqli $koneksi */

$error = '';
$sukses = '';

if (isset($_POST['proses_register'])) {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $konfirmasi_password = mysqli_real_escape_string($koneksi, $_POST['konfirmasi_password']);
    
    // Nilai default untuk status: 1 untuk member biasa (0 untuk admin)
    $status = 1; 

    // Validasi input kosong
    if (empty($username) || empty($password) || empty($konfirmasi_password)) {
        $error = "Semua form pendaftaran wajib diisi!";
    } 
    // Validasi kecocokan password
    elseif ($password !== $konfirmasi_password) {
        $error = "Konfirmasi password tidak sesuai!";
    } 
    else {
        // Cek apakah username sudah terdaftar di tabel login
        $cek_user = mysqli_query($koneksi, "SELECT * FROM login WHERE user = '$username'");
        
        if (mysqli_num_rows($cek_user) > 0) {
            $error = "Username '$username' sudah digunakan, cari nama lain!";
        } else {
            // MENYIMPAN TANPA HASH (Teks Asli)
            $query_register = "INSERT INTO login (user, password, status) VALUES ('$username', '$password', '$status')";
            $insert = mysqli_query($koneksi, $query_register);

            if ($insert) {
                $sukses = "Pendaftaran berhasil! Silakan login untuk mulai belanja.";
            } else {
                $error = "Gagal mendaftar, terjadi kesalahan sistem: " . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - ToKU</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 450px;
            margin-top: 5rem;
        }
        .card-register {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="register-container w-100">
        
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary"><i class="fas fa-shopping-bag me-2"></i>ToKU</h2>
            <p class="text-muted">Buat akun barumu untuk mulai menjelajahi produk terbaik</p>
        </div>

        <div class="card card-register p-4 bg-white">
            <h4 class="fw-bold text-dark text-center mb-4">Registrasi Akun</h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show small py-2" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($sukses)): ?>
                <div class="alert alert-success small py-2 text-center" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?php echo $sukses; ?><br>
                    <a href="login.php" class="btn btn-primary btn-sm mt-2 fw-bold w-100 rounded-pill">Ke Halaman Login</a>
                </div>
            <?php endif; ?>

            <?php if (empty($sukses)): ?>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label small fw-bold text-muted">Email baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-user"></i></span>
                            <input type="email" name="username" id="username" class="form-control form-control-sm" placeholder="Masukkan email" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-bold text-muted">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control form-control-sm" placeholder="Buat password" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="konfirmasi_password" class="form-label small fw-bold text-muted">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-shield-alt"></i></span>
                            <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control form-control-sm" placeholder="Ulangi password" required>
                        </div>
                    </div>

                    <button type="submit" name="proses_register" class="btn btn-primary w-100 rounded-pill fw-bold py-2 mb-3">
                        <i class="fas fa-user-plus me-1"></i> Daftar Sekarang
                    </button>
                </form>
            <?php endif; ?>

            <div class="text-center mt-2">
                <small class="text-muted">Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold text-primary">Login di sini</a></small>
            </div>
        </div>

    </div>
</div>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>