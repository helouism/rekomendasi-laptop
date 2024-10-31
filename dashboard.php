<?php
session_start();

include __DIR__ . '/config/config.php';


// CEK JIKA USER SUDAH LOGIN DAN PUNYA ROLE USER 
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login");
    exit();
}


// Menampilkan total laptop yang dibookmark oleh pengguna
$id_user = $_SESSION['id_user'];
$bookmark_query = "SELECT COUNT(*) as bookmark_count FROM tb_bookmarks WHERE id_user = $id_user";
$bookmark_result = mysqli_query($conn, $bookmark_query);
$bookmark_row = mysqli_fetch_assoc($bookmark_result);
$total_bookmarks = $bookmark_row['bookmark_count'];

// Menampilkan total laptop pada database
$laptop_query = "SELECT COUNT(*) as laptop_count FROM tb_laptop";
$laptop_result = mysqli_query($conn, $laptop_query);
$laptop_row = mysqli_fetch_assoc($laptop_result);
$total_laptops = $laptop_row['laptop_count'];

mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description"
        content="laptop recommender system, acer,asus, HP,lenovo, knowledge-based filtering, sistem rekomendasi laptop">
    <meta name="author" content="Louis">

    <title>Sistem Rekomendasi Laptop - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/web-laptop.min.css" rel="stylesheet">
    <style>
        .sidebar-toggled-init {
            display: none;
        }
    </style>

    <script>

        (function () {
            if (localStorage.getItem("sidebarCollapsed") === "true") {
                document.documentElement.classList.add('sidebar-toggled-init');
            }
        })();
    </script>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard">
                <div class="sidebar-brand-icon ">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Sistem Rekomendasi Laptop</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Fitur
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="rekomendasi-laptop">
                    <i class="fas fa-search"></i>
                    <span>Rekomendasi Laptop</span></a>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Profile
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="lihat-bookmarks">
                    <i class="fas fa-fw fa-bookmark"></i>
                    <span>Bookmark</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>



        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->

                <?php include 'includes/topbar.php'; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->


                    <!-- Content Row -->
                    <div class="row">
                        <!-- Total Bookmarked Laptops Card -->
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Laptop yang dibookmark</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo $total_bookmarks; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bookmark fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Laptops in Database Card -->
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Laptop yang tersedia</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo $total_laptops; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-laptop fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- About Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Sistem Rekomendasi Laptop dengan
                                        <i>Knowledge-based filtering</i>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p>Dengan website ini, anda bisa mencari laptop yang sesuai dengan kebutuhan anda
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->


                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                <?php include 'includes/footer.php'; ?>



            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="js/web-laptop.min.js"></script>

        <!-- Konfirmasi logout -->
        <script src="js/pages/logout.js"></script>






</body>

</html>