<?php

//Memastikan akses ke view-bookmarks-data.php hanya melalui AJAX / Mencegah direct access
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header("Location: login");
}


session_start();
include __DIR__ . '/config/config.php';

header('Content-Type: application/json');



// CEK JIKA USER SUDAH LOGIN DAN PUNYA ROLE USER 
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login");
    exit();
}
$id_user = $_SESSION['id_user'];

// Debugging
error_log("Session data: " . print_r($_SESSION, true));


$sql = "SELECT l.* FROM tb_laptop l
        JOIN tb_bookmarks b ON l.id_laptop = b.id_laptop
        WHERE b.id_user = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(['error' => 'Database prepare error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id_user);

if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    echo json_encode(['error' => 'Database execute error: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
if ($result === false) {
    error_log("Get result failed: " . $stmt->error);
    echo json_encode(['error' => 'Database get result error: ' . $stmt->error]);
    exit;
}

$bookmarks = [];
while ($row = $result->fetch_assoc()) {
    $bookmarks[] = $row;
}

$stmt->close();
$conn->close();

// DEBUGGING
error_log("JSON to be sent: " . json_encode($bookmarks));

echo json_encode($bookmarks);
exit;
