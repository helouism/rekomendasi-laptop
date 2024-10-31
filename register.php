<?php
include __DIR__ . '/config/config.php';
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

                    $stmt = $conn->prepare("INSERT INTO tb_user (username, password, role) VALUES (?, ?, 'user')");
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
            error_log("IP Address: " . $_SERVER['REMOTE_ADDR']);
            die("token validation failed");
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem Rekomendasi Laptop">
    <meta name="keywords"
        content="Laptop, Knowledge-based filtering, MSI, Acer, Asus, HP, Dell, Lenovo, Sistem Rekomendasi">
    <meta name="author" content="Louis">

    <title>Sistem Rekomendasi Laptop - Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/web-laptop.min.css" rel="stylesheet">


</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-6 col-lg-8 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Sistem Rekomendasi Laptop dengan
                                            <i>Knowledge-based filtering</i>
                                        </h1>
                                        <h1 class="h4 text-gray-900 mb-4">Daftar</h1>
                                    </div>
                                    <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                                        method="POST">

                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="username"
                                                placeholder="Masukkan Username" value="" maxlength="50" autofocus
                                                autocomplete="off" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="password" class="form-control form-control-user"
                                                    id="password" name="password" placeholder="Masukkan Password"
                                                    value="" autocomplete="off" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        id="togglePassword">
                                                        <i class="fa fa-eye" aria-hidden="true"
                                                            style="color: #228B22;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="password" class="form-control form-control-user"
                                                    id="cpassword" name="cpassword" placeholder="Konfirmasi Password"
                                                    value="" autocomplete="off" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        id="toggleCPassword">
                                                        <i class="fa fa-eye" aria-hidden="true"
                                                            style="color: #228B22;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="token" value="<?php echo $token; ?>" />

                                        <button class="btn btn-primary btn-user btn-block" type="submit" name="submit">
                                            Register
                                        </button>
                                    </form>

                                    <hr>

                                    <div class="text-center">
                                        <a class="small" href="login">Sudah punya akun ? Login disini !</a>
                                    </div>

                                    <?php if (isset($warning)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo htmlspecialchars($warning); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>


    <!-- Custom scripts for all pages-->
    <script src="js/web-laptop.min.js"></script>


    <!-- Button Lihat password-->
    <script>
        function togglePasswordVisibility(passwordId, toggleId) {
            const togglePassword = document.querySelector('#' + toggleId);
            const password = document.querySelector('#' + passwordId);

            togglePassword.addEventListener('click', function (e) {
                // TOGGLE TIPE KOLOM INPUT PASSWORD & KONFIRMASI PASSWORD
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                // TOGGLE IKON MATA
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }

        togglePasswordVisibility('password', 'togglePassword');
        togglePasswordVisibility('cpassword', 'toggleCPassword');
    </script>

</body>

</html>