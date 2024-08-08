<?php
session_start();

include($_SERVER['DOCUMENT_ROOT'] . '/rekomendasi-laptop/config/config.php');

if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
    header("Location: login");
    exit();
}

$id_user = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="Laptop, Knowledge-based filtering, MSI, Acer, Asus, HP, Dell, Lenovo, Sistem Rekomendasi">
    <meta name="author" content="Louis">

    <title>Sistem Rekomendasi Laptop - Cari Rekomendasi Laptop</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Custom style untk form pencarian -->
    <style>
        #preferencesForm .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        #preferencesForm .form-group {
            margin-bottom: 0.5rem;
        }
    </style>

    <!-- Custom styles for this template-->
    <link href="css/web-laptop.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>
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
            <li class="nav-item">
                <a class="nav-link" href="dashboard">
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
            <li class="nav-item active">
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
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>



                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">

                                    <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>
                                </span>
                                <i class="fas fa-user-circle fa-lg" style="color: #228B22;"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">



                                <a class="dropdown-item" href="#" onclick="confirmLogout()">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Cari Rekomendasi Laptop</h1>
                    <p class="mb-4">Gunakan form di bawah ini untuk mencari rekomendasi laptop sesuai kebutuhan Anda.
                    </p>

                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Form Pencarian</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Preference form -->
                                    <form id="preferencesForm">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="brandSelect">Pilih Brand:</label>
                                                <select id="brandSelect" name="brand" class="form-control form-control-sm">
                                                    <option value="">Semua Brand</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="categorySelect">Kategori:</label>
                                                <select id="categorySelect" name="category" class="form-control form-control-sm">
                                                    <option value="">Semua Kategori</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="priceRange">Cari berdasarkan harga (IDR):</label>
                                            <input type="range" id="priceRange" class="form-control-range" min="0" max="50000000" step="100000" value="50000000">
                                            <p id="priceDisplay">IDR 0 - IDR 25,000,000</p>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="getRecommendations()">Cari</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recommendations Table -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Hasil Rekomendasi Laptop</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="recommendationsTable" class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Gambar</th>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Harga</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                                <!-- Laptop Details Modal -->
                                <div class="modal fade" id="laptopDetailsModal" tabindex="-1" role="dialog" aria-labelledby="laptopDetailsModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="laptopDetailsModalLabel">Spesifikasi Laptop
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" id="laptopDetailsContent">
                                                <!-- ISI SPESIFIKASI LAPTOP -->
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Sistem Rekomendasi Laptop Berbasis <i>Knowledge-Based Filtering
                                2024</i></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/web-laptop.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- My custom script -->
    <script src="js/pages/rekomendasi-laptop.js"></script>

    <!-- Konfirmasi Logout -->
    <script src="js/pages/logout.js"></script>


</body>

</html>