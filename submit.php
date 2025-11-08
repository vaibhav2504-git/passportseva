<?php
// Database connection (replace with your credentials)
$conn = mysqli_connect("localhost", "root", "", "passport_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $errors = [];
    $identity_proof = "";
    $address_proof = "";
    $photograph = "";

    // Handle Identity Proof
    if (isset($_FILES['identity_proof']) && $_FILES['identity_proof']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['identity_proof']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['identity_proof']['size'] <= 2000000) {
            $identity_proof = $upload_dir . uniqid() . '.' . $ext;
            if (!move_uploaded_file($_FILES['identity_proof']['tmp_name'], $identity_proof)) {
                $errors[] = "Failed to upload identity proof.";
            }
        } else {
            $errors[] = "Invalid file type or size (>2MB) for identity proof.";
        }
    } else {
        $errors[] = "Identity proof is required.";
    }

    // Handle Address Proof
    if (isset($_FILES['address_proof']) && $_FILES['address_proof']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['address_proof']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['address_proof']['size'] <= 2000000) {
            $address_proof = $upload_dir . uniqid() . '.' . $ext;
            if (!move_uploaded_file($_FILES['address_proof']['tmp_name'], $address_proof)) {
                $errors[] = "Failed to upload address proof.";
            }
        } else {
            $errors[] = "Invalid file type or size (>2MB) for address proof.";
        }
    } else {
        $errors[] = "Address proof is required.";
    }

    // Handle Photograph
    if (isset($_FILES['photograph']) && $_FILES['photograph']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['photograph']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['photograph']['size'] <= 2000000) {
            $photograph = $upload_dir . uniqid() . '.' . $ext;
            if (!move_uploaded_file($_FILES['photograph']['tmp_name'], $photograph)) {
                $errors[] = "Failed to upload photograph.";
            }
        } else {
            $errors[] = "Invalid file type or size (>2MB) for photograph.";
        }
    } else {
        $errors[] = "Photograph is required.";
    }

    // If no errors, process and store data
    if (empty($errors)) {
        // Retrieve form data
        $full_name = $_POST['full_name'];
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $pin_code = $_POST['pin_code'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $aadhaar_number = $_POST['aadhaar_number'];
        $status = $_POST['status'];

        // Prepare and execute SQL statement
        $stmt = $conn->prepare("INSERT INTO applications (full_name, date_of_birth, gender, address, city, state, pin_code, email, phone_number, aadhaar_number, identity_proof, address_proof, photograph, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
        $stmt->bind_param("ssssssssssssss", $full_name, $date_of_birth, $gender, $address, $city, $state, $pin_code, $email, $phone_number, $aadhaar_number, $identity_proof, $address_proof, $photograph, $status);

        if ($stmt->execute()) {
            echo "Application submitted successfully.";
            echo '<br><a href="index_loggin.php">Go to Home Page</a>';


        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}

$conn->close();
?>