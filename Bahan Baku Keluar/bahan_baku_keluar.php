<?php
session_start();

if (!isset($_SESSION['nama_pengguna'])) {
    header("Location: ../Login/login.php");
    exit();
}

require_once 'koneksi.php';
include '../config/function.php';
$dropdown = select("SELECT nama_bahan_baku FROM t_bahan_baku " ); 
$data_tabel = select("SELECT * FROM t_bahan_masuk_keluar WHERE id_stok_keluar IS NOT NULL" );

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bahan Baku Masuk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/bahan_masuk.css">
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
        <li><a href="../Kelola Bahan Baku/kelola_bahan.php"><i class="fa-solid fa-table-cells-large"></i> Kelola Stok bahan Baku</a></li>
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
      <h2>Bahan Baku Keluar</h2>
      
      <!-- Filter bar --> 
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group">
          <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              Kategori
            </button>
            <ul class="dropdown-menu dropdown-menu-light">
              <li><a class="dropdown-item active" href="#">Cemilan</a></li>
              <li><a class="dropdown-item" href="#">Minuman</a></li>
              <li><a class="dropdown-item" href="#">Makanan Pokok</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#">Separated link</a></li>
            </ul>
          </div>
          </button>
          <button type="button" class="btn btn-light border">Tanggal</button>
          <button type="button" class="btn btn-light border text-danger">
            <i class="fa-solid fa-rotate-left"></i>Ulangi
          </button>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">Tambah</button>
      </div>

<!-- tambah Modal -->
<div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <!-- Modal Content -->
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><strong>Tambah Bahan Baku Keluar</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Modal Body -->
      <div class="modal-body">
        <form method="POST" action="stok_keluar.php">
          <div class="mb-3">
            <label for="bahanBaku" class="form-label">Nama Bahan Baku</label>
            <div class="dropdown">
              <select class="form-control" name="nama_bahan_baku">
                <option value="" hidden>Pilih Bahan Baku</option>
                 
                 <optgroup label="Bahan Baku">
                  <?php foreach ($dropdown as $d) : ?>
                 <option value="<?= $d['nama_bahan_baku'];?>">
                  <?= $d['nama_bahan_baku'];?>
                 </option>
                 <?php endforeach;?>
                </optgroup>
        
                  </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="kuantitas" class="form-label">Kuantitas</label>
            <input type="number" class="form-control" id="kuantitas" name="kuantitas" placeholder="Masukkan Kuantitas" min="0" required>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="tambah">Simpan</button>
          </div>
        </form>
        </div>
      </div>
    </div>
      </div>

<?php foreach ($data_tabel as $tabel):?>
<!--edit modal-->
 <!-- Modal -->
 <div id="editModal<?= $tabel['id_stok_keluar'];?>" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <!-- Modal Content -->
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><strong>Perbarui Bahan Baku Masuk</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Modal Body -->
      <div class="modal-body">
        <form method="POST" action="update_stok_keluar.php">
          
        <input type="hidden" id="id_transaksi" name="id_transaksi" value="<?= $tabel['id_transaksi'];?>" >

          <div class="mb-3">
            <label for="nama_bahan_baku" class="form-label">Nama Bahan Baku</label>
            <input type="text" class="form-control" id="nama_bahan_baku" name="nama_bahan_baku" value="<?= $tabel['nama_bahan_baku']; ?>" readonly>
          </div>

          <div class="mb-3">
            <label for="kuantitas" class="form-label">Kuantitas</label>
            <input type="number" class="form-control" id="kuantitas" name="kuantitas" value="<?= $tabel['kuantitas']; ?>" min="0">
          </div>
          
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
<div id="hapusModal<?= $tabel['id_transaksi']; ?>" class="modal fade" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel"><strong>Konfirmasi Hapus Data</strong></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                                    <h5 class="text-center"> Apakah anda yakin akan menghapus data transaksi ini?<br>
                                        <span class="text-primary font-monospace">ID: <?= $tabel['id_stok_masuk']?> - Nama: <?= $tabel['nama_bahan_baku']?></span>
                                    </h5>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <a href="hapus_transaksi.php?id_transaksi=<?= $tabel['id_transaksi'];?>" class="btn btn-danger">Hapus</a>
                                </div>
                        </div>
                    </div>
                </div>
            </div></tr>
<?php endforeach;?>

      <!-- Table -->
      <div class="table-responsive">
        <table id="data-table" class="table table-bordered table-striped"> <!--Tipe Border dari bootstrap-->
          <thead>
            <tr>
              <th>ID Stok Keluar</th>
              <th>Kode Barcode</th>
              <th>Nama Bahan Baku</th>
              <th>Kategori</th>
              <th>Kuantitas</th>
              <th>Tanggal Masuk</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data_tabel as $tabel):?>
            <tr>
              <td><?= $tabel['id_stok_keluar'];?></td>
              <td><?= $tabel['kode_barcode'];?></td>
              <td><?= $tabel['nama_bahan_baku'];?></td>
              <td><?= $tabel['id_kategori'];?></td>
              <td><?= $tabel['kuantitas'];?></td>
              <td><?= date('d-m-Y', strtotime($tabel['tanggal']));?></td>
              <td>
                <button class="btn btn-light btn-sm"  data-bs-toggle="modal" data-bs-target="#editModal<?= $tabel['id_stok_keluar'];?>"><i class="fas fa-edit"></i></button> <!-- Icon edit-->
                <button class="btn btn-light btn-sm"><i class="fa-regular fa-trash-can" style="color: #dd1d1d;" data-bs-toggle="modal" data-bs-target="#hapusModal<?= $tabel['id_transaksi']; ?>"></i></button> <!-- Icon Sampah/delete-->
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
