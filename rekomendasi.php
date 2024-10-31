<?php
session_start();

// CEK JIKA USER SUDAH LOGIN DAN PUNYA ROLE USER 
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login");
    exit();
}
include __DIR__ . '/config/config.php';

// Mendapatkan pilihan pengguna(brand, kategori, harga) dari POST request
$brand = $conn->real_escape_string($_POST['brand'] ?? '');
$category = $conn->real_escape_string($_POST['category'] ?? '');
$max_price = intval($_POST['max_price'] ?? 50000000);

$sql = "SELECT *, image_url FROM tb_laptop WHERE price <= ?";
$params = [$max_price];
$types = "i";

if ($brand) {
    $sql .= " AND brand_name = ?";
    $params[] = $brand;
    $types .= "s";
}

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$laptops = [];
while ($row = $result->fetch_assoc()) {
    $laptops[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($laptops);
