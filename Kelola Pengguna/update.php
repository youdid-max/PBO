<?php 
require("koneksinya.php");
include '../config/function.php';
if (isset($_POST['ubah'])){
    $result = update_pengguna($_POST);
    
    if ($result['success']) {
        echo "<script>
                alert('{$result['message']}');
                window.location.href = 'kelola_pengguna2.php';
              </script>";
    } else {
        echo "<script>
                alert('{$result['message']}');
                window.location.href = 'kelola_pengguna2.php';
              </script>";
    }
    mysqli_close($conn);
}
?>
