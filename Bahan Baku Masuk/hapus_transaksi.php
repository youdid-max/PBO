<?php
require_once 'koneksi.php';
include '../config/function.php';

// Untuk menerima id transaksi yang dipilih pengguna
$id_bahan_baku = $_GET['id_transaksi'];

if (delete_transaksi($id_bahan_baku)){
    echo"<script>
            alert('Data transaksi berhasil dihapus');
            window.location.href = 'bahan_masuk.php';
        </script>";
}else{
    echo"<script>
            alert('Data transaksi gagal dihapus');
            window.location.href = 'bahan_masuk.php';
        </script>";
}
mysqli_close($conn);
?>
