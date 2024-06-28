<?php
// Include database connection
require 'db_connection.php';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM contact_form_data WHERE id = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt_delete->execute();

    // Redirect to the same page to refresh the data
    header("Location: contact-data.php");
    exit();
}

// Retrieve contact form data from the database
$sql = "SELECT id, name, contact_number, created_at FROM contact_form_data ORDER BY id DESC";
$stmt = $pdo->query($sql);
$contactData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Data</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Contact Data</h2>
    <ul class="list-group mt-3">
        <?php foreach ($contactData as $data): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>
                        <span>Name: <?php echo $data['name']; ?></span>
                        <span class="ml-3">Phone: <?php echo $data['contact_number']; ?></span>
                        <span class="ml-3">Date: <?php echo $data['created_at']; ?></span>
                    </strong>
                </div>
                <div>
                    <a href="view-contact.php?id=<?php echo $data['id']; ?>" class="btn btn-primary">View</a>
                    <a href="contact-data.php?delete_id=<?php echo $data['id']; ?>" class="btn btn-danger"
                       onclick="return confirm('Are you sure you want to delete this contact?');">Delete</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="text-center mt-3 mb-3">
        <a href="dashboard.php" class="btn btn-primary">Back</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
