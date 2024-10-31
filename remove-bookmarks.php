<?php

//Memastikan akses ke remove_bookmarks.php hanya melalui AJAX / Mencegah direct access
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
$id_laptop = $_POST['id_laptop'];

$sql = "DELETE FROM tb_bookmarks WHERE id_user = ? AND id_laptop = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_user, $id_laptop);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove bookmark']);
}

$stmt->close();
$conn->close();