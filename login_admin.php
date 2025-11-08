<?php
// Start session
session_start();

// Database connection
$con = mysqli_connect('localhost', 'root', '', 'passport_db');

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$error_message = "";
$success_message = "";

// Handle form submission
if (isset($_POST['admin_login'])) {
    // Validate input fields
    $errors = [];

    // Validate username
    if (empty($_POST["username"])) {
        $errors[] = "Username is required";
    }

    // Validate password
    if (empty($_POST["password"])) {
        $errors[] = "Password is required";
    }

    // If there are any errors, stop and show them
    if (!empty($errors)) {
        foreach($errors as $error) {
            $error_message .= $error . "<br>";
        }
    } else {
        // Sanitize inputs
        $username = mysqli_real_escape_string($con, $_POST["username"]);
        $password = mysqli_real_escape_string($con, $_POST["password"]);

        // Prepare and execute the query to check admin credentials
        $stmt = $con->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch admin details
            $admin = $result->fetch_assoc();
            
            // Login successful
            $_SESSION['admin_loggedin'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_id'] = $admin['id'];
            
            // Redirect to admin dashboard
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Login failed
            $error_message = "Invalid username or password";
        }
        
        $stmt->close();
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Passport Seva</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            margin: 20px;
        }
        
        .login-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        .login-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .login-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background-color: white;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border: 2px solid #e1e8ed;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #6c757d;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: #667eea;
            background-color: white;
            color: #667eea;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .back-link a:hover {
            color: #667eea;
            text-decoration: none;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }
        
        .admin-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #f39c12;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-user-shield admin-icon"></i>
            <h3>Admin Login</h3>
            <p>Passport Seva Administration</p>
        </div>
        
        <div class="login-body">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="login_admin.php" method="POST">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user mr-2"></i>Username
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="Enter admin username"
                               required
                               autocomplete="username">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Enter admin password"
                               required
                               autocomplete="current-password">
                    </div>
                </div>
                
                <button type="submit" name="admin_login" class="btn btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login as Admin
                </button>
            </form>
            
            <div class="back-link">
                <a href="index.html">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Home
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Simple form validation and UX enhancements
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            
            // Add focus effects
            [usernameInput, passwordInput].forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
            
            // Form submission
            form.addEventListener('submit', function(e) {
                const username = usernameInput.value.trim();
                const password = passwordInput.value;
                
                if (username.length < 3) {
                    e.preventDefault();
                    alert('Username must be at least 3 characters long.');
                    usernameInput.focus();
                    return false;
                }
                
                if (password.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long.');
                    passwordInput.focus();
                    return false;
                }
            });
        });
    </script>
</body>
</html>
