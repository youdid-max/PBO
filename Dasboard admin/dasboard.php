<?php

session_start();

if (isset($_SESSION['login_success'])) {
  echo "<script>alert('Login berhasil!');</script>";
  unset($_SESSION['login_success']); 
}

if (!isset($_SESSION['role'])) {
  header("Location: ../Login/login.php?error=not_logged_in");
  exit();
}

$allowed_roles = ['Admin'];

if (!in_array($_SESSION['role'], $allowed_roles)) {
  echo "<script>
      alert('Anda tidak memiliki akses ke halaman ini.');
      window.location.href = '../Login/login.php';
  </script>";
  exit();
}

require_once 'koneksi.php';
include '../config/function.php';
$total_stok = select("SELECT SUM(kuantitas) AS stok FROM t_bahan_baku");
$stok_masuk = select("SELECT SUM(kuantitas) AS masuk FROM t_bahan_masuk_keluar WHERE id_stok_masuk IS NOT NULL 
                AND MONTH(tanggal) = MONTH(NOW())
                AND YEAR(tanggal) = YEAR(NOW())");
$stok_keluar = select("SELECT SUM(kuantitas) AS keluar FROM t_bahan_masuk_keluar WHERE id_stok_keluar IS NOT NULL 
                AND MONTH(tanggal) = MONTH(NOW())
                AND YEAR(tanggal) = YEAR(NOW())");

$chart = select("SELECT nama_bahan_baku, 
          MONTH(tanggal) AS bulan,
          YEAR(tanggal) AS tahun,
          SUM(CASE WHEN id_stok_masuk IS NOT NULL THEN kuantitas ELSE 0 END) AS stok_masuk,
          SUM(CASE WHEN id_stok_keluar IS NOT NULL THEN kuantitas ELSE 0 END) AS stok_keluar
          FROM t_bahan_masuk_keluar
          WHERE MONTH(tanggal) = MONTH(CURRENT_DATE()) 
          AND YEAR(tanggal) = YEAR(CURRENT_DATE())
          GROUP BY nama_bahan_baku, MONTH(tanggal), YEAR(tanggal)
          ORDER BY nama_bahan_baku;");
?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dasboard_admin.css">

  </head>
  <body>
    
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
            <i class="fas fa-user-circle fa-2xl me-2" aria-label="User Icon"></i>
            <span class="navbar-text text-black me-4"><?php if (isset($_SESSION['nama_pengguna'])) { echo $_SESSION['nama_pengguna']; 
              echo "<br>Admin";}?></span>
        </div>
      </nav>
    
  <div id="sidebar" class="sidebar">
    <img src="../pic/Polibatam.png">
    <button class="close-btn" onclick="closeSidebar()">x</button>
    
    <ul>
        <li><a href="#">Beranda</a></li>
        <li><a href="../Lihat Stok Bahan Baku/lihat_stok_admin.php"><i class="fa-solid fa-list-check"></i> Lihat Stok Bahan Baku</a></li>
        <li><a href="../Kelola Pengguna/kelola_pengguna2.php"><i class="fa-solid fa-user-plus"></i> Kelola Pengguna</a></li>
        <div class="exit">
        <li><a href="../Login/logout.php"><i class="fa-solid fa-power-off"></i> Keluar</a></li>
        </div>
    </ul>
  </div>

      <!-- Content -->
      <div class="main-content">
        <h2>Beranda</h2>
        <br>
        
        <!-- Cards Row -->
        <div class="row">
          <div class="col-md-4">
            <div class="card text-center">
              <div class="card-body" style="text-align: left;">
                <h5>Total Stok Bahan Baku</h5>
                <?php foreach($total_stok as $ts) : ?>
                <h2><?= $ts['stok']; ?></h2>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card text-center">
              <div class="card-body" style="text-align: left;">
                <h5>Total Stok Bahan Baku Masuk</h5>
                <?php foreach($stok_masuk as $sm) : ?>
                <h2><?= $sm['masuk'];?></h2>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card text-center">
              <div class="card-body"  style="text-align: left;">
                <h5>Total Stok Bahan Baku Keluar</h5>
                <?php foreach($stok_keluar as $sk):?>
                <h2><?= $sk['keluar'];?></h2>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Bar Chart -->
        <div class="chart mt-4">
          <canvas id="myChart"></canvas>
        </div>
      </div>
    </div>

    <script>
      const data_bahan = <?php echo json_encode($chart); ?>;
      </script>

      <!-- Chart.js version 3.x -->
      <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
      <!-- chartjs-plugin-datalabels version 2.0.0 -->
      <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
      <script src="dasboard.js"></script>
</body>
</html>
