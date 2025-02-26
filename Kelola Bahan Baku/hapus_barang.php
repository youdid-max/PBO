<?php
require_once 'koneksi.php';
include '../config/function.php';

// Untuk menerima id kategori yang dipilih pengguna
$id_bahan_baku = $_GET['id_bahan_baku'];

if (delete_bahan($id_bahan_baku)){
    echo"<script>
            alert('Data bahan baku berhasil dihapus');
            window.location.href = 'kelola_bahan.php';
        </script>";
}else{
    echo"<script>
            alert('Data bahan baku gagal dihapus');
            window.location.href = 'kelola_bahan.php';
        </script>";
}
mysqli_close($conn);
?>
