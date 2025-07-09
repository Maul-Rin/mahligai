<?php
ob_start();
session_start();

require_once 'koneksi.php'; // Pastikan file koneksi.php ada dan berfungsi

// Tangani parameter URL untuk alert
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

// Proses form jika ada POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Validasi input
    if (empty($email)) {
        header('Location: forgot-password.php?error=empty_email'); // Ganti ke forgot-password.php
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: forgot-password.php?error=invalid_email'); // Ganti ke forgot-password.php
        exit();
    }

    try {
        // Periksa apakah email ada di database
        $stmt_check = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt_check->execute([$email]);
        $user = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Untuk keamanan, jangan beritahu apakah email ditemukan atau tidak.
            // Selalu tampilkan pesan sukses seolah-olah email telah dikirim.
            header('Location: login.php?error=password_reset_sent'); // Redirect ke login dengan pesan sukses
            exit();
        }

        // Generate token reset password
        $reset_token = bin2hex(random_bytes(32));
        $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token berlaku 1 jam

        // Simpan token ke database (Anda perlu membuat tabel password_resets)
        // Pastikan tabel password_resets memiliki kolom email, token, expires_at
        // dan email memiliki UNIQUE index
        $stmt_token = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token = ?, expires_at = ?");
        $stmt_token->execute([$email, $reset_token, $reset_expires, $reset_token, $reset_expires]);

        // --- Simulasi Pengiriman Email (di sini Anda akan mengintegrasikan sistem email asli) ---
        $reset_link = "http://mahligaiheritage/Tugas%20Akhir/reset-password.php?token=" . $reset_token; // Ganti dengan path folder Anda

        // Dalam lingkungan produksi, Anda akan mengirim email sungguhan.
        // Contoh sederhana (PHP mail() - perlu konfigurasi server):
        // $to = $email;
        // $subject = "Reset Kata Sandi Mahligai Heritage Anda";
        // $message = "Halo " . htmlspecialchars($user['name']) . ",\n\n";
        // $message .= "Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda.\n";
        // $message .= "Silakan klik link berikut untuk mereset kata sandi Anda:\n";
        // $message .= $reset_link . "\n\n";
        // $message .= "Link ini akan kadaluarsa dalam 1 jam.\n";
        // $message .= "Jika Anda tidak meminta reset kata sandi, tidak ada tindakan lebih lanjut yang diperlukan.\n\n";
        // $message .= "Terima kasih,\nTim Mahligai Heritage";
        // $headers = "From: no-reply@mahligaiheritage.com\r\n";
        // $headers .= "Reply-To: no-reply@mahligaiheritage.com\r\n";
        // $headers .= "X-Mailer: PHP/" . phpversion();
        // mail($to, $subject, $message, $headers);
        // --- Akhir Simulasi Pengiriman Email ---

        header('Location: login.php?error=password_reset_sent'); // Redirect ke login dengan pesan sukses
        exit();

    } catch (PDOException $e) {
        error_log("Forgot password error: " . $e->getMessage());
        header('Location: forgot-password.php?error=reset_failed'); // Ganti ke forgot-password.php
        exit();
    }
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lupa Kata Sandi | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style-auth.css?v=<?php echo time(); ?>" />
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Lupa Kata Sandi?</h1>
            <p>Masukkan email Anda untuk menerima link reset kata sandi.</p>
        </div>

        <div id="alert-container">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php
                    switch ($error) {
                        case 'empty_email':
                            echo 'Email tidak boleh kosong.';
                            break;
                        case 'invalid_email':
                            echo 'Format email tidak valid.';
                            break;
                        case 'email_not_found':
                            echo 'Jika email terdaftar, link reset akan dikirim.'; // Pesan umum untuk keamanan
                            break;
                        case 'reset_failed':
                            echo 'Terjadi kesalahan saat mengirim link reset. Silakan coba lagi.';
                            break;
                        default:
                            echo 'Terjadi kesalahan. Silakan coba lagi.';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="forgot-password.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan Email Anda" required />
            </div>

            <button type="submit" class="submit-btn">Kirim Link Reset</button>
        </form>

        <div class="form-footer">
            <div class="back-link">
                <a href="login.php">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</body>

</html>