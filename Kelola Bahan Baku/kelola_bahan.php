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
require_once 'koneksi.php';
include '../config/function.php';
$data_kategori = select("SELECT id_kategori, nama_kategori FROM t_kategori");
$data_tabel = select("SELECT * FROM t_bahan_baku");

$kuantitas = select("SELECT * FROM t_bahan_baku WHERE kuantitas <= 20");
if (!empty($kuantitas)){
  include "api.php";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Bahan Baku</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/kelola_bahan.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
</head>
<body>
  
    <!-- Navbar -->
    <nav class="navbar d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
          <button onclick="openSidebar()" class="me-4">☰</button>
          <div class="input-group me-2" style="max-width: 300px;">
              <div class="input-group-text">
                  <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
              </div>
              <input type="search" class="form-control" placeholder="Pencarian">
          </div>
      </div>
      <div class="d-flex align-items-center">
          <i class="fas fa-user-circle fa-2xl me-2" aria-label="User  Icon"></i>
          <span class="navbar-text text-black me-4"><?php if (isset($_SESSION['nama_pengguna'])) { echo $_SESSION['nama_pengguna']; 
              echo "<br>Staff Gudang";}?></span>
      </div>
    </nav>
    
     <!-- Sidebar -->
  <div id="sidebar" class="sidebar">
    <img src="../Polibatam.png">
    <button class="close-btn" onclick="closeSidebar()">x</button>
    
    <ul>
        <li><a href="../Dasboard/dasboard.php">Beranda</a></li>
        <li><a href="#><i class="fa-solid fa-table-cells-large"></i> Kelola Stok bahan Baku</a></li>
        <li><a href="../Bahan Baku Masuk/bahan_masuk.php"><i class="fa-solid fa-list-check"></i> Bahan Baku Masuk</a></li>
        <li><a href="../Bahan Baku Keluar/bahan_baku_keluar.php"><i class="fa-regular fa-clipboard"></i> Bahan Baku Keluar</a></li>
        <li><a href="../Kategori/kategori.php"><i class="fa-sharp fa-thin fa-chart-simple"></i> Kategori Stok Bahan Baku</a></li>
        <div class="exit">
        <li><a href="../Login/login.php"><i class="fa-solid fa-power-off"></i> Keluar</a></li>
        </div>
    </ul>
  </div>

    <!-- Content -->
    <div class="container-fluid">
    <div class="content py-4">
      <h2>Kelola Bahan Baku</h2>
      
      <!-- Filter bar -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group">
         <select name="kategori">
        <option value="" hidden>Kategori</option>
        <option value="">Pokok</option> 
        <option value="">Cemilan</option>
        <option value="">Minuman</option>
      </select>
        
          <button type="button" class="btn btn-light border text-danger">
            <i class="fa-solid fa-rotate-left"></i>    Ulangi
          </button>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">Tambah</button>
      </div>

      <!-- Modal -->
<div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <!-- Modal Content -->
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><strong>Tambah Stok Bahan Baku</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Modal Body -->
      <div class="modal-body">
        <form method="POST" action="tambah_barang.php">
          <div class="mb-3">
            <label for="id_kategori" class="form-label">ID Kategori</label>
            <div class="dropdown">
             <select class="form-control" id="id_kategori" name="id_kategori" placeholder="Masukan ID_Kategori" required>
                <option value="" hidden>Pilih Kategori</option>
                <optgroup label="Kategori"></optgroup>
              <?php foreach ($data_kategori as $k) :?>
                <option value="<?= $k['id_kategori']?>">
                  <?= $k['id_kategori'] ?> (<?= $k['nama_kategori']?>)
                </option>
             <?php endforeach;?>
              </select>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="nama_bahan_baku" class="form-label">Nama Bahan Baku</label>
            <input type="text" class="form-control" id="nama_bahan_baku" name="nama_bahan_baku" placeholder="Masukan Nama Bahan Baku" required>
          </div>

          <div class="mb-3">
            <label for="unit" class="form-label">Unit</label>
            <input type="text" class="form-control" id="unit" name="unit" placeholder="PCS" required>
          </div>
          <!-- Modal Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="tambah">Simpan</button>
          </div>
        </form>
        </div>
      </div>
    </div>
      </div>
      

    <!--Edit Modals-->
    <!-- Modal -->
     <?php foreach ($data_tabel as $tabel) : ?>
<div id="editModal<?= $tabel['id_bahan_baku'];?>" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <!-- Modal Content -->
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><strong>Ubah Stok Bahan Baku</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Modal Body -->
      <div class="modal-body">
        <form method="POST" action="update_barang.php">
        
        <div class="mb-3">
        <label for="id_kategori" class="form-label">ID Kategori</label>
        <div class="dropdown">
        <select class="form-control" id="id_kategori" name="id_kategori" placeholder="Masukan ID_Kategori">
            <option value="" hidden>Pilih Kategori</option>
            <optgroup label="Kategori"></optgroup>
            <?php foreach ($data_kategori as $k) : ?>
                <option value="<?= $k['id_kategori'] ?>" 
                    <?= $tabel['id_kategori'] == $k['id_kategori'] ? 'selected' : '' ?>>
                    <?= $k['id_kategori'] ?> (<?= $k['nama_kategori'] ?>)
                </option>
            <?php endforeach; ?>
            </select>
          </div>
      </div>

          <div class="mb-3">
            <label for="id_bahan_baku" class="form-label">ID Bahan Baku</label>
            <input type="text" class="form-control" id="id_bahan_baku" name="id_bahan_baku" placeholder="Masukan Nama Bahan Baku" value=<?= $tabel['id_bahan_baku']; ?> readonly>
          </div>
          
          <div class="mb-3">
            <label for="nama_bahan_baku" class="form-label">Nama Bahan Baku</label>
            <input type="text" class="form-control" id="nama_bahan_baku" name="nama_bahan_baku" value="<?= htmlspecialchars($tabel['nama_bahan_baku'], ENT_QUOTES, 'UTF-8'); ?>">
          </div>

          <div class="mb-3">
            <label for="unit" class="form-label">Unit</label>
            <input type="text" class="form-control" id="unit" name="unit" placeholder="PCS" value="<?= $tabel['unit'];?>">
          </div>
          <!-- Modal Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="ubah">Simpan</button>
          </div>
        </form>
        </div>
      </div>
    </div>
      </div>

      <!-- Modal hapus-->
      <div id="hapusModal<?= $tabel['id_bahan_baku']; ?>" class="modal fade" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel"><strong>Konfirmasi Hapus Data</strong></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                                    <h5 class="text-center"> Apakah anda yakin akan menghapus data ini?<br>
                                        <span class="text-primary font-monospace">ID: <?= $tabel['id_bahan_baku']?> - Nama: <?= $tabel['nama_bahan_baku']?></span>
                                    </h5>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <a href="hapus_barang.php?id_bahan_baku=<?= $tabel['id_bahan_baku'];?>" class="btn btn-danger">Hapus</a>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
      <?php endforeach; ?>

      <!-- Table -->
      <div class="table-responsive">
        <table id="data-table" class="table table-bordered table-striped"> <!--Tipe Border dari bootstrap-->
          <thead>
            <tr>
              <th>ID Stok </th>
              <th>Kode Barcode</th>
              <th>Nama Bahan Baku</th>
              <th>Kategori</th>
              <th>Kuantitas</th>
              <th>Unit</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($data_tabel as $tabel) : ?>  
            <tr>
            <td> <?= $tabel['id_bahan_baku'];?>  </td>
            <td> <?= $tabel['kode_barcode'];?> </td>
            <td> <?= $tabel['nama_bahan_baku'];?> </td>
            <td> <?= $tabel['id_kategori'];?> </td>
            <td> <?= $tabel['kuantitas'];?> </td>
            <td> <?= $tabel['unit'];?> </td>
              <td>
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $tabel['id_bahan_baku'];?>"><i class="fas fa-edit"></i></button> <!-- Icon edit-->
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal<?= $tabel['id_bahan_baku'];?>"><i class="fa-regular fa-trash-can" style="color: #dd1d1d;"></i></i></button> <!-- Icon Sampah/delete-->
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

  <!-- Font Awesome for icons -->
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.1.0.js"></script>
  <script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
  <!-- Bootstrap JS -->
  <!-- Bootstrap 5 -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

  <script src="/sidebar_datatable.js"></script>
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
</body>
</html>
