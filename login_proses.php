<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['login'])) {
    $user     = mysqli_real_escape_string($koneksi, $_POST['user']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query  = "SELECT * FROM login WHERE user = '$user' AND password = '$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $data = mysqli_fetch_assoc($result);

        $_SESSION['login']  = true;
        $_SESSION['user']   = $data['user'];
        $_SESSION['status'] = $data['status'];

        if ($data['status'] == 0) {
            header("Location: admin/admin.php");
            exit;
        } else if ($data['status'] == 1) {
            header("Location: index.php");
            exit;
        }
    } else {
        echo "<script>
                alert('User atau Password salah!');
                window.location.href = 'login.php';
              </script>";
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>