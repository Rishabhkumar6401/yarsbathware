<?php
// Include database connection
require 'db_connection.php';

// Check if category ID is provided in the URL
if(isset($_GET['id'])) {
    // Retrieve category ID from URL parameter
    $category_id = $_GET['id'];

    try {
        // Fetch category image path from the database
        $sql = "SELECT category_image FROM categories WHERE category_id = :category_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete the category image file from the server
        if ($row && !empty($row['category_image'])) {
            $image_path = 'uploads/' . $row['category_image'];
            if (file_exists($image_path)) {
                unlink($image_path); // Delete the file
            }
        }
        
        // Prepare SQL statement to delete category by ID
        $sql = "DELETE FROM categories WHERE category_id = :category_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        
        // Execute the statement
        if($stmt->execute()) {
            // Category deleted successfully, redirect to categories page or display success message
            header("Location: category-management.php");
            exit();
        } else {
            // If deletion fails, display error message
            echo "Error: Unable to delete category.";
        }
    } catch(PDOException $e) {
        // If an error occurs, display error message
        echo "Error: " . $e->getMessage();
    }
} else {
    // If category ID is not provided in the URL, redirect to categories page or display error message
    echo "Error: Category ID is missing.";
}
?>
