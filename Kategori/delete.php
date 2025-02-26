<?php
require_once 'koneksi.php';
include '../config/function.php';

// Untuk menerima id kategori yang dipilih pengguna
$id_kategori = $_GET['id_kategori'];

if (delete_kategori($id_kategori)){
    echo"<script>
            alert('Data Kategori Berhasil Dihapus');
            window.location.href = 'kategori.php';
        </script>";
}else{
    echo"<script>
            alert('Data Kategori Gagal Dihapus');
            window.location.href = 'kategori.php';
        </script>";
}
mysqli_close($conn);
?>
