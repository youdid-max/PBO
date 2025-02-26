<?php
session_start();
include 'koneksi.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nama_pengguna']) && isset($_POST['password'])) {
        $nama_pengguna = trim($_POST['nama_pengguna']);
        $password = trim($_POST['password']);

        $query = "SELECT * FROM pengguna WHERE nama_pengguna = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nama_pengguna);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($password == $row['password']) { 
                $_SESSION['nama_pengguna'] = $row['nama_pengguna'];
                $_SESSION['role'] = $row['role'];

                if ($row['role'] === 'Admin') {
                    echo "<script>
                            alert('Berhasil masuk sebagai Admin!');
                            window.location.href = '../Dasboard admin/dasboard.php';
                          </script>";
                } elseif ($row['role'] === 'Staff Gudang') {
                    echo "<script>
                            alert('Berhasil masuk sebagai Staff Gudang!');
                            window.location.href = '../Dasboard/dasboard.php';
                          </script>";
                }
                exit();
            } else {

                echo "<script>
                        alert('Password salah!');
                        window.location.href = 'login.php?error=wrong_password';
                      </script>";
                exit();
            }
            if ($user['role'] == 'Admin') {
                header("Location: ../Admin/dashboard.php");
                exit();
            } elseif ($user['role'] == 'staff') {
                header("Location: ../Staff/dashboard.php");
                exit();
            } else {
                header("Location: ../User/dashboard.php");
            exit();
            }

        } else {
            echo "<script>
                    alert('Nama pengguna tidak ditemukan!');
                    window.location.href = 'login.php?error=user_not_found';
                  </script>";
            exit();
        }
        
    } else {
        echo "<script>
                alert('Form tidak lengkap!');
                window.location.href = 'login.php';
              </script>";
        exit();
    }
}
?>
