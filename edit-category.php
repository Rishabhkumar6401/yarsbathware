<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

// Include database connection file
include_once 'db_connection.php';

// Check if category ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: category-management.php");
    exit();
}

$category_id = $_GET['id'];

// Fetch category details from the database
$sql = "SELECT * FROM categories WHERE category_id = :category_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: category-management.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $category_name = $_POST['category_name'];

    // Check if a new image file is uploaded
    if ($_FILES['category_image']['size'] > 0) {
        // Remove old image file
        if (file_exists("uploads/{$category['category_image']}")) {
            unlink("uploads/{$category['category_image']}");
        }

        // Upload new image file
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['category_image']['name']);
        if (move_uploaded_file($_FILES['category_image']['tmp_name'], $uploadFile)) {
            $category_image = $_FILES['category_image']['name'];
        } else {
            $category_image = $category['category_image']; // Use the existing image if upload fails
        }
    } else {
        $category_image = $category['category_image']; // Use the existing image if no new image is uploaded
    }

    // Update category in the database
    $sql = "UPDATE categories SET category_name = :category_name, category_image = :category_image WHERE category_id = :category_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':category_name' => $category_name,
        ':category_image' => $category_image,
        ':category_id' => $category_id
    ]);

    // Redirect to category management page
    header("Location: category-management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Edit Category</h2>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="category_name">Category Name</label>
                                <input type="text" name="category_name" id="category_name" class="form-control" value="<?php echo $category['category_name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="category_image">Category Image</label>
                                <input type="file" name="category_image" id="category_image" class="form-control-file">
                                <small class="form-text text-muted">Leave blank to keep the existing image.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Category</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
