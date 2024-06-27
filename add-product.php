<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

// Include database connection file
include_once 'db_connection.php';

// Define variables and initialize with empty values
$product_name = $product_code = $product_image = $category_id = $best_seller = "";
$product_name_err = $product_code_err = $product_image_err = $category_id_err = "";

// Fetch categories from the database
$sql_categories = "SELECT category_id, category_name FROM categories";
$result_categories = $pdo->query($sql_categories);

$categories = [];
while ($row = $result_categories->fetch(PDO::FETCH_ASSOC)) {
    $categories[] = $row;
}

// Compress image function
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
        case IMAGETYPE_WEBP:
            $image = imagecreatefromwebp($inputFilePath);
            // Save the WebP image without compression
            imagewebp($image, $outputFilePath);
            imagedestroy($image);
            return true;
        default:
            // Unsupported image type
            return false;
    }

    // Compress the image for JPEG, PNG, and GIF
    imagejpeg($image, $outputFilePath, $quality);

    // Free up memory
    imagedestroy($image);

    return true;
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate product name
    if(empty(trim($_POST["product_name"]))){
        $product_name_err = "Please enter product name.";
    } else{
        $product_name = trim($_POST["product_name"]);
    }
    
    // Validate product code
    if(empty(trim($_POST["product_code"]))){
        $product_code_err = "Please enter product code.";
    } else{
        $product_code = trim($_POST["product_code"]);
    }

    // Validate category
    if(empty(trim($_POST["category_id"]))){
        $category_id_err = "Please select a category.";
    } else{
        $category_id = trim($_POST["category_id"]);
    }
    
    // Validate product image
    if(empty($_FILES["product_image"]["name"])){
        $product_image_err = "Please select an image.";
    } else{
        // Generate a random 4-digit number
        $random_number = rand(1000, 9999);
        // Get the original file name
        $original_name = $_FILES["product_image"]["name"];
        // Extract file extension
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        // Generate unique file name with prefix
        $product_image = $random_number . '_' . $original_name;
    }

    // Check if the "Best Seller" checkbox is checked
    $best_seller = isset($_POST['best_seller']) ? 1 : 0;
    
    // Check input errors before inserting into database
    if(empty($product_name_err) && empty($product_code_err) && empty($category_id_err) && empty($product_image_err)){
        try {
            // Prepare an insert statement
            $sql = "INSERT INTO products (category_id, product_name, product_code, product_image, best_seller) VALUES (:category_id, :product_name, :product_code, :product_image, :best_seller)";
             
            // Prepare the SQL statement
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':product_code', $product_code);
            $stmt->bindParam(':product_image', $product_image);
            $stmt->bindParam(':best_seller', $best_seller);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Upload image to server
                $target_dir = "uploads/";
                $target_file = $target_dir . $product_image;
                move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);

                // Compress the image
                $compressed_file = $target_dir . 'compressed_' . $product_image;
                if (compressImage($target_file, $compressed_file)) {
                    // Remove the original uploaded image
                    unlink($target_file);
                    // Rename the compressed image to the original image name
                    rename($compressed_file, $target_file);
                }

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
    <title>Add Product</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Add Product</h2>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group <?php echo (!empty($product_name_err)) ? 'has-error' : ''; ?>">
                                <label>Product Name</label>
                                <input type="text" name="product_name" class="form-control" value="<?php echo $product_name; ?>">
                                <span class="text-danger"><?php echo $product_name_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($product_code_err)) ? 'has-error' : ''; ?>">
                                <label>Product Code</label>
                                <input type="text" name="product_code" class="form-control" value="<?php echo $product_code; ?>">
                                <span class="text-danger"><?php echo $product_code_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($category_id_err)) ? 'has-error' : ''; ?>">
                                <label>Category</label>
                                <select name="category_id" class="form-control">
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="text-danger"><?php echo $category_id_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($product_image_err)) ? 'has-error' : ''; ?>">
                                <label>Product Image</label>
                                <input type="file" name="product_image" class="form-control">
                                <span class="text-danger"><?php echo $product_image_err; ?></span>
                            </div>
                            <div class="form-group">
                                <label>Best Seller</label><br>
                                <input type="checkbox" name="best_seller" value="1">
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
