```php
<?php
ob_start();
session_start(); // Pastikan sesi dimulai di sini
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password_input = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? '';

    // Validasi input
    if (empty($name) || empty($email) || empty($password_input)) {
        header('Location: login.php?error=empty_fields' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: login.php?error=invalid_email' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
        exit();
    }
    if (strlen($password_input) < 6) {
        header('Location: login.php?error=password_too_short' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
        exit();
    }

    try {
        // Cek pengguna di database
        $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? AND name = ?");
        $stmt->execute([$email, $name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        var_dump($user);

        if ($user && password_verify($password_input, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_logged_in'] = true;

            // Debugging
            error_log("Login successful for user: {$user['email']} with role: {$user['role']} at " . date('Y-m-d H:i:s'));

            require_once 'check-admin.php';
            if (isAdmin()) {
                header('Location: dashboard.php');
                exit();
            } elseif ($redirect) {
                header('Location: ' . urldecode($redirect));
                exit();
            } else {
                header('Location: index.php');
                exit();
            }
        } else {
            error_log("Invalid credentials for email: $email, name: $name at " . date('Y-m-d H:i:s'));
            header('Location: login.php?error=invalid_credentials' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
        header('Location: login.php?error=database_error' . ($redirect ? '&redirect=' . urlencode($redirect) : ''));
        exit();
    }
} else {
    if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
        header('Location: login.php');
        exit();
    } else {
        require_once 'check-admin.php';
        if (isAdmin()) {
            header('Location: dashboard.php');
            exit();
        } else {
            header('Location: index.php');
            exit();
        }
    }
}
ob_end_flush();
?>