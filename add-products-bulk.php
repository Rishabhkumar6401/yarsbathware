<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

// Include database connection file
include_once 'db_connection.php';

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
    $product_data = [];
    foreach ($_POST['products'] as $i => $product) {
        // Validate product name
        $product_name = trim($product['product_name']);
        $product_code = trim($product['product_code']);
        $category_id = trim($product['category_id']);
        $best_seller = isset($product['best_seller']) ? 1 : 0;

        // Handle product image upload
        $product_image = '';
        if ($_FILES['products']['size'][$i]['product_image'] > 0) {
            $target_dir = "uploads/";
            $file_extension = pathinfo($_FILES['products']['name'][$i]['product_image'], PATHINFO_EXTENSION);
            $random_number = rand(1000, 9999);
            $product_image = $random_number . '_' . basename($_FILES['products']['name'][$i]['product_image']);
            $target_file = $target_dir . $product_image;
            move_uploaded_file($_FILES['products']['tmp_name'][$i]['product_image'], $target_file);

            // Compress the image
            $compressed_file = $target_dir . 'compressed_' . $product_image;
            if (compressImage($target_file, $compressed_file)) {
                // Remove the original uploaded image
                unlink($target_file);
                // Rename the compressed image to the original image name
                rename($compressed_file, $target_file);
            }
        }

        // Insert product data into the database
        $sql = "INSERT INTO products (category_id, product_name, product_code, best_seller, product_image) 
                VALUES (:category_id, :product_name, :product_code, :best_seller, :product_image)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':product_code', $product_code);
        $stmt->bindParam(':best_seller', $best_seller);
        $stmt->bindParam(':product_image', $product_image);
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
    <style>
        .remove-button {
            margin-top: 30px;
        }
    </style>
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
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" id="product-form">
                            <div id="product-forms-container">
                                <div class="product-form">
                                    <div class="mb-3">
                                        <label for="product_name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="product_name" name="products[0][product_name]">
                                    </div>
                                    <div class="mb-3">
                                        <label for="product_code" class="form-label">Product Code</label>
                                        <input type="text" class="form-control" id="product_code" name="products[0][product_code]">
                                    </div>
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-control" id="category_id" name="products[0][category_id]">
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category) : ?>
                                                <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="best_seller" name="products[0][best_seller]">
                                        <label class="form-check-label" for="best_seller">Best Seller</label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="product_image" class="form-label">Product Image</label>
                                        <input type="file" class="form-control" id="product_image" name="products[0][product_image]">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mb-3">
                                <button type="button" class="btn btn-secondary" id="add-product-button">Add Another Product</button>
                            </div>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        let productIndex = 1;

        document.getElementById('add-product-button').addEventListener('click', function () {
            const productForm = document.querySelector('.product-form');
            const newProductForm = productForm.cloneNode(true);
            const inputs = newProductForm.querySelectorAll('input, select');

            inputs.forEach(input => {
                const name = input.name.replace(/\d+/, productIndex);
                input.name = name;
                input.id = name;
                if (input.type !== 'checkbox' && input.type !== 'file') {
                    input.value = '';
                } else if (input.type === 'checkbox') {
                    input.checked = false;
                }
            });

            newProductForm.querySelector('.form-check-label').setAttribute('for', 'best_seller_' + productIndex);
            newProductForm.querySelector('.form-check-input').id = 'best_seller_' + productIndex;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.classList.add('btn', 'btn-danger', 'remove-button');
            removeButton.textContent = 'Remove Product';
            removeButton.addEventListener('click', function () {
                this.closest('.product-form').remove();
            });

            newProductForm.appendChild(removeButton);

            document.getElementById('product-forms-container').appendChild(newProductForm);
            productIndex++;
        });
    </script>
</body>

</html>
