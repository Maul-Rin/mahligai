<?php
session_start();
require_once 'koneksi.php'; // Pastikan file koneksi.php ada dan berfungsi

$token = $_GET['token'] ?? '';
$error = $_GET['error'] ?? '';

$valid_token = false;
$user_email = '';

if (!empty($token)) {
    try {
        $stmt = $pdo->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        $reset_entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reset_entry && strtotime($reset_entry['expires_at']) > time()) {
            $valid_token = true;
            $user_email = $reset_entry['email'];
        } else {
            $error = 'token_invalid';
        }
    } catch (PDOException $e) {
        error_log("Reset password token check error: " . $e->getMessage());
        $error = 'database_error';
    }
} else {
    $error = 'token_invalid';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $new_password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $post_token = $_POST['token'] ?? ''; // Token dari hidden input

    // Validasi token POST dengan token GET/awal
    if ($post_token !== $token) {
        header('Location: reset-password.php?token=' . urlencode($token) . '&error=token_invalid');
        exit();
    }

    if (empty($new_password) || empty($confirm_password)) {
        header('Location: reset-password.php?token=' . urlencode($token) . '&error=empty_fields');
        exit();
    }

    if (strlen($new_password) < 6) {
        header('Location: reset-password.php?token=' . urlencode($token) . '&error=password_too_short');
        exit();
    }

    if ($new_password !== $confirm_password) {
        header('Location: reset-password.php?token=' . urlencode($token) . '&error=password_mismatch');
        exit();
    }

    try {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password pengguna di tabel `users`
        $stmt_update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt_update->execute([$hashed_password, $user_email]);

        // Hapus token reset dari tabel `password_resets`
        $stmt_delete_token = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt_delete_token->execute([$user_email]);

        header('Location: login.php?success=password_reset');
        exit();

    } catch (PDOException $e) {
        error_log("Update password error: " . $e->getMessage());
        header('Location: reset-password.php?token=' . urlencode($token) . '&error=reset_failed');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Kata Sandi | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style-auth.css?v=<?php echo time(); ?>" />
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Reset Kata Sandi</h1>
            <p>Masukkan kata sandi baru Anda.</p>
        </div>

        <div id="alert-container">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php
                    switch ($error) {
                        case 'token_invalid':
                            echo 'Link reset password tidak valid atau sudah kadaluarsa. Silakan minta reset baru.';
                            break;
                        case 'empty_fields':
                            echo 'Semua field harus diisi.';
                            break;
                        case 'password_too_short':
                            echo 'Kata sandi minimal 6 karakter.';
                            break;
                        case 'password_mismatch':
                            echo 'Konfirmasi kata sandi tidak cocok.';
                            break;
                        case 'reset_failed':
                            echo 'Gagal mereset kata sandi. Silakan coba lagi.';
                            break;
                        case 'database_error':
                            echo 'Terjadi kesalahan server. Silakan coba lagi.';
                            break;
                        default:
                            echo 'Terjadi kesalahan. Silakan coba lagi.';
                    }
                    ?>
                </div>
            <?php elseif (!$valid_token): ?>
                 <div class="alert alert-error">
                    Link reset password tidak valid atau sudah kadaluarsa. Silakan minta reset baru.
                </div>
            <?php endif; ?>
        </div>

        <?php if ($valid_token): ?>
            <form action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group password-group">
                    <label for="password">Kata Sandi Baru</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Masukkan Kata Sandi Baru" required />
                        <span class="toggle-password" onclick="togglePasswordField('password')">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group password-group">
                    <label for="confirm_password">Konfirmasi Kata Sandi Baru</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Kata Sandi Baru" required />
                        <span class="toggle-password" onclick="togglePasswordField('confirm_password')">
                            <i class="fas fa-eye" id="confirm_password-icon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Reset Kata Sandi</button>
            </form>
        <?php else: ?>
            <div class="form-footer">
                <p>Silakan kembali ke halaman lupa kata sandi untuk meminta link baru.</p>
                <div class="back-link">
                    <a href="forgot-password.php">
                        <i class="fas fa-arrow-left"></i>
                        Minta Link Baru
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-footer">
            <div class="back-link">
                <a href="login.php">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordField(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId + '-icon'); // Assuming icon ID is fieldId-icon

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