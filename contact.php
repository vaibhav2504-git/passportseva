<?php

// Database connection
$con = mysqli_connect('localhost', 'root', '', 'passport_db');

$name = mysqli_real_escape_string($con, $_POST["name"]);
$email = mysqli_real_escape_string($con, $_POST["email"]);

$message = mysqli_real_escape_string($con, $_POST["message"]);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['sb'])){
    $errors = [];
    // Use prepared statement for better security
    $stmt = $con->prepare("INSERT INTO contact (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email,$message);
    
    if ($stmt->execute()) {
        echo '<p style="color:red;">Thank you for your message! we will get back to you shortly. redirecting...</p>';
        header("refresh:2;url=index_loggin.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

mysqli_close($con);
?>