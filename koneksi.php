<?php
ob_start(); // Start output buffering at the very beginning

// Database configuration
$host = 'localhost';
$username = 'root'; // Adjust to your database username
$password = '@kaesquare123'; // Adjust to your database password
$database = 'projec15_mahligai_db';

try {
    global $pdo; // Declare $pdo as global
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Debug: log successful connection
    error_log("Database connection successful at " . date('Y-m-d H:i:s'));
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage()); // Log error for debugging
    die("Koneksi database gagal: " . $e->getMessage()); // Stop script and display error message
}

ob_end_flush(); // End output buffering and send output to browser
