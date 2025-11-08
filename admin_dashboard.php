<?php
// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: login_admin.php");
    exit();
}

// Database connection
$con = mysqli_connect('localhost', 'root', '', 'passport_db');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get admin username
$admin_username = $_SESSION['admin_username'];

// Get application statistics
$total_applications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM applications"))['count'];
$pending_applications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM applications WHERE status = 'Pending'"))['count'];
$approved_applications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM applications WHERE status = 'Approved'"))['count'];
$rejected_applications = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM applications WHERE status = 'Rejected'"))['count'];

// Get recent applications
$recent_apps_query = "SELECT id, full_name, email, phone_number, status, submission_date FROM applications ORDER BY submission_date DESC LIMIT 10";
$recent_apps = mysqli_query($con, $recent_apps_query);

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Passport Seva</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .sidebar {
            background: white;
            min-height: calc(100vh - 76px);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: #2c3e50;
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
            color: #495057;
        }
        
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .main-content {
            padding: 30px;
        }
        
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-user-shield mr-2"></i>Admin Dashboard
            </a>
            <div class="navbar-nav ml-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                        <i class="fas fa-user mr-1"></i><?php echo htmlspecialchars($admin_username); ?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i>Profile</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i>Settings</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout_admin.php"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 px-0">
                <div class="sidebar">
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-file-alt mr-2"></i>Applications
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-users mr-2"></i>Users
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-bar mr-2"></i>Reports
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <div class="main-content">
                    <!-- Welcome Section -->
                    <div class="welcome-section">
                        <h2>Welcome back, <?php echo htmlspecialchars($admin_username); ?>!</h2>
                        <p class="mb-0">Here's what's happening with passport applications today.</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stats-card p-4 text-center">
                                <i class="fas fa-file-alt stats-icon text-primary"></i>
                                <h4 class="mt-3"><?php echo $total_applications; ?></h4>
                                <p class="text-muted mb-0">Total Applications</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card p-4 text-center">
                                <i class="fas fa-clock stats-icon text-warning"></i>
                                <h4 class="mt-3"><?php echo $pending_applications; ?></h4>
                                <p class="text-muted mb-0">Pending</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card p-4 text-center">
                                <i class="fas fa-check-circle stats-icon text-success"></i>
                                <h4 class="mt-3"><?php echo $approved_applications; ?></h4>
                                <p class="text-muted mb-0">Approved</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card p-4 text-center">
                                <i class="fas fa-times-circle stats-icon text-danger"></i>
                                <h4 class="mt-3"><?php echo $rejected_applications; ?></h4>
                                <p class="text-muted mb-0">Rejected</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Applications Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-list mr-2"></i>Recent Applications</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($recent_apps) > 0): ?>
                                            <?php while ($app = mysqli_fetch_assoc($recent_apps)): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($app['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($app['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($app['phone_number']); ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php 
                                                            echo $app['status'] == 'Approved' ? 'success' : 
                                                                ($app['status'] == 'Rejected' ? 'danger' : 'warning'); 
                                                        ?>">
                                                            <?php echo htmlspecialchars($app['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M j, Y', strtotime($app['submission_date'])); ?></td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    No applications found
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
