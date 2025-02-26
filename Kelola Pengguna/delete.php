<?php
require "koneksinya.php";
include '../config/function.php';

$email = $_GET['email'];

if (delete_pengguna($email)) {
    echo"<script>
            alert('Data Berhasil Dihapus');
            window.location.href = 'kelola_pengguna2.php';
        </script>";
}else{
    echo"<script>
            alert('Gagal menghapus pengguna. Email tidak ditemukan atau terjadi kesalahan');
            window.location.href = 'kelola_pengguna2.php';
        </script>";
}
mysqli_close($conn);
?>
