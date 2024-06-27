<?php
session_start();
$category_name = $category_name_err = $category_image_err = "";
if(!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

// Include database connection file
include_once 'db_connection.php';

// Function to compress an image
function compressImage($inputFilePath, $outputFilePath, $quality = 75) {
    // Get image details
    list($width, $height, $type) = getimagesize($inputFilePath);

    // Create image resource based on image type
    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($inputFilePath);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($inputFilePath);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($inputFilePath);
            break;
        default:
            // Unsupported image type
            return false;
    }

    // Compress the image
    imagejpeg($image, $outputFilePath, $quality);

    // Free up memory
    imagedestroy($image);

    return true;
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate category name
    if(empty(trim($_POST["category_name"]))){
        $category_name_err = "Please enter category name.";
    } else{
        $category_name = trim($_POST["category_name"]);
    }
    
    // Validate category image
    if(empty($_FILES["category_image"]["name"])){
        $category_image_err = "Please select an image.";
    } else{
        // Generate a random 4-digit number
        $random_number = rand(1000, 9999);
        // Get the original file name
        $original_name = $_FILES["category_image"]["name"];
        // Extract file extension
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        // Generate unique file name with prefix
        $category_image = $random_number . '_' . $original_name;

        // Compress the uploaded image
        $target_dir = "uploads/";
        $compressed_file_name = pathinfo($category_image, PATHINFO_FILENAME) . '_compressed.webp';
        $compressed_file_path = $target_dir . $compressed_file_name;
        if (!compressImage($_FILES["category_image"]["tmp_name"], $compressed_file_path)) {
            echo "<script>alert('Error occurred while compressing image')</script>";
            exit; // Exit if compression fails
        }
    }
    
    // Check input errors before inserting into database
    if(empty($category_name_err) && empty($category_image_err)){
        try {
            // Prepare an insert statement
            $sql = "INSERT INTO categories (category_name, category_image) VALUES (:category_name, :category_image)";
             
            // Prepare the SQL statement
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':category_name', $category_name);
            $stmt->bindParam(':category_image', $compressed_file_name);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to dashboard
                header("location: dashboard.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        } catch(PDOException $e) {
            // If execution fails, display error message
            die("Error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Add Category</h2>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group <?php echo (!empty($category_name_err)) ? 'has-error' : ''; ?>">
                                <label>Category Name</label>
                                <input type="text" name="category_name" class="form-control" value="<?php echo $category_name; ?>">
                                <span class="text-danger"><?php echo $category_name_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($category_image_err)) ? 'has-error' : ''; ?>">
                                <label>Category Image</label>
                                <input type="file" name="category_image" class="form-control">
                                <span class="text-danger"><?php echo $category_image_err; ?></span>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Submit">
                                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                            </div>
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
