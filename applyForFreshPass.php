<?php
// Start the session
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

// Query to check for pending applications
// Corrected query to ensure proper operator precedence with parentheses
$query = "SELECT COUNT(*) as count FROM applications WHERE phone_number = ? AND (status = 'Pending'";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $mobile);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$pending_count = $row['count'];

// Close statement and connection
mysqli_stmt_close($stmt);
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Fresh Passport</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --dark-blue: #003366;
            --orange: #ff6600;
            --light-orange: #cc5200;
            --white: #ffffff;
            --light-gray: #f8f9fa;
        }
        header, footer {
            background-color: var(--dark-blue);
            color: var(--white);
        }
        .sidebar {
            background-color: var(--light-gray);
            min-height: 100vh;
        }
        .sidebar a {
            color: var(--dark-blue);
            text-decoration: none;
            padding: 0.5rem 1rem;
            display: block;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: var(--white);
            color: var(--orange);
        }
        .btn-primary {
            background-color: var(--orange);
            border: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn-primary:hover {
            background-color: var(--light-orange);
            transform: scale(1.05);
        }
        section {
            padding: 2rem 0;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo fs-4 fw-bold">Passport Seva</div>
            <nav>
                <a href="#" class="text-white mx-2">Home</a>
                <a href="#" class="text-white mx-2">About Us</a>
                <a href="#" class="text-white mx-2">Contact Us</a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="sidebar col-md-3 py-3">
                <ul class="list-unstyled">
                    <li><a href="#register"><i class="fas fa-user me-2"></i> Register</a></li>
                    <li><a href="#apply"><i class="fas fa-file-alt me-2"></i> Fill Application</a></li>
                    <li><a href="#upload"><i class="fas fa-upload me-2"></i> Upload Documents</a></li>
                    <li><a href="#schedule"><i class="fas fa-calendar-alt me-2"></i> Schedule Appointment</a></li>
                    <li><a href="#pay"><i class="fas fa-credit-card me-2"></i> Make Payment</a></li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 py-5">
                <!-- Hero Section -->
                <section class="text-center mb-5">
                    <h1>Apply for a Fresh Passport</h1>
                    <p>Start your passport application process online with ease.</p>
                    <a href="#apply" class="btn btn-primary">Get Started</a>
                </section>

                <!-- Conditional Content -->
                <?php if ($pending_count > 0): ?>
                    <div class="alert alert-warning" role="alert">
                        Your previous application is already pending. You cannot submit another application at this time.
                        <br>
                        <a href="application_status.php" class="alert-link">Check your application status</a>
                    </div>
                <?php else: ?>
                    <!-- Application Form Section -->
                    <div class="card shadow" id="apply">
                        <div class="card-header">
                            <h2>Application Form</h2>
                        </div>
                        <div class="card-body">
                            <form action="submit.php" method="post" enctype="multipart/form-data">
                                <h3>Personal Information</h3>
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name:</label>
                                    <input type="text" id="full_name" name="full_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth:</label>
                                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Gender:</label>
                                    <div class="form-check">
                                        <input type="radio" name="gender" value="Male" class="form-check-input" required>
                                        <label class="form-check-label">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="gender" value="Female" class="form-check-input">
                                        <label class="form-check-label">Female</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="gender" value="Other" class="form-check-input">
                                        <label class="form-check-label">Other</label>
                                    </div>
                                </div>
                                <hr>
                                <h3>Address Information</h3>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address:</label>
                                    <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="city" class="form-label">City:</label>
                                    <input type="text" id="city" name="city" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="state" class="form-label">State:</label>
                                    <input type="text" id="state" name="state" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="pin_code" class="form-label">Pin Code:</label>
                                    <input type="text" id="pin_code" name="pin_code" class="form-control" required>
                                </div>
                                <hr>
                                <h3>Contact Information</h3>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" id="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number:</label>
                                    <input type="tel" id="phone_number" name="phone_number" class="form-control" required>
                                </div>
                                <hr>
                                <h3>Identity Information</h3>
                                <div class="mb-3">
                                    <label for="aadhaar_number" class="form-label">Aadhaar Number:</label>
                                    <input type="text" id="aadhaar_number" name="aadhaar_number" class="form-control" required>
                                </div>
                                <hr>
                                <h3>Document Uploads</h3>
                                <div class="mb-3">
                                    <label for="identity_proof" class="form-label">Upload Identity Proof (PDF, JPG, PNG):</label>
                                    <input type="file" id="identity_proof" name="identity_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address_proof" class="form-label">Upload Address Proof (PDF, JPG, PNG):</label>
                                    <input type="file" id="address_proof" name="address_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                                <div class="mb-3">
                                    <label for="photograph" class="form-label">Upload Photograph (JPG, PNG):</label>
                                    <input type="file" id="photograph" name="photograph" class="form-control" accept=".jpg,.jpeg,.png" required>
                                </div>
                                <div class="mb-3" style="display: none;">
                                    <label for="status" class="form-label">Application Status</label>
                                    <input type="text" id="status" value="Pending" name="status" class="form-control" required>
                                </div>
                                <hr>
                                <input type="submit" value="Submit Application" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-3">
        <div class="container text-center">
            <p>&copy; 2023 Passport Seva</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="#" class="text-white">Privacy Policy</a></li>
                <li class="list-inline-item"><a href="#" class="text-white">Terms of Use</a></li>
                <li class="list-inline-item"><a href="#" class="text-white">Contact Us</a></li>
            </ul>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>