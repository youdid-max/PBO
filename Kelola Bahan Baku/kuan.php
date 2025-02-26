<?php
include "koneksi.php";
$kuantitas = select("SELECT * FROM t_bahan_baku WHERE kuantitas <= 20");