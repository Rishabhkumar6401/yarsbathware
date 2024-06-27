<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

// Include database connection
require 'db_connection.php';

// Check if product ID is provided in the URL
if (isset($_GET['id'])) {
    // Retrieve product ID from URL parameter
    $product_id = $_GET['id'];

    // Check if username and password are provided
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Hardcoded admin credentials
        $admin_username = "admin";
        $admin_password = "123456";

        // Retrieve provided username and password
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check if provided credentials match admin credentials
        if ($username === $admin_username && $password === $admin_password) {
            try {
                // Fetch product image path from the database
                $sql = "SELECT product_image FROM products WHERE product_id = :product_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Delete the product image file from the server
                if ($row && !empty($row['product_image'])) {
                    $image_path = 'uploads/' . $row['product_image'];
                    if (file_exists($image_path)) {
                        unlink($image_path); // Delete the file
                    }
                }

                // Prepare SQL statement to delete product by ID
                $sql = "DELETE FROM products WHERE product_id = :product_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

                // Execute the statement
                if ($stmt->execute()) {
                    // Product deleted successfully, redirect to products page
                    header("Location: products-management.php");
                    exit();
                } else {
                    // If deletion fails, display error message
                    echo "Error: Unable to delete product.";
                }
            } catch (PDOException $e) {
                // If an error occurs, display error message
                echo "Error: " . $e->getMessage();
            }
        } else {
            // If username and password do not match, redirect back to products management page with error message
            header("Location: products-management.php?error=1");
            exit();
        }
    } else {
        // If username and password are not provided, redirect back to products management page with error message
        header("Location: products-management.php?error=2");
        exit();
    }
} else {
    // If product ID is not provided in the URL, redirect to products page or display error message
    echo "Error: Product ID is missing.";
}
?>
