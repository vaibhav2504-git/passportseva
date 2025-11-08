<?php
// Database connection
$con = mysqli_connect('localhost', 'root', '', 'passport_db');

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['sb'])){
    // Validate input fields
    $errors = [];

    // Validate mobile number
    if(empty($_POST["mobile"]) || !preg_match('/^\d{10}$/', $_POST["mobile"])) {
        $errors[] = "Invalid Mobile Number. Must be 10 digits.";
    }

    // Validate password
    if(empty($_POST["pass"])) {
        $errors[] = "Password is required";
    }

    // If there are any errors, stop and show them
    if(!empty($errors)) {
        foreach($errors as $error) {
            echo $error . "<br>";
        }
        exit();
    }

    // Sanitize inputs
    $mobileNo = mysqli_real_escape_string($con, $_POST["mobile"]);
    $password = mysqli_real_escape_string($con, $_POST["pass"]);

    // Prepare and execute the query to check credentials
    $stmt = $con->prepare("SELECT * FROM mydata WHERE mobileNo = ? AND password = ?");
    $stmt->bind_param("ss", $mobileNo, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user details
        $user = $result->fetch_assoc();
        
        // Login successful
        // Start a session or set a cookie to maintain login state
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['mobile'] = $mobileNo;
        
        // Prepare user details for client-side storage
        $userDetails = [
            'mobile' => $mobileNo,
            'name' => $user['name'] ?? 'User', // Assuming there's a name column
            'email' => $user['email'] ?? ''
        ];
        
        // Output a script to set localStorage with mobile number
        echo "<script>
            localStorage.setItem('userName', '" . addslashes($userDetails['name']) . "');
            localStorage.setItem('userMobile', '" . addslashes($mobileNo) . "');
            localStorage.setItem('userEmail', '" . addslashes($userDetails['email']) . "');
            window.location.href = 'index_loggin.php';
        </script>";
        exit();
    } else {
        // Login failed
        echo "<div style='color: red; text-align: center; margin-top: 20px;'>Invalid mobile number or password</div>";
    }
    
    $stmt->close();
}

mysqli_close($con);
?>