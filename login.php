<?php
session_start();

// Cek jika sudah login
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']) {
    require_once 'check-admin.php';
    if (isAdmin()) {
        header('Location: dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Tangani parameter URL untuk alert
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$redirect = $_GET['redirect'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style-auth.css?v=<?php echo time(); ?>" />
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Login</h1>
            <p>Silakan masuk untuk membeli tiket atau produk UMKM</p>
        </div>

        <div id="alert-container">
            <?php if ($error || $success): ?>
                <div class="alert <?php echo $error ? 'alert-error' : 'alert-success'; ?>">
                    <?php
                    if ($error) {
                        switch ($error) {
                            case 'invalid_credentials':
                                echo 'Nama, email, atau password salah.';
                                break;
                            case 'empty_fields':
                                echo 'Semua field harus diisi.';
                                break;
                            case 'invalid_email':
                                echo 'Email tidak valid.';
                                break;
                            case 'password_too_short':
                                echo 'Kata sandi minimal 6 karakter.';
                                break;
                            case 'database_error':
                                echo 'Terjadi kesalahan server. Silakan coba lagi.';
                                break;
                            case 'login_failed':
                                echo 'Terjadi kesalahan saat login. Silakan coba lagi.';
                                break;
                            case 'not_logged_in':
                                echo 'Anda harus login terlebih dahulu untuk mengakses halaman tersebut.';
                                break;
                            case 'password_reset_sent':
                                echo 'Link reset password telah dikirim ke email Anda.';
                                break;
                            case 'token_invalid':
                                echo 'Link reset password tidak valid atau sudah kadaluarsa.';
                                break;
                            case 'password_mismatch':
                                echo 'Konfirmasi kata sandi tidak cocok.';
                                break;
                            case 'reset_failed':
                                echo 'Gagal mereset kata sandi. Silakan coba lagi.';
                                break;
                            default:
                                echo 'Terjadi kesalahan. Silakan coba lagi.';
                        }
                    } elseif ($success === 'true') {
                        echo 'Pendaftaran berhasil! Silakan login dengan akun Anda.';
                    } elseif ($success === 'password_reset') {
                        echo 'Kata sandi Anda berhasil direset! Silakan login dengan kata sandi baru Anda.';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="cek-login.php<?php echo $redirect ? '?redirect=' . urlencode($redirect) : ''; ?>" method="POST" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name" placeholder="Masukkan Nama" required />
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan Email" required />
            </div>

            <div class="form-group password-group">
                <label for="password">Kata Sandi</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Masukkan Kata Sandi" required />
                    <span class="toggle-password" onclick="togglePasswordField('password')">
                        <i class="fas fa-eye" id="password-icon"></i>
                    </span>
                </div>
            </div>

            <div class="forgot-password">
                <a href="forgot-password.php">Lupa kata sandi?</a>
            </div>

            <button type="submit" class="submit-btn">Masuk</button>

            <?php if ($redirect): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>
        </form>

        <div class="form-footer">
            <div class="register-link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </div>

            <div class="back-link">
                <a href="index.php">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordField(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId + '-icon');

            if (passwordInput.getAttribute('type') === 'password') {
                passwordInput.setAttribute('type', 'text');
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.setAttribute('type', 'password');
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>