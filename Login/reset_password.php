<?php
session_start();
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];
    $new_password = $_POST['new_password'];
    $email = $_SESSION['email'] ?? null;

    if (!$email || $otp != $_SESSION['otp']) {
        header("Location: reset_password.php?error=OTP salah atau sesi telah berakhir.");
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $query = $conn->prepare("UPDATE pengguna SET password = ? WHERE email = ?");
    $query->bind_param('ss', $hashed_password, $email);
    if ($query->execute()) {
        session_destroy();
        header("Location: login.php?message=Kata sandi berhasil direset. Silakan login.");
    } else {
        header("Location: reset_password.php?error=Gagal memperbarui kata sandi.");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Reset Kata Sandi</h2>
        <form action="reset_password.php" method="POST">
            <div class="form-group">
                <label for="otp">Kode OTP</label>
                <input type="text" name="otp" id="otp" class="form-control" placeholder="Masukkan Kode OTP" required>
            </div>
            <div class="form-group">
                <label for="new_password">Kata Sandi Baru</label>
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Kata Sandi Baru" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Kata Sandi</button>
        </form>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
