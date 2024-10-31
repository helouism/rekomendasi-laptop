<?php
//IMPORT AUTOLOADER
require __DIR__ . '/../vendor/autoload.php';

// AMBIL VARIABEL UNTUK KONEKSI DARI FILE .ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// DETAIL KONEKSI DATABASE
$host = $_ENV['DB_HOST'];
$db_username = $_ENV['DB_USERNAME'];
$db_password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

// Buat koneksi ke database
$conn = new mysqli($host, $db_username, $db_password, $dbname);

// Cek Koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SET CHARSET KE utf8mb4
if (!$conn->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", $conn->error);
    exit();
}

// NONAKTIFKAN ANSI mode
$conn->query("SET SESSION sql_mode = ''");