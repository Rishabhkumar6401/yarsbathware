<?php
// Include database connection
require 'db_connection.php';

// Check if contact ID is provided in the URL
if(isset($_GET['id'])) {
    // Retrieve contact ID from URL parameter
    $contact_id = $_GET['id'];

    // Retrieve contact data from the database
    $sql = "SELECT * FROM contact_form_data WHERE id = :contact_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
    $stmt->execute();
    $contactData = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // If contact ID is not provided in the URL, redirect or display error message
    echo "Error: Contact ID is missing.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Contact Details</h2>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Name: <?php echo $contactData['name']; ?></h5>
            <h6 class="card-subtitle mb-2 text-muted">Phone: <?php echo $contactData['contact_number']; ?></h6>
            <p class="card-text">Email: <?php echo $contactData['email']; ?></p>
            <p class="card-text">City: <?php echo $contactData['city']; ?></p>
            <p class="card-text">Message: <?php echo $contactData['message']; ?></p>
            <a href="contact-data.php" class="btn btn-primary">Back to Contact Data</a>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
