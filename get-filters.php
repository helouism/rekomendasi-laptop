<?php
session_start();

// CEK JIKA USER SUDAH LOGIN DAN PUNYA ROLE USER 
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login");
    exit();
}
include __DIR__ . '/config/config.php';
// Mengambil semua nama brand
$brands = [];
$sql = "SELECT DISTINCT brand_name FROM tb_laptop";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row['brand_name'];
    }
}

// Mengambil semua kategori
$categories = [];
$sql = "SELECT DISTINCT category FROM tb_laptop";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode(['brands' => $brands, 'categories' => $categories]);
