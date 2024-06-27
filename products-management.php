<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

// Include database connection file
include_once 'db_connection.php';

// Fetch all products with their category information from the database
$sql = "SELECT p.*, c.category_name FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        ORDER BY c.category_name, p.product_name";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group products by category
$categories = [];
foreach ($products as $product) {
    $categories[$product['category_name']][] = $product;
}

// Check for error messages from delete-product.php
$error = isset($_GET['error']) ? $_GET['error'] : null;

// Function to display alert messages
function displayAlert($message)
{
    echo "<script>alert('$message');</script>";
}

// Display appropriate alert messages based on the error code
if ($error == 1) {
    displayAlert("Admin username or password is incorrect.");
} elseif ($error == 2) {
    displayAlert("Admin username and password are required.");
} elseif ($error == 3) {
    displayAlert("An error occurred while deleting the product.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Products Management</h2>
                </div>
                <div class="card-body">
                    <div class="text-left mb-3">
                        <a href="add-product.php" class="btn btn-primary">Add New Product</a>
                        <a href="add-products-bulk.php" class="btn btn-primary ms-2">Add Products in Bulk</a>
                    </div>
                    <!-- List of products by category -->
                    <?php foreach ($categories as $category_name => $category_products): ?>
                        <h3 class="mt-4"><?php echo $category_name; ?></h3>
                        <ul class="list-group mb-4">
                            <?php foreach ($category_products as $product): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $product['product_name']; ?>
                                    <div>
                                        <a href="edit-product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-info me-2">Edit</a>
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $product['product_id']; ?>">
                                            Delete
                                        </button>
                                    </div>
                                </li>
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal<?php echo $product['product_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="delete-product.php?id=<?php echo $product['product_id']; ?>" method="post">
                                                    <div class="form-group">
                                                        <label for="adminUsername">Admin Username</label>
                                                        <input type="text" class="form-control" id="adminUsername" name="username" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="adminPassword">Admin Password</label>
                                                        <input type="password" class="form-control" id="adminPassword" name="password" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-danger">Confirm Deletion</button>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                    <div class="text-center mb-3">
                        <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
