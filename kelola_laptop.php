<?php
session_start();


include __DIR__ . '/config/config.php';

require_once __DIR__ . '/includes/admin_auth.php';
// CEK ROLE ADMIN
requireAdmin();


// Function to get all laptops
function getAllLaptops($conn)
{
    $sql = "SELECT * FROM tb_laptop ORDER BY id_laptop DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}



// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_clean(); // Clear any output buffered so far
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'Terjadi kesalahan'];

    try {
        if (!isset($_POST['action'])) {
            throw new Exception("No action specified.");
        }

        switch ($_POST['action']) {
            case 'add_laptop':
                // Validate inputs
                $required_fields = ['brand_name', 'model_name', 'processor', 'operating_system', 'graphics', 'ram', 'screen_size', 'internal_storage', 'category', 'price'];
                foreach ($required_fields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Field '$field' is required.");
                    }
                }

                // Your existing code for adding a laptop...
                $brand_name = sanitize_input($_POST['brand_name']);
                $model_name = sanitize_input($_POST['model_name']);
                $processor = sanitize_input($_POST['processor']);
                $operating_system = sanitize_input($_POST['operating_system']);
                $graphics = sanitize_input($_POST['graphics']);
                $ram = sanitize_input($_POST['ram']);
                $screen_size = sanitize_input($_POST['screen_size']);
                $internal_storage = sanitize_input($_POST['internal_storage']);
                $category = sanitize_input($_POST['category']);
                $price = floatval($_POST['price']);

                $sql = "INSERT INTO tb_laptop (brand_name, model_name, processor, operating_system, graphics, ram, screen_size, internal_storage, category, price) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("sssssssssd", $brand_name, $model_name, $processor, $operating_system, $graphics, $ram, $screen_size, $internal_storage, $category, $price);

                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }

                $new_id = $stmt->insert_id;
                $stmt->close();

                // Handle image upload
                if ($_FILES['image']['error'] == 0) {
                    $image_filename = "laptop_id_" . $new_id . ".webp";
                    $image_path = "img/laptop_img/" . $image_filename;

                    // Convert image to WebP
                    $source_image = $_FILES['image']['tmp_name'];
                    $info = getimagesize($source_image);
                    $mime = $info['mime'];

                    switch ($mime) {
                        case 'image/jpeg':
                            $image = imagecreatefromjpeg($source_image);
                            break;
                        case 'image/png':
                            $image = imagecreatefrompng($source_image);
                            break;
                        case 'image/gif':
                            $image = imagecreatefromgif($source_image);
                            break;
                        default:
                            throw new Exception("Unsupported image format.");
                    }

                    if ($image !== false) {
                        $width = imagesx($image);
                        $height = imagesy($image);
                        $new_image = imagecreatetruecolor($width, $height);
                        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $width, $height);

                        if (!imagewebp($new_image, $image_path, 80)) {
                            throw new Exception("Failed to save the image as WebP.");
                        }

                        imagedestroy($image);
                        imagedestroy($new_image);

                        // Update image_url in database
                        $sql = "UPDATE tb_laptop SET image_url = ? WHERE id_laptop = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("si", $image_path, $new_id);
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to update image URL in database.");
                        }
                        $stmt->close();
                    } else {
                        throw new Exception("Failed to process the uploaded image.");
                    }
                } else {
                    throw new Exception("Image upload failed with error code: " . $_FILES['image']['error']);
                }

                $response = ['status' => 'success', 'message' => 'Laptop berhasil ditambahakan.'];
                break;
            case 'edit_laptop':
                // Validate inputs
                $required_fields = ['id_laptop', 'brand_name', 'model_name', 'processor', 'operating_system', 'graphics', 'ram', 'screen_size', 'internal_storage', 'category', 'price'];
                foreach ($required_fields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Field '$field' is required.");
                    }
                }

                $id_laptop = intval($_POST['id_laptop']);
                $brand_name = sanitize_input($_POST['brand_name']);
                $model_name = sanitize_input($_POST['model_name']);
                $processor = sanitize_input($_POST['processor']);
                $operating_system = sanitize_input($_POST['operating_system']);
                $graphics = sanitize_input($_POST['graphics']);
                $ram = sanitize_input($_POST['ram']);
                $screen_size = sanitize_input($_POST['screen_size']);
                $internal_storage = sanitize_input($_POST['internal_storage']);
                $category = sanitize_input($_POST['category']);
                $price = floatval($_POST['price']);

                $sql = "UPDATE tb_laptop SET brand_name = ?, model_name = ?, processor = ?, operating_system = ?, graphics = ?, ram = ?, screen_size = ?, internal_storage = ?, category = ?, price = ? WHERE id_laptop = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("sssssssssdi", $brand_name, $model_name, $processor, $operating_system, $graphics, $ram, $screen_size, $internal_storage, $category, $price, $id_laptop);

                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }

                $stmt->close();

                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $image_filename = "laptop_id_" . $id_laptop . ".webp";
                    $image_path = "img/laptop_img/" . $image_filename;

                    // Convert image to WebP
                    $source_image = $_FILES['image']['tmp_name'];
                    $info = getimagesize($source_image);
                    $mime = $info['mime'];

                    switch ($mime) {
                        case 'image/jpeg':
                            $image = imagecreatefromjpeg($source_image);
                            break;
                        case 'image/png':
                            $image = imagecreatefrompng($source_image);
                            break;
                        case 'image/gif':
                            $image = imagecreatefromgif($source_image);
                            break;
                        default:
                            throw new Exception("Unsupported image format.");
                    }

                    if ($image !== false) {
                        $width = imagesx($image);
                        $height = imagesy($image);
                        $new_image = imagecreatetruecolor($width, $height);
                        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $width, $height);

                        if (!imagewebp($new_image, $image_path, 80)) {
                            throw new Exception("Failed to save the image as WebP.");
                        }

                        imagedestroy($image);
                        imagedestroy($new_image);

                        // Update image_url in database
                        $sql = "UPDATE tb_laptop SET image_url = ? WHERE id_laptop = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("si", $image_path, $id_laptop);
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to update image URL in database.");
                        }
                        $stmt->close();
                    } else {
                        throw new Exception("Failed to process the uploaded image.");
                    }
                }

                $response = ['status' => 'success', 'message' => 'Laptop berhasil diperbarui.'];
                break;
            case 'delete_laptop':
                // Handle laptop deletion
                $id_laptop = $_POST['id_laptop'];
                $sql = "DELETE FROM tb_laptop WHERE id_laptop = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_laptop);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to delete laptop.");
                }
                $stmt->close();
                $response = ['status' => 'success', 'message' => 'Laptop berhasil dihapus.'];
                break;
            default:
                throw new Exception("Invalid action specified.");
        }
    } catch (Exception $e) {
        error_log("Error in kelola_laptop: " . $e->getMessage());
        $response = ['status' => 'error', 'message' => $e->getMessage()];
    }

    echo json_encode($response);
    exit;
}

// Get all laptops
$laptops = getAllLaptops($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Laptop - Admin</title>
    <!-- Include necessary CSS files -->
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/web-laptop.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

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
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin_dashboard">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Admin Panel</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="admin_dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <li class="nav-item">
                <a class="nav-link" href="kelola_user">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Kelola User</span>
                </a>
            </li>

            <li class="nav-item  active">
                <a class="nav-link" href="kelola_laptop">
                    <i class="fas fa-fw fa-laptop"></i>
                    <span>Kelola Laptop</span>
                </a>
            </li>
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
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </span>
                                <i class="fas fa-user-circle fa-lg" style="color: #228B22;"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
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
                    <h1 class="h3 mb-2 text-gray-800">Kelola Laptop</h1>
                    <p class="mb-4">Manajemen data laptop dalam sistem</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Laptop</h6>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addLaptopModal"> <i
                                    class="fas fa-plus"></i> </button>
                            <div class="table-responsive">
                                <table id="laptopTable" class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Brand</th>
                                            <th>Model</th>

                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Image</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($laptops as $laptop): ?>
                                            <tr>
                                                <td><?php echo $laptop['id_laptop']; ?></td>
                                                <td><?php echo $laptop['brand_name']; ?></td>
                                                <td><?php echo $laptop['model_name']; ?></td>

                                                <td><?php echo $laptop['category']; ?></td>
                                                <td><?php echo $laptop['price']; ?></td>
                                                <td><img src="<?php echo $laptop['image_url']; ?>" alt="Laptop Image"
                                                        style="width: 100px;"></td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm edit-laptop" data-toggle="modal"
                                                        data-target="#editLaptopModal"
                                                        data-laptop='<?php echo htmlspecialchars(json_encode($laptop), ENT_QUOTES, 'UTF-8'); ?>'><i
                                                            class="fas fa-edit"></i></button>
                                                    <button type="button" class="btn btn-danger btn-sm delete-laptop"
                                                        data-id="<?php echo $laptop['id_laptop']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include 'includes/footer.php'; ?>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Add Laptop Modal -->
    <div class="modal fade" id="addLaptopModal" tabindex="-1" role="dialog" aria-labelledby="addLaptopModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLaptopModalLabel">Tambah Laptop</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addLaptopForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_laptop">
                        <div class="form-group">
                            <label for="brand_name">Brand</label>
                            <input type="text" class="form-control" id="brand_name" name="brand_name" required>
                        </div>
                        <div class="form-group">
                            <label for="model_name">Model</label>
                            <input type="text" class="form-control" id="model_name" name="model_name" required>
                        </div>
                        <div class="form-group">
                            <label for="processor">Processor</label>
                            <input type="text" class="form-control" id="processor" name="processor" required>
                        </div>
                        <div class="form-group">
                            <label for="operating_system">Operating System</label>
                            <input type="text" class="form-control" id="operating_system" name="operating_system"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="graphics">Graphics</label>
                            <input type="text" class="form-control" id="graphics" name="graphics" required>
                        </div>
                        <div class="form-group">
                            <label for="ram">RAM</label>
                            <input type="text" class="form-control" id="ram" name="ram" required>
                        </div>
                        <div class="form-group">
                            <label for="screen_size">Screen Size</label>
                            <input type="text" class="form-control" id="screen_size" name="screen_size" required>
                        </div>
                        <div class="form-group">
                            <label for="internal_storage">Internal Storage</label>
                            <input type="text" class="form-control" id="internal_storage" name="internal_storage"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="Student">Student</option>
                                <option value="Designer">Designer</option>
                                <option value="Gamer">Gamer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Price (IDR)</label>
                            <input type="number" class="form-control" id="price" name="price" required min="0"
                                step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" class="form-control-file" id="image" name="image" required
                                accept="image/*">
                        </div>
                        <button type="submit" name="add_laptop" class="btn btn-primary">Tambah Laptop</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Laptop Modal -->
    <div class="modal fade" id="editLaptopModal" tabindex="-1" role="dialog" aria-labelledby="editLaptopModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLaptopModalLabel">Edit Laptop</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editLaptopForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit_laptop">
                        <input type="hidden" id="edit_id_laptop" name="id_laptop">
                        <div class="form-group">
                            <label for="edit_brand_name">Brand</label>
                            <input type="text" class="form-control" id="edit_brand_name" name="brand_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_model_name">Model</label>
                            <input type="text" class="form-control" id="edit_model_name" name="model_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_processor">Processor</label>
                            <input type="text" class="form-control" id="edit_processor" name="processor" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_operating_system">Operating System</label>
                            <input type="text" class="form-control" id="edit_operating_system" name="operating_system"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="edit_graphics">Graphics</label>
                            <input type="text" class="form-control" id="edit_graphics" name="graphics" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_ram">RAM</label>
                            <input type="text" class="form-control" id="edit_ram" name="ram" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_screen_size">Screen Size</label>
                            <input type="text" class="form-control" id="edit_screen_size" name="screen_size" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_internal_storage">Internal Storage</label>
                            <input type="text" class="form-control" id="edit_internal_storage" name="internal_storage"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="edit_category">Category</label>
                            <select class="form-control" id="edit_category" name="category">
                                <option value="Student">Student</option>
                                <option value="Designer">Designer</option>
                                <option value="Gamer">Gamer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_price">Price (IDR)</label>
                            <input type="number" class="form-control" id="edit_price" name="price" required min="0"
                                step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="edit_image">Image</label>
                            <input type="file" class="form-control-file" id="edit_image" name="image" accept="image/*">
                        </div>
                        <button type="submit" name="edit_laptop" class="btn btn-primary">Update Laptop</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include necessary JavaScript files -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="js/web-laptop.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="js/pages/kelola_laptop.js"></script>
    <script src="js/web-laptop.min.js"></script>
    <!-- Konfirmasi Logout -->
    <script src="js/pages/logout.js"></script>
</body>

</html>