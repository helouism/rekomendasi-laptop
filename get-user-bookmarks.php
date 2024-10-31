<?php

//Memastikan akses ke get-user-bookmarks.php hanya melalui AJAX / Mencegah direct access
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header("Location: login");
}


session_start();
include __DIR__ . '/config/config.php';

// CEK JIKA USER SUDAH LOGIN DAN PUNYA ROLE USER 
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login");
    exit();
}
$id_user = $_SESSION['id_user'];

$stmt = $conn->prepare("SELECT id_laptop FROM tb_bookmarks WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

$bookmarks = [];
while ($row = $result->fetch_assoc()) {
    $bookmarks[] = $row['id_laptop'];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($bookmarks);
