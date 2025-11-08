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

// Query to fetch approved passport details
$query = "SELECT full_name, date_of_birth, passport_number, issue_date, expiry_date, photograph 
          FROM applications 
          WHERE phone_number = ? AND status = 'Approved'";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $mobile);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $passport = mysqli_fetch_assoc($result);
} else {
    $passport = null;
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Passport - Passport Seva</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS with Real E-Passport Theme -->
    <style>
        /* E-Passport Theme Variables */
        :root {
            --passport-blue: #003087; /* Deep blue for official passport color */
            --passport-gold: #d4af37; /* Gold for accents */
            --background-light: #f5f5f5; /* Light page background */
            --text-dark: #1a1a1a; /* Dark text for readability */
            --passport-bg: #e6e6e6; /* Light gray passport background */
            --border-color: #000; /* Black for borders */
        }

        /* Header and Footer */
        header, footer {
            background-color: var(--passport-blue);
            color: var(--passport-gold);
            padding: 1rem 0;
            text-align: center;
        }

        /* Sidebar */
        .sidebar {
            background-color: var(--background-light);
            min-height: 100vh;
            padding: 1rem;
        }

        .sidebar a {
            color: var(--passport-blue);
            text-decoration: none;
            padding: 0.5rem 1rem;
            display: block;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: var(--passport-gold);
            color: var(--passport-blue);
        }

        /* Main Content */
        main {
            padding: 2rem;
            background-color: var(--background-light);
        }

        /* Passport Card */
        .passport-card {
            background-color: var(--passport-bg);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
            position: relative;
            max-width: 550px; /* Wider to mimic a real passport page */
            margin: 0 auto;
            font-family: 'Courier New', Courier, monospace; /* Common passport font */
        }

        /* Passport Header */
        .passport-header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: var(--passport-blue);
            text-transform: uppercase;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 0.5rem;
        }

        /* Passport Content */
        .passport-content {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        /* Passport Photo */
        .passport-photo {
            border: 2px solid var(--border-color);
            border-radius: 5px;
            width: 150px; /* Standard passport photo size */
            height: 160px;
            object-fit: cover;
            margin-right: 1rem;
        }

        /* Passport Details */
        .passport-details {
            flex-grow: 1;
            margin-left: 90px;
           
        }

        .passport-details .row {
            margin-bottom: 0.5rem;
            display: flex;
           
        }

        .passport-details label {
            text-transform: uppercase;
            font-weight: bold;
            color: var(--passport-blue);
            font-size: 14px;
            width: 150px; /* Fixed width for labels to align like a real passport */
        }

        .passport-details span {
            color: var(--text-dark);
            font-size: 14px;
            font-family: 'Courier New', Courier, monospace;
        }

        /* QR Code */
        .passport-qr {
            width: 80px;
            height: 80px;
            border: 1px solid var(--border-color);
            margin-left: 1rem;
            top: 50px;
        }

        /* Alert Styling */
        .alert {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        /* Button Styling */
        .btn-primary {
            background-color: var(--passport-blue);
            border: none;
            color: var(--passport-gold);
            padding: 0.5rem 1rem;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--passport-gold);
            color: var(--passport-blue);
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo fs-4 fw-bold">Passport Seva</div>
            <nav>
                <a href="#" class="text-gold mx-2">Home</a>
                <a href="#" class="text-gold mx-2">About Us</a>
                <a href="#" class="text-gold mx-2">Contact Us</a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="sidebar col-md-3">
                <ul class="list-unstyled">
                    <li><a href="#register"><i class="fas fa-user me-2"></i> Register</a></li>
                    <li><a href="#apply"><i class="fas fa-file-alt me-2"></i> Fill Application</a></li>
                    <li><a href="#upload"><i class="fas fa-upload me-2"></i> Upload Documents</a></li>
                    <li><a href="#schedule"><i class="fas fa-calendar-alt me-2"></i> Schedule Appointment</a></li>
                    <li><a href="#pay"><i class="fas fa-credit-card me-2"></i> Make Payment</a></li>
                    <li><a href="view_passport.php"><i class="fas fa-passport me-2"></i> View Passport</a></li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9">
                <?php if ($passport): ?>
                    <div class="passport-card">
                        <div class="passport-header">E-Passport</div>
                        <div class="passport-content">
                            <div>
                                <img src="<?php echo htmlspecialchars($passport['photograph']); ?>" alt="Passport Photo" class="passport-photo img-fluid">
                            </div>
                            <div class="passport-details">
                                <div class="row">
                                    <label>Passport No :</label>
                                    <span><?php echo htmlspecialchars($passport['passport_number']); ?></span>
                                </div>
                                <div class="row">
                                    <label>Name</label>
                                    <span><?php echo htmlspecialchars($passport['full_name']); ?></span>
                                </div>
                                <div class="row">
                                    <label>Date of Birth</label>
                                    <span><?php echo htmlspecialchars($passport['date_of_birth']); ?></span>
                                </div>
                                <div class="row">
                                    <label>Issue Date</label>
                                    <span><?php echo htmlspecialchars($passport['issue_date']); ?></span>
                                </div>
                                <div class="row">
                                    <label>Expiry Date</label>
                                    <span><?php echo htmlspecialchars($passport['expiry_date']); ?></span>
                                </div>
                            </div>
                            <div>
                                <img src="qr.png" alt="QR Code" class="passport-qr">
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert" role="alert">
                        No passport found. Please check your application status.
                        <br>
                        <a href="application_status.php" class="alert-link">View Application Status</a>
                    </div>
                <?php endif; ?>
                <div class="text-center mt-3">
                    <a href="index_loggin.php" class="btn btn-primary">Back to Home</a>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p>© 2023 Passport Seva</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="#" class="text-gold">Privacy Policy</a></li>
                <li class="list-inline-item"><a href="#" class="text-gold">Terms of Use</a></li>
                <li class="list-inline-item"><a href="#" class="text-gold">Contact Us</a></li>
            </ul>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>