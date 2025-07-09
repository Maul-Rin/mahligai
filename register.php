<?php
ob_start();
session_start();

// Cek jika sudah login
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']) {
    if ($_SESSION['user_email'] === 'admin@mahligaiheritage.id') {
        header('Location: dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

require_once 'koneksi.php';

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $redirect = $_POST['redirect'] ?? '';

    // Validasi input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        header('Location: register.php?error=empty_fields' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: register.php?error=invalid_email' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
        exit();
    }

    if ($password !== $confirm_password) {
        header('Location: register.php?error=password_mismatch' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
        exit();
    }

    if (strlen($password) < 6) {
        header('Location: register.php?error=password_too_short' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
        exit();
    }

    try {
        // Cek apakah email sudah terdaftar
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            header('Location: register.php?error=email_exists' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
            exit();
        }

        // Simpan pengguna baru
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password]);

        // Redirect ke login dengan pesan sukses
        header('Location: login.php?success=true' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
        exit();
    } catch (PDOException $e) {
        error_log("Register error: " . $e->getMessage());
        header('Location: register.php?error=register_failed' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style-auth.css?v=<?php echo time(); ?>" />
</head>

<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Daftar Akun</h1>
            <p>Buat akun baru untuk membeli tiket atau produk UMKM</p>
        </div>

        <!-- Alert Container -->
        <div id="alert-container">
            <?php if ($error || $success): ?>
                <div class="alert <?php echo $error ? 'alert-error' : 'alert-success'; ?>">
                    <?php
                    if ($error) {
                        switch ($error) {
                            case 'empty_fields':
                                echo 'Semua field harus diisi.';
                                break;
                            case 'invalid_email':
                                echo 'Email tidak valid.';
                                break;
                            case 'password_mismatch':
                                echo 'Kata sandi dan konfirmasi kata sandi tidak cocok.';
                                break;
                            case 'password_too_short':
                                echo 'Kata sandi harus minimal 6 karakter.';
                                break;
                            case 'email_exists':
                                echo 'Email sudah terdaftar. Gunakan email lain.';
                                break;
                            case 'register_failed':
                                echo 'Pendaftaran gagal. Silakan coba lagi.';
                                break;
                            default:
                                echo 'Terjadi kesalahan. Silakan coba lagi.';
                        }
                    } elseif ($success === 'true') {
                        echo 'Pendaftaran berhasil! Silakan login dengan akun Anda.';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="register.php<?php echo $redirect ? '?redirect=' . urlencode($redirect) : ''; ?>" method="POST" id="registerForm">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" placeholder="Masukkan Nama Lengkap" required />
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

            <div class="form-group password-group">
                <label for="confirm_password">Konfirmasi Kata Sandi</label>
                <div class="password-input-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Kata Sandi" required />
                    <span class="toggle-password" onclick="togglePasswordField('confirm_password')">
                        <i class="fas fa-eye" id="confirm_password-icon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="submit-btn">Daftar</button>

            <?php if ($redirect): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>
        </form>

        <div class="form-footer">
            <div class="login-link">
                Sudah punya akun? <a href="login.php">Masuk di sini</a>
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
        // Password toggle function
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

        // Real-time password confirmation validation
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const form = document.getElementById('registerForm');

            function validatePasswords() {
                if (password.value && confirmPassword.value) {
                    if (password.value === confirmPassword.value) {
                        confirmPassword.classList.remove('match-error');
                        confirmPassword.classList.add('match-success');
                    } else {
                        confirmPassword.classList.remove('match-success');
                        confirmPassword.classList.add('match-error');
                    }
                } else {
                    confirmPassword.classList.remove('match-success', 'match-error');
                }
            }

            password.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);

            form.addEventListener('submit', function(event) {
                if (password.value !== confirmPassword.value) {
                    event.preventDefault();
                    alert('Kata sandi dan konfirmasi kata sandi tidak cocok.');
                }
            });
        });
    </script>
</body>

</html>
<?php ob_end_flush(); ?>