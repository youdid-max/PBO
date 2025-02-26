<?php
require_once 'koneksi.php';
include '../config/function.php';

if (isset($_POST["tambah"])) { 
    $result = create_bahan($_POST);
    
    if($result['success']){
        echo"<script>
                alert('{$result['message']}');
                window.location.href = 'kelola_bahan.php';
            </script>";
    }else{
        echo"<script>
                alert('{$result['message']}');
                window.location.href = 'kelola_bahan.php';
            </script>";
    }
mysqli_close($conn);
}

?>