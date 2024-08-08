<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
    header("Location: login");
    exit();
}

include($_SERVER['DOCUMENT_ROOT'] . '/rekomendasi-laptop/config/config.php');

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
