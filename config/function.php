<?php
// Fungsi menampilkan ke tabel
function select ($query)
{
    global $conn;
    
    $result = mysqli_query($conn, $query);
    $rows = [];

    while ($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }

    return $rows;
}

// FUNGSI KATEGORI

// Fungsi menambahkan data kategori
function create_kategori ($POST)
{
    global $conn;

    $nama_kategori = mysqli_real_escape_string($conn, $POST['nama_kategori']);

    // ID kategori baru
    $query_id = "SELECT MAX(CAST(SUBSTRING(id_kategori, 3) AS UNSIGNED)) AS max_id FROM t_kategori";
    $stmt_id = $conn->prepare($query_id);
    $stmt_id->execute();
    $result_id = $stmt_id->get_result();
    $row = $result_id->fetch_assoc();

    $new_id_number = $row['max_id'] ? $row['max_id'] + 1 : 1;
    $id_kategori = 'K' . str_pad($new_id_number, 3, '0', STR_PAD_LEFT);

    // Query tambah data
    $query = "INSERT INTO t_kategori (id_kategori, nama_kategori) VALUES (?, ?)";
    $stmt = $conn->prepare($query);

    $stmt->bind_param('ss', $id_kategori,$nama_kategori);
    if($stmt -> execute()){
        $affectedRows = $stmt->affected_rows;
        $stmt -> close();

        if ($affectedRows > 0){
            return[
                'success' => true,
                'message' => 'Kategori Berhasil Ditambahkan',
            ];
        }else{
            return[
                'success' => false,
                'message' => 'Tidak ada data yang Ditambahkan',
            ];
        }
    }else{
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Gagal menambahkan Kategori: ' . $stmt->error
        ];
    }
}

//Fungsi menghapus kategori
function delete_kategori($id_kategori) {
    global $conn;

    // Memulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // id_kategori menjadi NULL
        $query1 = "UPDATE t_bahan_baku SET id_kategori = NULL WHERE id_kategori = ?";
        $stmt_update = $conn->prepare($query1);
        $stmt_update->bind_param("s", $id_kategori);

        if (!$stmt_update->execute()) {
            throw new Exception("Gagal menghapus kategori di tabel bahan baku: " . $stmt_update->error);
        }
        $stmt_update->close();

        // Hapus kategori dari tabel kategori
        $query2 = "DELETE FROM t_kategori WHERE id_kategori = ?";
        $stmt_delete = $conn->prepare($query2);
        $stmt_delete->bind_param("s", $id_kategori);

        if (!$stmt_delete->execute()) {
            throw new Exception("Gagal menghapus kategori: " . $stmt_delete->error);
        }
        $stmt_delete->close();

        mysqli_commit($conn);

        return true;

    } catch (Exception $e) {

        mysqli_rollback($conn);

        error_log($e->getMessage());
        return false;
    }
}


//Fungsi mengubah data kategori
function update_kategori ($POST)
{
    global $conn;

    $id_kategori = mysqli_real_escape_string($conn, $POST['id_kategori']);
    $nama_kategori = mysqli_real_escape_string($conn, $POST['nama_kategori']);

    $query = "UPDATE t_kategori SET nama_kategori = ? WHERE id_kategori = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt){
        return['success' => false, 
                'message' => 'Error pada prepare statement ' . $conn->error
            ];
    }
    $stmt->bind_param('ss', $nama_kategori,$id_kategori);
    if($stmt -> execute()){
       $affectedRows = $stmt->affected_rows;
       $stmt -> close();

       if ($affectedRows > 0) {
           return [
               'success' => true,
               'message' => 'Kategori berhasil diubah'
           ];
       } else {
           return [
               'success' => false,
               'message' => 'Tidak ada data yang diubah'
           ];
       }
   } else {
       $stmt->close();
       return [
           'success' => false,
           'message' => 'Gagal mengubah data kategori: ' . $stmt->error
       ];
   }
}

//FUNGSI KELOLA PENGGUNA
//Menambahkan data pengguna
function create_pengguna ($POST)
{
    global $conn;

    $email = mysqli_real_escape_string($conn, $POST['email']);
    $nama_pengguna = mysqli_real_escape_string($conn, $POST['nama_pengguna']);
    $password = mysqli_real_escape_string($conn, $POST['password']);
    $no_WA = mysqli_real_escape_string($conn, $POST['no_WA']);
    $role= mysqli_real_escape_string($conn, $POST['role']);

    if($role === 'Admin' && empty($no_WA)){
        return[
            'success' => false,
            'message' => 'Nomor Whatsapp wajib diisi untuk Admin!'
        ];
    } 
    //Query tambah data pengguna
    $query ="INSERT INTO pengguna(email,nama_pengguna,password,no_WA,role) VALUES (?,?,?,?,?)";
    $stmt =  $conn->prepare($query);

    if (!$stmt){
        return[
            'success' => false,
            'message' => 'Error pada prepare statement ' . $conn->error
        ];
    }
    $stmt->bind_param('sssss', $email,$nama_pengguna,$password,$no_WA,$role);
     if($stmt -> execute()){
        $affectedRows = $stmt->affected_rows;
        $stmt -> close();

        if ($affectedRows > 0) {
            return [
                'success' => true,
                'message' => 'Pengguna berhasil ditambahkan'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Tidak ada data yang ditambahkan'
            ];
        }
    } else {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Gagal menambahkan pengguna: ' . $stmt->error
        ];
    }
}
     
   // Menghapus pengguna
function delete_pengguna ($email)
{
    global $conn;

    $query = "DELETE FROM pengguna WHERE email = ?";
    $stmt = $conn->prepare($query);

    // Cek prepare
    if ($stmt === false) {
        error_log("Error pada prepare statement: " . $conn->error);
        return false;
    }

    $stmt->bind_param("s", $email);

    // Eksekusi pernyataan
    if ($stmt->execute()) {
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows > 0;
    }else{
        error_log("Error pada execute statement: " . $stmt->error);
        return false;
    }
}

// Mengubah data pengguna
function update_pengguna ($POST)
{
    global $conn;

    $email = mysqli_real_escape_string($conn, $POST['email']);
    $nama_pengguna = mysqli_real_escape_string($conn, $POST['nama_pengguna']);
    $password = mysqli_real_escape_string($conn, $POST['password']);
    $no_WA = mysqli_real_escape_string($conn, $POST['no_WA']);
    $role= mysqli_real_escape_string($conn, $POST['role']);

    $query = "UPDATE pengguna SET nama_pengguna = ?, password = ?, no_WA = ?, role = ? WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt){
        return['success' => false,
                'message' => 'Error pada prepare statement ' . $conn->error
            ];
    }
    $stmt->bind_param('sssss', $nama_pengguna,$password,$no_WA,$role,$email);
     if($stmt -> execute()){
        $affectedRows = $stmt->affected_rows;
        $stmt -> close();

        if ($affectedRows > 0) {
            return [
                'success' => true,
                'message' => 'Data pengguna berhasil diubah'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Tidak ada data yang diubah'
            ];
        }
    } else {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Gagal mengubah pengguna: ' . $stmt->error
        ];
    }
}

function generateBarcode($id_bahan_baku) {
    // Contoh sederhana: tambahkan prefiks 'BB-' di depan ID Bahan Baku
    return 'BB-' . str_pad($id_bahan_baku, 6, STR_PAD_LEFT); 
}

//Menambahkan bahan baku
function create_bahan($POST) {
    global $conn;

    // ID stok keluar baru
    $query_id = "SELECT MAX(CAST(SUBSTRING(id_bahan_baku, 3) AS UNSIGNED)) AS max_id FROM t_bahan_baku";
    $stmt_id = $conn->prepare($query_id);
    $stmt_id->execute();
    $result_id = $stmt_id->get_result();
    $row = $result_id->fetch_assoc();

    $new_id_number = $row['max_id'] ? $row['max_id'] + 1 : 1;
    $id_bahan_baku = 'S' . str_pad($new_id_number, 3, '0', STR_PAD_LEFT);
    
    $kode_barcode = generateBarcode($id_bahan_baku);
    $nama_bahan_baku = $_POST['nama_bahan_baku'];
    $unit = $_POST['unit'];
    $id_kategori = $_POST['id_kategori'];

    // Insert ke tabel t_bahan_baku
        $query = "INSERT INTO t_bahan_baku (id_kategori,kode_barcode, id_bahan_baku, nama_bahan_baku, unit)
                   VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($query);

        if (!$stmt){
            return[
                'success' => false,
                'message' => 'Error pada prepare statement ' . $conn->error
            ];
        }
        $stmt->bind_param('sssss', $id_kategori,$kode_barcode,$id_bahan_baku,$nama_bahan_baku,$unit);
         if($stmt -> execute()){
            $affectedRows = $stmt->affected_rows;
            $stmt -> close();
    
            if ($affectedRows > 0) {
                return [
                    'success' => true,
                    'message' => 'Bahan baku berhasil ditambahkan'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Tidak ada data yang ditambahkan'
                ];
            }
        } else {
            $stmt->close();
            return [
                'success' => false,
                'message' => 'Gagal menambahkan bahan baku: ' . $stmt->error
            ];
        }
} 

// Mengubah bahan baku
function update_bahan($POST) {
    global $conn;

    $id_bahan_baku = $_POST['id_bahan_baku'];
    $nama_bahan_baku = $_POST['nama_bahan_baku'];
    $unit = $_POST['unit'];
    $id_kategori = $_POST['id_kategori'];
    
    $query = "UPDATE t_bahan_baku SET id_kategori = ?, nama_bahan_baku = ?, unit = ? WHERE id_bahan_baku = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt){
        return['success' => false,
                'message' => 'Error pada prepare statement ' . $conn->error
            ];
    }
    $stmt->bind_param('ssss', $id_kategori,$nama_bahan_baku,$unit,$id_bahan_baku);
     if($stmt -> execute()){
        $affectedRows = $stmt->affected_rows;
        $stmt -> close();

        if ($affectedRows > 0) {
            return [
                'success' => true,
                'message' => 'Data barang berhasil diubah'
            ];
        } else {
            return [
                'success' => true,
                'message' => 'Tidak ada data yang diubah'
            ];
        }
    } else {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Gagal mengubah data barang: ' . $stmt->error
        ];
    }
}

//Menghapus bahan baku
function delete_bahan ($id_bahan_baku)
{
    global $conn;

    $query = "DELETE FROM t_bahan_baku WHERE id_bahan_baku = ?";
    $stmt = $conn->prepare($query);

    // Cek prepare
    if ($stmt === false) {
        error_log("Error pada prepare statement: " . $conn->error);
        return false;
    }

    $stmt->bind_param("s", $id_bahan_baku);

    if ($stmt->execute()) {
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows > 0;
    }else{
        error_log("Error pada execute statement: " . $stmt->error);
        return false;
    }
}

// Tambah Stok
function tambah_stok($POST)
{
    global $conn;


    $kuantitas = $POST['kuantitas'];
    $nama_bahan_baku = $POST['nama_bahan_baku'];

    if(!is_numeric($kuantitas) || $kuantitas <= 0){
        return[
            'success' => false,
            'message' => 'Kuantitas tidak boleh kurang atau sama dengan 0'
        ];
    }

    mysqli_begin_transaction($conn);
    
    try{
    $query1 = "SELECT id_bahan_baku, kode_barcode, id_kategori FROM t_bahan_baku WHERE nama_bahan_baku = ?";
    $stmt = $conn->prepare($query1);
    $stmt->bind_param('s',$nama_bahan_baku);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if(!$data){
        throw new Exception('ID Bahan Baku tidak ditemukan');
    }

    $kode_barcode = $data['kode_barcode'];
    $id_kategori = $data['id_kategori'];
    $id_bahan_baku = $data['id_bahan_baku'];

    // ID stok masuk baru
    $query_id = "SELECT MAX(CAST(SUBSTRING(id_stok_masuk, 3) AS UNSIGNED)) AS max_id FROM t_bahan_masuk_keluar";
    $stmt_id = $conn->prepare($query_id);
    $stmt_id->execute();
    $result_id = $stmt_id->get_result();
    $row = $result_id->fetch_assoc();

    $new_id_number = $row['max_id'] ? $row['max_id'] + 1 : 1;
    $id_stok_masuk = 'M' . str_pad($new_id_number, 4, '0', STR_PAD_LEFT);

    $insert_query = "INSERT INTO t_bahan_masuk_keluar (id_bahan_baku,nama_bahan_baku,id_stok_masuk,kode_barcode,kuantitas,id_kategori) VALUES(?,?,?,?,?,?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt ->bind_param('ssssis',$id_bahan_baku,$nama_bahan_baku,$id_stok_masuk,$kode_barcode,$kuantitas,$id_kategori);
    if (!$insert_stmt->execute()){
        throw new Exception('Gagal menambahkan transaksi: ' . $insert_stmt->error);
    }

    // Update kuantitas bahan baku
    $update_query = "UPDATE t_bahan_baku SET kuantitas = kuantitas + ? WHERE id_bahan_baku = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('is', $kuantitas, $id_bahan_baku);

    if (!$update_stmt->execute()){
        throw new Exception('Gagal mengupdate kuantitas bahan baku: ' . $update_stmt->error);
    }

    mysqli_commit($conn);

    return[
        'success' => true,
        'message' => 'Transaksi berhasil ditambahkan dan stok diperbarui'
    ];
 } catch (Exception $e){
    mysqli_rollback($conn);

    return[
        'success' => false,
        'message' =>$e->getMessage()
    ];
 }
}

//Update tambah stok
function update_transaksi($POST)
{
    global $conn;

    $id_transaksi = $POST['id_transaksi'];
    $kuantitas_baru = $POST['kuantitas']; 

    if(!is_numeric($kuantitas_baru) || $kuantitas_baru < 0){
        return[
            'success' => false,
            'message' => 'Kuantitas tidak boleh kurang dari 0'
        ];
    }

    mysqli_begin_transaction($conn);

    try {
        // Ambil data transaksi lama berdasarkan ID transaksi
        $query_transaksi = "SELECT id_bahan_baku, kuantitas FROM t_bahan_masuk_keluar WHERE id_transaksi = ?";
        $stmt_transaksi = $conn->prepare($query_transaksi);
        $stmt_transaksi->bind_param('i', $id_transaksi);
        $stmt_transaksi->execute();
        $result_transaksi = $stmt_transaksi->get_result();
        $data_transaksi = $result_transaksi->fetch_assoc();

        if (!$data_transaksi) {
            throw new Exception("Data transaksi tidak ditemukan.");
        }

        $id_bahan_baku = $data_transaksi['id_bahan_baku'];
        $kuantitas_lama = $data_transaksi['kuantitas'];

        // Ambil stok bahan baku saat ini
        $query_stok = "SELECT kuantitas FROM t_bahan_baku WHERE id_bahan_baku = ?";
        $stmt_stok = $conn->prepare($query_stok);
        $stmt_stok->bind_param('s', $id_bahan_baku);
        $stmt_stok->execute();
        $result_stok = $stmt_stok->get_result();
        $data_stok = $result_stok->fetch_assoc();

        if (!$data_stok) {
            throw new Exception("Data bahan baku tidak ditemukan.");
        }

        $stok_sekarang = $data_stok['kuantitas'];

        // Hitung selisih kuantitas dan update stok
        if ($kuantitas_baru >= $kuantitas_lama) {
            $selisih = $kuantitas_baru - $kuantitas_lama;
            $stok_baru = $stok_sekarang + $selisih;
        } else {
            $selisih = $kuantitas_lama - $kuantitas_baru;
            $stok_baru = $stok_sekarang - $selisih;
        }

        // Update stok bahan baku
        $update_stok = "UPDATE t_bahan_baku SET kuantitas = ? WHERE id_bahan_baku = ?";
        $stmt_update_stok = $conn->prepare($update_stok);
        $stmt_update_stok->bind_param('is', $stok_baru, $id_bahan_baku);
        if (!$stmt_update_stok->execute()) {
            throw new Exception("Gagal memperbarui stok bahan baku.");
        }

        // Update data di tabel bahan masuk keluar
        $update_transaksi = "UPDATE t_bahan_masuk_keluar 
                             SET kuantitas = ?
                             WHERE id_transaksi = ?";
        $stmt_update_transaksi = $conn->prepare($update_transaksi);
        $stmt_update_transaksi->bind_param('is', $kuantitas_baru, $id_transaksi);
        if (!$stmt_update_transaksi->execute()) {
            throw new Exception("Gagal memperbarui data transaksi.");
        }

        mysqli_commit($conn);

        return [
            'success' => true,
            'message' => 'Transaksi berhasil diperbarui dan stok berhasil diperbarui.'
        ];
    } catch (Exception $e) {
        mysqli_rollback($conn);

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

//Mengurangi Stok

function keluar_stok($POST)
{
    global $conn;

    $kuantitas = $POST['kuantitas'];
    $nama_bahan_baku = $POST['nama_bahan_baku'];

    if(!is_numeric($kuantitas) || $kuantitas <= 0){
        return[
            'success' => false,
            'message' => 'Kuantitas tidak boleh kurang atau sama dengan 0'
        ];
    }

    mysqli_begin_transaction($conn);
    
    try{
    $query1 = "SELECT id_bahan_baku, kode_barcode, id_kategori, kuantitas AS stok FROM t_bahan_baku WHERE nama_bahan_baku = ?";
    $stmt = $conn->prepare($query1);
    $stmt->bind_param('s',$nama_bahan_baku);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if(!$data){
        throw new Exception('ID Bahan Baku tidak ditemukan');
    }

    $kode_barcode = $data['kode_barcode'];
    $id_kategori = $data['id_kategori'];
    $id_bahan_baku = $data['id_bahan_baku'];
    $stok = $data['stok'];

    if ($stok < $kuantitas) {
        throw new Exception('Stok tidak cukup');
    }

        // ID stok keluar baru
    $query_id = "SELECT MAX(CAST(SUBSTRING(id_stok_keluar, 3) AS UNSIGNED)) AS max_id FROM t_bahan_masuk_keluar";
    $stmt_id = $conn->prepare($query_id);
    $stmt_id->execute();
    $result_id = $stmt_id->get_result();
    $row = $result_id->fetch_assoc();

    $new_id_number = $row['max_id'] ? $row['max_id'] + 1 : 1;
    $id_stok_keluar = 'K' . str_pad($new_id_number, 4, '0', STR_PAD_LEFT);

    $insert_query = "INSERT INTO t_bahan_masuk_keluar (id_bahan_baku,nama_bahan_baku,id_stok_keluar,kode_barcode,kuantitas,id_kategori) VALUES(?,?,?,?,?,?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt ->bind_param('ssssis',$id_bahan_baku,$nama_bahan_baku,$id_stok_keluar,$kode_barcode,$kuantitas,$id_kategori);
    if (!$insert_stmt->execute()){
        throw new Exception('Gagal menambahkan transaksi: ' . $insert_stmt->error);
    }

    // Update kuantitas bahan baku
    $update_query = "UPDATE t_bahan_baku SET kuantitas = kuantitas - ? WHERE id_bahan_baku = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('is', $kuantitas, $id_bahan_baku);

    if (!$update_stmt->execute()){
        throw new Exception('Gagal mengupdate kuantitas bahan baku: ' . $update_stmt->error);
    }

    mysqli_commit($conn);

    return[
        'success' => true,
        'message' => 'Transaksi berhasil ditambahkan dan stok diperbarui'
    ];
 } catch (Exception $e){
    mysqli_rollback($conn);

    return[
        'success' => false,
        'message' =>$e->getMessage()
    ];
 }
}

//Update stok keluar
function update_stok_keluar($POST)
{
    global $conn;

    $id_transaksi = $POST['id_transaksi'];
    $kuantitas_baru = $POST['kuantitas']; 

    if(!is_numeric($kuantitas_baru) || $kuantitas_baru < 0){
        return[
            'success' => false,
            'message' => 'Kuantitas tidak boleh kurang dari 0'
        ];
    }

    mysqli_begin_transaction($conn);

    try {
        // Ambil data transaksi lama berdasarkan ID transaksi
        $query_transaksi = "SELECT id_bahan_baku, kuantitas FROM t_bahan_masuk_keluar WHERE id_transaksi = ?";
        $stmt_transaksi = $conn->prepare($query_transaksi);
        $stmt_transaksi->bind_param('i', $id_transaksi);
        $stmt_transaksi->execute();
        $result_transaksi = $stmt_transaksi->get_result();
        $data_transaksi = $result_transaksi->fetch_assoc();

        if (!$data_transaksi) {
            throw new Exception("Data transaksi tidak ditemukan.");
        }

        $id_bahan_baku = $data_transaksi['id_bahan_baku'];
        $kuantitas_lama = $data_transaksi['kuantitas'];

        // Ambil stok bahan baku saat ini
        $query_stok = "SELECT kuantitas FROM t_bahan_baku WHERE id_bahan_baku = ?";
        $stmt_stok = $conn->prepare($query_stok);
        $stmt_stok->bind_param('s', $id_bahan_baku);
        $stmt_stok->execute();
        $result_stok = $stmt_stok->get_result();
        $data_stok = $result_stok->fetch_assoc();

        if (!$data_stok) {
            throw new Exception("Data bahan baku tidak ditemukan.");
        }

        $stok_sekarang = $data_stok['kuantitas'];

        // Hitung selisih kuantitas dan update stok
        if ($kuantitas_baru >= $kuantitas_lama) {
            $selisih = $kuantitas_baru - $kuantitas_lama;
            if ($stok_sekarang < $selisih) {
                throw new Exception("Stok tidak cukup untuk melakukan transaksi ini.");
            }
            $stok_baru = $stok_sekarang - $selisih;
        } else {
            $selisih = $kuantitas_lama - $kuantitas_baru;
            $stok_baru = $stok_sekarang + $selisih;
        }

        // Update stok bahan baku
        $update_stok = "UPDATE t_bahan_baku SET kuantitas = ? WHERE id_bahan_baku = ?";
        $stmt_update_stok = $conn->prepare($update_stok);
        $stmt_update_stok->bind_param('is', $stok_baru, $id_bahan_baku);
        if (!$stmt_update_stok->execute()) {
            throw new Exception("Gagal memperbarui stok bahan baku.");
        }

        // Update data di tabel bahan masuk keluar
        $update_transaksi = "UPDATE t_bahan_masuk_keluar 
                             SET kuantitas = ?
                             WHERE id_transaksi = ?";
        $stmt_update_transaksi = $conn->prepare($update_transaksi);
        $stmt_update_transaksi->bind_param('is', $kuantitas_baru, $id_transaksi);
        if (!$stmt_update_transaksi->execute()) {
            throw new Exception("Gagal memperbarui data transaksi.");
        }

        mysqli_commit($conn);

        return [
            'success' => true,
            'message' => 'Transaksi berhasil diperbarui dan stok berhasil diperbarui.'
        ];
    } catch (Exception $e) {
        mysqli_rollback($conn);

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
// menghapus transaksi
function delete_transaksi ($id_transaksi)
{
    global $conn;

    $query = "DELETE FROM t_bahan_masuk_keluar WHERE id_transaksi = ?";
    $stmt = $conn->prepare($query);

    // Cek prepare
    if ($stmt === false) {
        error_log("Error pada prepare statement: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $id_transaksi);

    if ($stmt->execute()) {
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows > 0;
    }else{
        error_log("Error pada execute statement: " . $stmt->error);
        return false;
    }
}

?>