<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kost_2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
