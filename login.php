<?php

// login
require 'includes/login_controller.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="Laptop, Knowledge-based filtering, MSI, Acer, Asus, HP, Dell, Lenovo, Sistem Rekomendasi">
    <meta name="author" content="Louis">

    <title>Sistem Rekomendasi Laptop - Login</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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
                                        <h1 class="h4 text-gray-900 mb-4">Sistem Rekomendasi Laptop dengan <i>Knowledge-based filtering</i></h1>

                                        <h1 class="h4 text-gray-900 mb-4">Login</h1>
                                    </div>
                                    <form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="POST" class="user">
                                       
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" id="username" name="username" value="" placeholder="Masukkan Username..." maxlength="10" autofocus autocomplete="off" required>
                                        </div>
                                       
                                        
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="password" class="form-control form-control-user" id="password" name="password" value="" autocomplete="off" placeholder="Masukkan Password" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                        <i class="fa fa-eye " aria-hidden="true" style="color: #228B22;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="token" value="<?php echo $token; ?>" />

                                        <button class="btn btn-primary btn-user btn-block" type="submit" name="login">
                                            Login
                                        </button>
                                    </form>

                                    <hr>

                                    <div class="text-center">
                                        <a class="small" href="register">Belum punya akun ? Daftar disini ! </a>
                                    </div>

                                    <?php if (isset($warning)) : ?>
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
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>
