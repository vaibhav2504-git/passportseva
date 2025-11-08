<?php
session_start();

// Check if user is logged in and session is valid
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Check session age and automatically logout if inactive for too long
if ($isLoggedIn) {
    $maxInactiveTime = 8000; // 30 minutes
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $maxInactiveTime)) {
        // Destroy session
        session_unset();
        session_destroy();
        $isLoggedIn = false;
        
        // Redirect to login page
        header("Location: login.html");
        exit();
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passport Seva</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>

.user-profile {
    position: relative;
    cursor: pointer;
}

.user-profile-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6a11cb, #2575fc); /* Gradient */
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.user-profile-icon:hover {
    transform: scale(1.1); /* Smooth scale on hover */
}

.user-logo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff9a9e, #fad0c4); /* New gradient */
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    cursor: pointer;
}

.user-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    color: white;
    background: linear-gradient(135deg, #667eea, #764ba2); /* Gradient for background */
    min-width: 350px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
    border-radius: 12px; /* Smoother corners */
    padding: 15px;
    z-index: 1000;
    transition: all 0.3s ease;
    opacity: 0;
    visibility: hidden;
}

.user-dropdown.show {
    display: block;
    opacity: 1;
    visibility: visible;
}

.user-dropdown-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding-bottom: 10px;
}

.user-dropdown-header .user-profile-icon {
    margin-right: 15px;
    width: 50px;
    height: 50px;
}

.user-dropdown-header-info {
    flex-grow: 1;
}

.user-dropdown-header-info h3 {
    margin: 0;
    font-size: 18px; /* Slightly larger font */
    font-weight: bold;
}

.user-dropdown-header-info p {
    margin: 5px 0 0;
    color: rgba(255, 255, 255, 0.8); /* Softer white */
    font-size: 14px;
}

.user-dropdown-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.user-dropdown-menu li {
    padding: 10px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: background-color 0.3s ease;
}

.user-dropdown-menu li:last-child {
    border-bottom: none;
}

.user-dropdown-menu li a {
    text-decoration: none;
    color: white;
    display: flex;
    align-items: center;
    transition: color 0.3s ease;
}

.user-dropdown-menu li:hover {
    background-color: rgba(255, 255, 255, 0.1); /* Subtle hover effect */
}

.user-dropdown-menu li a i {
    margin-right: 10px;
    color: white;
}

.my-profile-info {
    display: none;
    flex-direction: column;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    background: linear-gradient(135deg, #ff9966, #ff5e62); /* Fresh gradient */
    color: white;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    margin: 15px 0;
}
        .tooltip {
            position: absolute;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            opacity: 0;
            animation: fadeIn 0.3s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
         

     
    .logo{
        color: white;
     }
    
    .btn-success{
        background-color: #2f97d3;
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        cursor: pointer;
        width: 100%;
        opacity: 0.9;
        border-radius: 10px;
    }
    .btn-success:hover {
        background-color: #55c1ff;
    }
    .btn-success:active {
        background-color: #87ceeb;
    }


</style>
</head>
<body>
    <!-- Header -->
    <header class="bg-gradient-primary text-white p-3 sticky-top">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo-white">
                <img src="logo.png" class="logo-white" alt="Logo" style="height: 40px;">
            </div>
            <div class="title">Passport Seva</div>
            <nav class="nav">
                <a class="nav-link" href="#">Home</a>
                <a class="nav-link" href="#">About Us</a>
                <a class="nav-link" href="#">Contact Us</a>
                <?php if ($isLoggedIn): ?>
                    <div class="user-profile" id="userProfile">
                        <div class="user-profile-icon" id="userProfileIcon">
                            <img src="profile-user.png" alt="User Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                        </div>
                        <div class="user-dropdown" id="userDropdown">
                            <div class="user-dropdown-header" id="userDropdownHeader">
                                <div class="user-profile-icon" id="userProfileIconDropdown">
                                    <img src="profile-user.png" alt="User Profile" class="user-logo">
                                </div>
                                <div class="user-dropdown-header-info" id="userDropdownHeaderInfo">
                                    <p id="userMobileDropdown" style="color: white;">+91 <?php echo substr($_SESSION['mobile'], 0, 2) . '******' . substr($_SESSION['mobile'], 8); ?></p>
                                </div>
                            </div>
                            <ul class="user-dropdown-menu" id="userDropdownMenu">
                                <li><a href="javascript:void(0)" id="myProfileLink" onclick="document.getElementById('myProfileInfo').style.display='block'"><i class="fas fa-user"></i> My Profile</a></li>
                                <div class="my-profile" id="myProfile">
                                   
                                    <div class="my-profile-info" id="myProfileInfo" style="display: none;">
                                        
                                        <?php
                                           // Assuming you've already started the session and have $_SESSION['mobile']
                                           $con = mysqli_connect('localhost', 'root', '', 'passport_db');
                                           
                                           // Prepare the statement
                                           $stmt = mysqli_prepare($con, "SELECT firstName, lastName, mobileNo, gender, email FROM mydata WHERE mobileNo = ?");
                                           
                                           // Bind the parameter
                                           mysqli_stmt_bind_param($stmt, "s", $_SESSION['mobile']);
                                           
                                           // Execute the statement
                                           mysqli_stmt_execute($stmt);
                                           
                                           // Get the result
                                           $stats_result = mysqli_stmt_get_result($stmt);
                                           
                                           if ($stats_result && mysqli_num_rows($stats_result) > 0) {
                                               $stats = mysqli_fetch_assoc($stats_result);
                                               ?>
                                               <p style="color: black;"><span style="font-weight: bold; color: #800080;">Name:</span> <?php echo htmlspecialchars($stats['firstName']); ?> <?php echo htmlspecialchars($stats['lastName']); ?></p>
                                               <p style="color: black;"><span style="font-weight: bold; color: #800080;">Mobile Number:</span> <?php echo htmlspecialchars(substr($stats['mobileNo'], 0, 2) . '******' . substr($stats['mobileNo'], 8)); ?></p>
                                               <p style="color: black;"><span style="font-weight: bold; color: #800080;">Gender:</span> <?php echo htmlspecialchars($stats['gender']); ?></p>
                                               <p style="color: black;"><span style="font-weight: bold; color: #800080;">Email:</span> <?php echo htmlspecialchars(substr($stats['email'], 0, 3) . '*****' . substr($stats['email'], strpos($stats['email'], '@'))); ?></p>
                                               <?php
                                           } else {
                                               echo "No user data found.";
                                           }
                                           
                                           // Close the statement
                                           mysqli_stmt_close($stmt);
                                           ?>
                                    </div>
                                </div>
                                <li><a href="application_status.php"><i class="fas fa-search"></i> Check Application Status</a></li>
                                <li><a href=""><i class="fas fa-history"></i> application History</a></li>
                                

                                <li><a href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
     <section class="home">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 bg-light p-3">
                <ul class="nav flex-column">
               
                    <li class="nav-item">
                        <button type="button" class="btn btn-success" onclick="window.location.href='applyForFreshPass.php'"><i class="fas fa-passport" style="margin-right: 10px; color: #f1c40f;"></i> Apply for Fresh Passport</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="btn btn-success" onclick="window.location.href='change_in_pass.php'"><i class="fas fa-edit" style="margin-right: 10px; color: #db34b1;"></i> Change in Existing Passport</button>
                    </li>
                    
                    <li class="nav-item">
                     <button type="button" class="btn btn-success" onclick="window.location.href='application_status.php'"><i class="fas fa-search" style="margin-right: 10px; color: #e74c3c;"></i> Check Application Status</button>
                  </li>
                  <li class="nav-item">
                        <button type="button" class="btn btn-success"><i class="fas fa-sync" style="margin-right: 10px; color: #2ecc71;"></i> Re-issue of Passport</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="btn btn-success"><i class="fas fa-exchange-alt" style="margin-right: 10px; color: #9b59b6;"></i> Replacement of Damaged Passport</button>
                    </li>
                </ul>
            </div>

            <!-- Main Area -->
            <div class="col-md-9">
                <!-- Hero Section -->
                <div class="hero text-center py-5">
                    <div class="container">
                        <h1>Welcome to Passport Seva</h1>
                        <p>Online Passport Application Service</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="view_passport.php" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> See e-Passport
                            </a>
                            <a href="#contact-us" class="btn btn-secondary">
                                <i class="fas fa-sign-in-alt"></i> Contant Us !
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Service Cards -->
                <div class="container my-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-file-alt"></i> Apply Online</h5>
                                    <p class="card-text">Fill and submit your application form online.</p>
                                    <a href="applyForFreshPass.php" class="btn btn-primary">Go to Apply</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-search"></i> Track Application</h5>
                                    <p class="card-text">Check the status of your passport application.</p>
                                    <a href="application_status.php" class="btn btn-primary">Track Now</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-calculator"></i> Your Application History</h5>
                                    <p class="card-text">Check Your Application History.</p>
                                    <a href="check_history.php" class="btn btn-primary">Check Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about-us" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">About Us</h2>
        <div class="row">
            <div class="col-md-6">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
            <div class="col-md-6">
                <img src="passport photo.png" alt="About Us" class="img-fluid about-img">
            </div>
        </div>
    </div>
</section>

<!-- Contact Us Section -->
<section id="contact-us" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Contact Us</h2>
        <div class="row">
            <div class="col-md-6">
                <form action="contact.php" method="POST">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" placeholder="Your message"></textarea>
                    </div>
                    <button type="submit" name="sb" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            <div class="col-md-6">
                <h4>Contact Information</h4>
                <p><i class="fas fa-envelope contact-icon"></i> contact@example.com</p>
                <p><i class="fas fa-phone contact-icon"></i> +1 234 567 890</p>
                <p><i class="fas fa-map-marker-alt contact-icon"></i> 123 Street, City, Country</p>
                <!-- Optional map placeholder -->
                <div id="map" style="height: 200px; background: #ccc; margin-top: 1rem;"></div>
            </div>
        </div>
    </div>
</section>

    <!-- Footer -->
    <footer class="footer bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <!-- Contact Section -->
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">Contact Us</h5>
                    <p><i class="fas fa-envelope"></i> info@example.com</p>
                    <p><i class="fas fa-phone"></i> +1 234 567 890</p>
                </div>
                <!-- Social Media Section -->
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">Follow Us</h5>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <!-- Newsletter Section -->
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">Newsletter</h5>
                    <form>
                        <input type="email" class="form-control mb-2" placeholder="Your email">
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom text-center mt-4">
                <p>&copy; 2023 Your Company. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userProfile = document.getElementById('userProfile');
            const userProfileIcon = document.getElementById('userProfileIcon');
            const userDropdown = document.getElementById('userDropdown');
            const myProfileLink = document.getElementById('myProfileLink');
            const myProfileInfo = document.getElementById('myProfileInfo');

            // Debug logging
            console.log('User Profile Element:', userProfile);
            console.log('User Profile Icon Element:', userProfileIcon);
            console.log('User Dropdown Element:', userDropdown);

            // Toggle dropdown when profile icon is clicked
            if (userProfileIcon) {
                userProfileIcon.addEventListener('click', function(event) {
                    event.stopPropagation();
                    console.log('Profile icon clicked, toggling dropdown');
                    userDropdown.classList.toggle('show');
                    console.log('Dropdown classes after toggle:', userDropdown.className);
                });
            } else {
                console.error('User profile icon not found!');
            }

            // Toggle profile info when "My Profile" is clicked
            if (myProfileLink) {
                myProfileLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    if (myProfileInfo.style.display === 'none' || myProfileInfo.style.display === '') {
                        myProfileInfo.style.display = 'block';
                    } else {
                        myProfileInfo.style.display = 'none';
                    }
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (userProfile && !userProfile.contains(event.target)) {
                    userDropdown.classList.remove('show');
                }
            });

            // Close dropdown when pressing Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    userDropdown.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>