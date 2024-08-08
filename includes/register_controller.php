<?php
include($_SERVER['DOCUMENT_ROOT'] . '/rekomendasi-laptop/config/config.php');
session_start();

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

// Jika user sudah login, arahkan ke dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard");
    exit();
}

$warning = null;

// Cek apakah kombinasi password aman
function isPasswordValid($password)
{
    // Pola regex : setidaknya satu angka, satu huruf besar, satu huruf kecil, dan minimal 8 karakter.
    $pattern = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/';
    return preg_match($pattern, $password);
}


// Cek apakah password mengandung spasi
function containsSpace($string)
{
    return preg_match('/\s/', $string);
}

if (isset($_POST['submit'])) {

    if (!empty($_POST['token'])) {
        if (hash_equals($_SESSION['token'], $_POST['token'])) {
            $username = isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '';
            $password = $_POST['password'];
            $cpassword = $_POST['cpassword'];

            if (containsSpace($username)) {
                $warning = 'Username tidak boleh mengandung spasi.';
            } elseif (!isPasswordValid($password)) {
                $warning = 'Password harus mengandung setidaknya satu angka, satu huruf besar, satu huruf kecil, dan minimal 8 karakter.';
            } elseif ($password == $cpassword) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("SELECT * FROM tb_user WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 0) {
                    $stmt = $conn->prepare("INSERT INTO tb_user (username, password) VALUES (?, ?)");
                    $stmt->bind_param("ss", $username, $hashed_password);
                    if ($stmt->execute()) {

                        $username = "";
                        header("Location: login");
                    } else {
                        $warning = 'Maaf Terjadi kesalahan.';
                    }
                } else {
                    $warning = 'Maaf Username Sudah Terdaftar.';
                }
            } else {
                $warning = 'Password Tidak Sesuai';
            }
        } else {
            error_log("CSRF token validation failed, IP Address: " . $_SERVER['REMOTE_ADDR']);
            die("token validation failed");
        }
    }
}
