<?php

// Database connection
$con = mysqli_connect('localhost', 'root', '', 'passport_db');

$firstName = mysqli_real_escape_string($con, $_POST["first"]);
$lastName = mysqli_real_escape_string($con, $_POST["last"]);
$gender = mysqli_real_escape_string($con, $_POST["gender"]);
$mobileNo = mysqli_real_escape_string($con, $_POST["mobile"]);
$email = mysqli_real_escape_string($con, $_POST["email"]);
$password = mysqli_real_escape_string($con, $_POST["pass"]);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['sb'])){
    // Validate input fields
    $errors = [];

    // Validate first name
    if(empty($_POST["first"])) {
        $errors[] = "First name is required";
    }

    // Validate last name
    if(empty($_POST["last"])) {
        $errors[] = "Last name is required";
    }

    // Validate gender
    if(empty($_POST["gender"])) {
        $errors[] = "Gender selection is required";
    }

    // Validate mobile number
    if(empty($_POST["mobile"]) || !preg_match('/^\d{10}$/', $_POST["mobile"])) {
        $errors[] = "Invalid mobile number. Must be 10 digits.";
    }

    // Validate email
    if(empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }

    // Validate password
    if(empty($_POST["pass"])) {
        $errors[] = "Password is required";
    }

    // Validate confirm password
    if(empty($_POST["confirmPassword"])) {
        $errors[] = "Confirm password is required";
    }

    // Check if passwords match
    if($_POST["pass"] !== $_POST["confirmPassword"]) {
        $errors[] = "Passwords do not match";
    }

    // If there are any errors, stop and show them
    if(!empty($errors)) {
        foreach($errors as $error) {
            echo $error . "<br>";
        }
        exit();
    }

    // Sanitize inputs


    // Check if mobile number already exists
    $check_mobile_stmt = $con->prepare("SELECT * FROM mydata WHERE mobileNo = ?");
    $check_mobile_stmt->bind_param("s", $mobileNo);
    $check_mobile_stmt->execute();
    $result = $check_mobile_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "Given mobile number is already registered!! Kindly login. <a href='login.html'>Click here to login</a>";
        exit();
    }
    $check_mobile_stmt->close();

    //check if email id already exist
    $check_email_stmt = $con->prepare("SELECT * FROM mydata WHERE email = ?");
    $check_email_stmt->bind_param("s",  $email);
    $check_email_stmt->execute();
    $result = $check_email_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Given email id is already registered!! Kindly login. <a href='login.html'>Click here to login</a>";
        exit();
    }
    $check_email_stmt->close();

    // Use prepared statement for better security
    $stmt = $con->prepare("INSERT INTO mydata (firstName, lastName, gender, mobileNo, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstName, $lastName, $gender, $mobileNo, $email, $password);
    
    if ($stmt->execute()) {
        echo "Successfully registered! <a href='login.html'>Click here to login</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

mysqli_close($con);
?>