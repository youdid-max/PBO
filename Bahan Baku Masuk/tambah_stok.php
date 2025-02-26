<?php
require_once 'koneksi.php';
include '../config/function.php';

if (isset($_POST["tambah"])) { 
    $result = tambah_stok($_POST);
    
    if($result['success']){
        echo"<script>
                alert('{$result['message']}');
                window.location.href = 'bahan_masuk.php';
            </script>";
    }else{
        echo"<script>
                alert('{$result['message']}');
                window.location.href = 'bahan_masuk.php';
            </script>";
    }
mysqli_close($conn);
}
?>