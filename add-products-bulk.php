<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

// Include database connection file
include_once 'db_connection.php';

// Define variables and initialize with empty values
$product_data = array_fill(0, 5, array('product_name' => '', 'product_code' => '', 'category_id' => '', 'best_seller' => '', 'product_image' => ''));
$product_name_err = $product_code_err = $category_id_err = '';

// Fetch categories from the database
$sql_categories = "SELECT category_id, category_name FROM categories";
$result_categories = $pdo->query($sql_categories);
$categories = $result_categories->fetchAll(PDO::FETCH_ASSOC);

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

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and insert each product
    for ($i = 0; $i < 5; $i++) {
        // Validate product name
        if (empty(trim($_POST["product_name_$i"]))) {
            $product_name_err = "Please enter product name.";
        } else {
            $product_data[$i]['product_name'] = trim($_POST["product_name_$i"]);
        }

        // Validate product code
        if (empty(trim($_POST["product_code_$i"]))) {
            $product_code_err = "Please enter product code.";
        } else {
            $product_data[$i]['product_code'] = trim($_POST["product_code_$i"]);
        }

        // Validate category
        if (empty(trim($_POST["category_id_$i"]))) {
            $category_id_err = "Please select a category.";
        } else {
            $product_data[$i]['category_id'] = trim($_POST["category_id_$i"]);
        }

        // Handle best seller checkbox
        $product_data[$i]['best_seller'] = isset($_POST["best_seller_$i"]) ? 1 : 0;

        // Handle product image upload
        if ($_FILES["product_image_$i"]["size"] > 0) {
            $target_dir = "uploads/";
            $file_extension = pathinfo($_FILES["product_image_$i"]["name"], PATHINFO_EXTENSION);
            $random_number = rand(1000, 9999);
            $product_data[$i]['product_image'] = $random_number . '_' . basename($_FILES["product_image_$i"]["name"]);
            $target_file = $target_dir . $product_data[$i]['product_image'];
            move_uploaded_file($_FILES["product_image_$i"]["tmp_name"], $target_file);

            // Compress the image
            $compressed_file = $target_dir . 'compressed_' . $product_data[$i]['product_image'];
            if (compressImage($target_file, $compressed_file)) {
                // Remove the original uploaded image
                unlink($target_file);
                // Rename the compressed image to the original image name
                rename($compressed_file, $target_file);
            }
        }
    }

    // Insert product data into the database
    foreach ($product_data as $product) {
        // Prepare an insert statement
        $sql = "INSERT INTO products (category_id, product_name, product_code, best_seller, product_image) VALUES (:category_id, :product_name, :product_code, :best_seller, :product_image)";

        // Prepare the SQL statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':category_id', $product['category_id']);
        $stmt->bindParam(':product_name', $product['product_name']);
        $stmt->bindParam(':product_code', $product['product_code']);
        $stmt->bindParam(':best_seller', $product['best_seller']);
        $stmt->bindParam(':product_image', $product['product_image']);

        // Attempt to execute the prepared statement
        $stmt->execute();
    }

    header("location: products-management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Add Products</title>
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
                        <h2 class="text-center">Bulk Add Products</h2>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                            enctype="multipart/form-data">
                            <?php for ($i = 0; $i < 5; $i++) : ?>
                            <div class="mb-3">
                                <label for="product_name_<?php echo $i; ?>"
                                    class="form-label">Product Name <?php echo $i + 1; ?></label>
                                <input type="text" class="form-control" id="product_name_<?php echo $i; ?>"
                                    name="product_name_<?php echo $i; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="product_code_<?php echo $i; ?>"
                                    class="form-label">Product Code <?php echo $i + 1; ?></label>
                                <input type="text" class="form-control" id="product_code_<?php echo $i; ?>"
                                    name="product_code_<?php echo $i; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="category_id_<?php echo $i; ?>"
                                    class="form-label">Category <?php echo $i + 1; ?></label>
                                <select class="form-control" id="category_id_<?php echo $i; ?>"
                                    name="category_id_<?php echo $i; ?>">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category) : ?>
                                    <option
                                        value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input"
                                    id="best_seller_<?php echo $i; ?>" name="best_seller_<?php echo $i; ?>">
                                <label class="form-check-label"
                                    for="best_seller_<?php echo $i; ?>">Best Seller</label>
                            </div>
                            <div class="mb-3">
                                <label for="product_image_<?php echo $i; ?>"
                                    class="form-label">Product Image <?php echo $i + 1; ?></label>
                                <input type="file" class="form-control" id="product_image_<?php echo $i; ?>"
                                    name="product_image_<?php echo $i; ?>">
                            </div>
                            <?php endfor; ?>
                            <div class="text-center mb-3">
                                <button type="submit" class="btn btn-primary">Add Products</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
