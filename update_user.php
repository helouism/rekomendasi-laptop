<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

include __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'];
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '';

    $password = isset($_POST['password']) && !empty($_POST['password']) ?
        password_hash($_POST['password'], PASSWORD_DEFAULT) : null;


    if ($password) {
        $stmt = $conn->prepare("UPDATE tb_user SET username = ?, password = ? WHERE id_user = ?");
        $stmt->bind_param("ssi", $username, $password, $id_user);
    } else {
        $stmt = $conn->prepare("UPDATE tb_user SET username = ? WHERE id_user = ?");
        $stmt->bind_param("si", $username, $id_user);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }

    $stmt->close();
    mysqli_close($conn);
}
