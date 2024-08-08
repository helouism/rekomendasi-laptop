<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
    header("Location: login");
    exit();
}

include($_SERVER['DOCUMENT_ROOT'] . '/rekomendasi-laptop/config/config.php');
// Mengambil semua nama brand
$brands = [];
$sql = "SELECT DISTINCT brand_name FROM tb_laptop";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row['brand_name'];
    }
}

// Mengambil kategori
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
