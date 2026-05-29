<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';

// Ambil ID Produk dan Qty dari Form
$id_produk = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if (!empty($id_produk)) {
    // Jika keranjang belum didefinisikan, buat array kosong
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Jika produk sudah ada di keranjang, akumulasikan jumlahnya (qty)
    if (isset($_SESSION['cart'][$id_produk])) {
        $_SESSION['cart'][$id_produk] += $qty;
    } else {
        // Jika belum ada, masukkan produk baru ke array keranjang
        $_SESSION['cart'][$id_produk] = $qty;
    }

    echo "<script>
            alert('Produk berhasil dimasukkan ke keranjang belanja!');
            window.location.href = 'index.php';
          </script>";
    exit;
} else {
    header("Location: index.php");
    exit;
}
