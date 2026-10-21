<?php
if (!defined('BASEPATH')) {
    define('BASEPATH', dirname(__DIR__));
}

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'connection.php') {
    die('No direct access allowed');
}

// --- Konfigurasi Database Aiven ---
$host = "mysql-25c76f65-pemilosproject.e.aivencloud.com";
$user = "avnadmin";
$pass = "PASTE_PASSWORD_AIVEN_LU_DISINI"; // Isi pakai password yang depannya AVNS_...
$db   = "pemilosdb"; 
$port = 23935;

// Inisialisasi koneksi MySQLi
$con = mysqli_init();

// Koneksi ke Aiven wajib pakai SSL, ditambahkan flag MYSQLI_CLIENT_SSL
mysqli_real_connect($con, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$con) {
    die('Database connection failed: ' . mysqli_connect_error());
}
?>