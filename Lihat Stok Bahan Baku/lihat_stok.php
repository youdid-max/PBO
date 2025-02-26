<?php
session_start();

if (!isset($_SESSION['nama_pengguna'])) {
    header("Location: ../Login/login.php");
    exit();
}
if (!isset($_SESSION['role'])) {
  header("Location: ../Login/login.php?error=not_logged_in");
  exit();
}

$allowed_roles = ['Staff Gudang'];

if (!in_array($_SESSION['role'], $allowed_roles)) {
  echo "<script>
      alert('Anda tidak memiliki akses ke halaman ini.');
      window.location.href = '../Login/login.php';
  </script>";
  exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lihat Stok Bahan Baku</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../css/lihat_stok.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
          <button onclick="openSidebar()">☰</button>
      </div>
      <div class="d-flex align-items-center">
          <i class="fas fa-user-circle fa-2xl me-2" aria-label="User  Icon"></i>
          <span class="navbar-text text-black me-4"><?php if (isset($_SESSION['nama_pengguna'])) { echo $_SESSION['nama_pengguna']; 
              echo "<br>Staff Gudang";}?></span>
      </div>
    </nav>
    
      <!-- Sidebar -->
  <div id="sidebar" class="sidebar">
    <img src="Polibatam.png">
    <button class="close-btn" onclick="closeSidebar()">x</button>
    
    <ul>
        <li><a href="/Dasboard/tes.html">Beranda</a></li>
        <li><a href="/Kelola Bahan Baku/kelola_bahan.html"><i class="fa-solid fa-table-cells-large"></i> Kelola Stok bahan Baku</a></li>
        <li><a href="/Bahan Baku Masuk/tes.html"><i class="fa-solid fa-list-check"></i> Bahan Baku Masuk</a></li>
        <li><a href="/Bahan Baku Keluar/bahan_baku_keluar.html"><i class="fa-regular fa-clipboard"></i> Bahan Baku Keluar</a></li>
        <li><a href="/Kategori/kategori.html"><i class="fa-sharp fa-thin fa-chart-simple"></i> Kategori Stok Bahan Baku</a></li>
        <div class="exit">
        <li><a href="../Login/login.php"><i class="fa-solid fa-power-off"></i> Keluar</a></li>
        </div>
    </ul>
  </div>
    <div class="container-fluid">
    <!-- Content -->
    <div class="content py-4">
      <h2>Lihat Stok Bahan Baku</h2>

      <!-- Search bar and download icon -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="input-group" style="max-width: 300px;">
          <div class="input-group-prepend">
            <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
          </div>
          <input type="text" class="form-control" placeholder="Pencarian">
        </div>
        <button class="btn btn-light border-0"><i class="fas fa-download"></i></button>
      </div>
      
      <!-- Table -->
      <div class="table-responsive">
        <table id="data-table" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Kode Barcode</th>
              <th>Nama Bahan Baku</th>
              <th>Kategori</th>
              <th>Kuantitas</th>
              <th>Unit</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>4342665</td>
              <td>Beras</td>
              <td>Pokok</td>
              <td>80</td>
              <td>PCS</td>
            </tr>
            <tr>
              <td>5646335</td>
              <td>Roti</td>
              <td>Cemilan</td>
              <td>90</td>
              <td>PCS</td>
            </tr>
            <tr>
              <td>8999876</td>
              <td>Susu</td>
              <td>Minuman</td>
              <td>65</td>
              <td>PCS</td>
            </tr>
            <tr>
              <td>8999743</td>
              <td>Coklat</td>
              <td>Cemilan</td>
              <td>88</td>
              <td>PCS</td>
            </tr>
            <tr>
              <td>8999087</td>
              <td>Gula</td>
              <td>Pokok</td>
              <td>35</td>
              <td>PCS</td>
            </tr>
            <tr>
              <td>87779876</td>
              <td>Garam</td>
              <td>Pokok</td>
              <td>77</td>
              <td>PCS</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Font Awesome for icons -->
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script src="https://code.jquery.com/jquery-3.1.0.js"></script>
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
  <script>
  function openSidebar() {
    document.getElementById("sidebar").style.width = "250px"; // Buka sidebar
    document.querySelector(".content").style.marginLeft = "250px"; // Pindahkan konten utama
    document.querySelector(".navbar").style.marginLeft = "250px"; // Pindahkan navbar
  }
  
  function closeSidebar() {
    document.getElementById("sidebar").style.width = "0"; // Tutup sidebar
    document.querySelector(".content").style.marginLeft = "0"; // Reset margin konten utama
    document.querySelector(".navbar").style.marginLeft = "0"; // Reset margin navbar
  }
  </script>
  <script>
  $(document).ready( function () {
    $('#data-table').DataTable();
} );</script>
</body>
</html>
