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

// Retrieve user's mobile number from session
$mobile = $_SESSION['mobile'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $date_of_birth = $_POST['date_of_birth'];
    $address = $_POST['address'];
    $id = $_POST['id'];

    // Handle photograph upload
    $photograph_path = null;
    if (isset($_FILES['photograph']) && $_FILES['photograph']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES['photograph']['name'], PATHINFO_EXTENSION);
        $photograph_path = $upload_dir . "photo_" . $id . "_" . time() . "." . $file_ext;
        move_uploaded_file($_FILES['photograph']['tmp_name'], $photograph_path);
    }

    // Prepare and execute update query
    if ($photograph_path) {
        $update_query = "UPDATE applications SET full_name = ?, email = ?, date_of_birth = ?, address = ?, photograph = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "sssssi", $full_name, $email, $date_of_birth, $address, $photograph_path, $id);
    } else {
        $update_query = "UPDATE applications SET full_name = ?, email = ?, date_of_birth = ?, address = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $date_of_birth, $address, $id);
    }
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Fetch user details
$query = "SELECT id, full_name, email, date_of_birth, phone_number, aadhaar_number, status, address, photograph FROM applications WHERE phone_number = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $_SESSION['mobile']);
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
    <title>Edit Passport Application - Passport Seva</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        .edit-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .form-group label {
            font-weight: bold;
            color: #333;
        }
        .form-group input[disabled], .form-group textarea[disabled] {
            background-color: #e9ecef;
        }
        .photo-preview {
            max-width: 150px;
            max-height: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-container">
            <h2 class="text-center">Edit Passport Application</h2>
            <?php if ($user): ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Photograph</label><br>
                        <?php if (!empty($user['photograph']) && file_exists($user['photograph'])): ?>
                            <img src="<?php echo htmlspecialchars($user['photograph']); ?>" alt="Photograph" class="photo-preview mb-2"><br>
                        <?php endif; ?>
                        <input type="file" name="photograph" accept="image/*" class="form-control-file">
                        <small class="form-text text-muted">Upload a new photograph to replace the existing one.</small>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user['phone_number']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Aadhaar Number</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['aadhaar_number']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Application Status</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['status']); ?>" disabled>
                    </div>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <?php if (isset($success) && $success): ?>
                            <p class="text-center text-success">Data is updated successfully.</p>
                        <?php endif; ?>
                    </div>
                </form>
            <?php else: ?>
                <p class="text-center text-danger">No application found for this user.</p>
            <?php endif; ?>
            <div class="text-center mt-3">
                <a href="index_loggin.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>