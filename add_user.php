<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

include __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '';
    $password = $_POST['password'];
    $role = $_POST['role'];

    // VALIDASI INPUT
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username and password are required']);
        exit;
    }

    // MEMERIKSA APAKAH USERNAME SUDAH ADA DI DATABASE
    $stmt = $conn->prepare("SELECT id_user FROM tb_user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit;
    }
    $stmt->close();

    // ENKRIPSI PASSWOD
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // TAMBAH USER KE DATABASE
    $stmt = $conn->prepare("INSERT INTO tb_user (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }

    $stmt->close();
    mysqli_close($conn);
}
