
<?php
session_start();

// Cek apakah ini request AJAX
if (isset($_GET['ajax']) && $_GET['ajax'] == 'true') {
    header('Content-Type: application/json');

    $response = [];

    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']) {
        $response['logged_in'] = true;
        $response['user_name'] = $_SESSION['user_name'];
        $response['user_email'] = $_SESSION['user_email'];
        require_once 'check-admin.php';
        $response['is_admin'] = isAdmin();
    } else {
        $response['logged_in'] = false;
    }

    echo json_encode($response);
    exit();
}

// Jika bukan AJAX, redirect ke halaman utama
header('Location: index.php');
exit();
?>