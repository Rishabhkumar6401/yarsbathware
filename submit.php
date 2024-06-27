<?php
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';
// Include database connection
require 'db_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contactNumber = $_POST['phone']; // Corrected input name
    $city = $_POST['city']; // Corrected input name
    $message = $_POST['Message']; // Corrected input name

    // Insert data into database
    $sql = "INSERT INTO contact_form_data (name, email, contact_number, city, message) 
            VALUES (:name, :email, :contact_number, :city, :message)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':contact_number', $contactNumber, PDO::PARAM_STR);
    $stmt->bindParam(':city', $city, PDO::PARAM_STR);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->execute();

    // Close the database connection
    $conn = null;
    
       // Send an email with the form data 
    $mail = new PHPMailer(true);
    // print_r($mail);




        
    try {
                                      
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server address
    $mail->SMTPAuth = true;
    $mail->Username = 'gozoomtechnologies@gmail.com'; // Replace with your SMTP username
    $mail->Password = 'frdbvawsokeqkqoo'; // Replace with your SMTP password
    $mail->SMTPSecure = 'ssl'; // Enable TLS encryption, 'ssl' also possible
    $mail->Port = 465 ;

//     Recipients
    $mail->setFrom('gozoomtechnologies@gmail.com',$name);
    $mail->addAddress('yarsbathware@gmail.com'); // Replace with the desired email address

//     Email content
    $mail->isHTML(true);
    $mail->Subject = 'Contact Deltails';
    $mail->Body = "Name: $name\n"
        . "Email: $email\n"
        . "Contact Number: $contactNumber\n"
        . "City: $city\n"
        . "Message: $message\n";
   


    $mail->send();
   

//     Email sent successfully
    header("Location: success.html");
    exit;
} 
catch (Exception $e) {
    // Error sending email
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    exit;
}
}
?>

