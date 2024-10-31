<?php


//Memastikan akses ke bookmarks.php hanya melalui AJAX / Mencegah direct access
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
$id_laptop = $_POST['id_laptop'] ?? null;
$action = $_POST['action'] ?? null;

if (!$id_laptop || !$action) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

if ($action === 'add') {
    // Cek jika sudah pernah dibookmark
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tb_bookmarks WHERE id_user = ? AND id_laptop = ?");
    $stmt->bind_param("ii", $id_user, $id_laptop);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Laptop already bookmarked']);
        exit;
    }

    // Tambah ke bookmark
    $stmt = $conn->prepare("INSERT INTO tb_bookmarks (id_user, id_laptop) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_user, $id_laptop);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Laptop bookmarked successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to bookmark laptop']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$stmt->close();
$conn->close();
