<?php

session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

include __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_user'])) {
    $id_user = $_POST['id_user'];

    // TIDAK BISA HAPUS AKUN SENDIRI
    if ($id_user == $_SESSION['id_user']) {
        echo json_encode(['success' => false, 'message' => 'Tidak bisa menghapus akunmu sendiri']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // HAPUS BOOKMARKNYA DULU KALAU ADA
        $stmt = $conn->prepare("DELETE FROM tb_bookmarks WHERE id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $stmt->close();

        // HAPUS USERNYA
        $stmt = $conn->prepare("DELETE FROM tb_user WHERE id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $stmt->close();

        // COMMIT TRANSACTION
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // JIKA ADA ERROR< ROLLBACK TRANSACTION
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menghapus user. Error: ' . $e->getMessage()
        ]);
    } finally {
        mysqli_close($conn);
    }
}
