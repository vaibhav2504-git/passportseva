<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}

// Database connection
$con = mysqli_connect('localhost', 'root', '', 'passport_db');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user details and application status
$mobile = $_SESSION['mobile'];
$query = "SELECT full_name, email, phone_number, aadhaar_number , status, id FROM applications WHERE phone_number = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $mobile);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    $user = null;
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status - Passport Seva</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        .status-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .status-info {
            margin-bottom: 15px;
        }
        .status-info label {
            font-weight: bold;
            color: #333;
        }
        .status-info span {
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-container">
            <h2 class="text-center">Application Status</h2>
            <?php if ($user): ?>

                <div class="status-info">
                    <label>Id:</label>
                    <span><?php echo htmlspecialchars($user['id']); ?></span>
                </div>
                <div class="status-info">
                    <label>Full Name:</label>
                    <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                </div>
                <div class="status-info">
                    <label>Email:</label>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="status-info">
                    <label>Phone:</label>
                    <span><?php echo htmlspecialchars($user['phone_number']); ?></span>
                </div>
                <div class="status-info">
                    <label>Aadhaar Number:</label>
                    <span><?php echo htmlspecialchars($user['aadhaar_number']); ?></span>
                </div>
                <div class="status-info">
                    <label>Application Status:</label>
                    <span><?php echo htmlspecialchars($user['status']); ?></span>
                </div>
    
            <?php else: ?>
                <p class="text-center text-danger">No application found for this user.</p>
            <?php endif; ?>
            <div class="text-center mt-3">
                <a href="index_loggin.php" class="btn btn-primary">Back to Home</a>
            </div>
        </divc>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>