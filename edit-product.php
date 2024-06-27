<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

// Include database connection file
include_once 'db_connection.php';

// Check if product ID is provided in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details based on the provided ID
    $sql = "SELECT * FROM products WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the product exists
    if (!$product) {
        // If the product does not exist, redirect back to the product management page or show an error message
        header("Location: product-management.php"); // You can change this to the product management page URL
        exit();
    }
} else {
    // If product ID is not provided, redirect back to the product management page or show an error message
    header("Location: product-management.php"); // You can change this to the product management page URL
    exit();
}

// Fetch categories for dropdown
$sql_categories = "SELECT * FROM categories";
$stmt_categories = $pdo->query($sql_categories);
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data

    // Retrieve form data
    $product_name = $_POST['product_name'];
    $product_code = $_POST['product_code'];
    $category_id = $_POST['category_id'];
    $product_image = $_FILES['product_image'];
    $old_image = $product['product_image']; // Store old image filename

    // Check if a new image is uploaded
    if ($product_image['size'] > 0) {
        // Delete old image from the server
        unlink("uploads/" . $old_image);

        // Upload new image
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($product_image['name']);
        move_uploaded_file($product_image['tmp_name'], $target_file);
        $product_image_name = basename($product_image['name']);
    } else {
        // Use the old image if a new image is not uploaded
        $product_image_name = $old_image;
    }

    // Determine the value of the "Best Seller" checkbox
    $best_seller = isset($_POST['best_seller']) ? 1 : 0;

    // Update product details in the database, including the best_seller field
    $sql = "UPDATE products SET product_name = :product_name, product_code = :product_code, category_id = :category_id, product_image = :product_image, best_seller = :best_seller WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
    $stmt->bindParam(':product_code', $product_code, PDO::PARAM_STR);
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_image', $product_image_name, PDO::PARAM_STR);
    $stmt->bindParam(':best_seller', $best_seller, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect to the product management page after updating
    header("Location: products-management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content goes here -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Navigation bar -->
    <!-- Include navigation bar code here -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Edit Product</h2>
                    </div>
                    <div class="card-body">
                        <!-- Product edit form -->
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo $product['product_name']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="product_code" class="form-label">Product Code</label>
                                <input type="text" class="form-control" id="product_code" name="product_code" value="<?php echo $product['product_code']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Product Category</label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>" <?php echo ($product['category_id'] == $category['category_id']) ? 'selected' : ''; ?>><?php echo $category['category_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="product_image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="product_image" name="product_image">
                            </div>
                            <!-- Hidden field to store old image filename -->
                            <input type="hidden" name="old_image" value="<?php echo $product['product_image']; ?>">
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="best_seller" name="best_seller" <?php echo ($product['best_seller'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="best_seller">Best Seller</label>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Update Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <!-- Include Bootstrap JS here -->
</body>
</html>
