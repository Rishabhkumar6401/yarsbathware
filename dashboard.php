<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

// Check if logout button is clicked
if(isset($_POST['logout'])) {
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session.
    session_destroy();

    // Redirect to the login page
    header("Location: admin-login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Admin Dashboard</h2>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <a href="category-management.php" class="btn btn-primary">Category Management</a>
                        </div>
                        <div class="text-center mb-3">
                            <a href="products-management.php"class="btn btn-primary">Products Management</a>
                        </div>
                        <div class="text-center mb-3">
                            <a href="contact-data.php" class="btn btn-primary">Contact Data</a>
                        </div>
                        <div class="text-center mb-3">
                            <a href="index.php" class="btn btn-primary">Go to Homepage</a>
                        </div>
                        <form method="post" class="text-center mb-3">
                            <button type="submit" name="logout" class="btn btn-danger">Logout</button>
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
