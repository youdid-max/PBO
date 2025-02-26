<?php
require_once 'koneksi.php';
include '../config/function.php';

if (isset($_POST['tambah'])) {
    $result = create_kategori($_POST);

    // Tutup koneksi database
    mysqli_close($conn);

    // Redirect ke kategori.php dengan parameter hasil
    if ($result['success']) {
        header("Location: kategori.php?status=success&message=" . urlencode($result['message']));
    } else {
        header("Location: kategori.php?status=error&message=" . urlencode($result['message']));
    }
    exit;
}
?>
