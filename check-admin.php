<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';

// Pastikan $pdo tersedia
if (!isset($pdo) || $pdo === null) {
    error_log("PDO connection is not available in check-admin.php at " . date('Y-m-d H:i:s'));
    die("Koneksi database gagal. Silakan periksa konfigurasi.");
}

// Fungsi untuk memeriksa apakah pengguna adalah admin berdasarkan role di database
function isAdmin()
{
    global $pdo; // Akses $pdo sebagai global
    if (isset($_SESSION['user_email'])) {
        try {
            $stmt = $pdo->prepare("SELECT role FROM users WHERE email = ?");
            $stmt->execute([$_SESSION['user_email']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $role = $user['role'] ?? 'null';
                error_log("User {$_SESSION['user_email']} role checked: $role at " . date('Y-m-d H:i:s'));
                return $role === 'admin';
            } else {
                error_log("No user found for email: {$_SESSION['user_email']} at " . date('Y-m-d H:i:s'));
            }
        } catch (PDOException $e) {
            error_log("Error checking admin role: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
        }
    } else {
        error_log("No user email in session at " . date('Y-m-d H:i:s'));
    }
    return false;
}

// Fungsi untuk memastikan hanya admin yang dapat mengakses halaman
function requireAdmin()
{
    global $pdo; // Akses $pdo sebagai global
    if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in'] || !isAdmin()) {
        error_log("Access denied for user: " . ($_SESSION['user_email'] ?? 'Not logged in') . " at " . date('Y-m-d H:i:s'));
        header('Location: login.php?error=access_denied');
        exit();
    }
}

ob_end_flush();
