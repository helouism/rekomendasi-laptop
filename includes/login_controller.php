<?php
include($_SERVER['DOCUMENT_ROOT'] . '/rekomendasi-laptop/config/config.php');
require_once "includes/CSRF.php";
session_start();

// Jika user sudah login, arahkan ke dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard");
    exit();
}

$warning = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Debug: Log the attempted login
    // error_log("Login attempt for user: " . $username);

    // Prepared statement untuk menghindari SQL injection
    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Validasi token
    if (CSRF::validate($_POST['token'])) {

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Menggunakan password_verify untuk memastikan password benar
            if (password_verify($password, $user['password'])) {

                // session_regenerate_id(true)  

                $_SESSION['username'] = $username;

                $_SESSION['id_user'] = $user['id_user'];
                header("Location: dashboard");
                exit();
            } else {
                // Password atau username salah
                // error_log("Login failed for user: " . $username . " (incorrect password)");
                $warning = 'Username atau password Anda salah. Silakan coba lagi!';
            }
        } else {
            // User tidak ditemukan
            // error_log("Login failed for user: " . $username . " (user not found)");
            $warning = 'Username belum terdaftar. Silakan daftar terlebih dahulu!';
        }
        $stmt->close();
    } else {
        // Validasi token gagal
        exit("Gagal validasi token");
    }
}
