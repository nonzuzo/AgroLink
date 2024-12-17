<?php
// Include the database connection file
include 'db_connect.php'; 

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize error variables
$nameError = $emailError = $passwordError = $confirmPasswordError = $userTypeError = "";
$name = $email = $userType = ""; // Preserve input values on form reload
$successMessage = "";

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate name
function validateName($name) {
    // Allow letters, spaces, and a wide range of special characters used in names
    if (empty($name)) {
        return "Name is required.";
    }
    if (strlen($name) < 2 || strlen($name) > 50) {
        return "Name must be between 2 and 50 characters.";
    }
    if (!preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð \.\-']+$/u", $name)) {
        return "Name contains invalid characters.";
    }
    return "";
}

// Function to validate email
function validateEmail($email, $conn) {
    if (empty($email)) {
        return "Email is required.";
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }
    
    // Check email length
    if (strlen($email) > 100) {
        return "Email is too long. Maximum 100 characters.";
    }
    
    // Check for existing email in database
    $stmtCheckEmail = $conn->prepare("SELECT email FROM Users WHERE email = ?");
    if ($stmtCheckEmail === false) {
        return "Database error checking email.";
    }
    $stmtCheckEmail->bind_param("s", $email);
    $stmtCheckEmail->execute();
    $resultCheckEmail = $stmtCheckEmail->get_result();
    
    if ($resultCheckEmail->num_rows > 0) {
        return "Email already exists.";
    }
    
    return "";
}

// Function to validate password
function validatePassword($password, $confirmPassword) {
    // Check if password is empty
    if (empty($password)) {
        return "Password is required.";
    }
    
    // Check password length
    if (strlen($password) < 9) {
        return "Password must be at least 8 characters long.";
    }
    
    // Check password complexity
    $uppercase = preg_match('/[A-Z]/', $password);
    $lowercase = preg_match('/[a-z]/', $password);
    $number = preg_match('/\d/', $password);
    $specialChar = preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password);
    
    if (!$uppercase || !$lowercase || !$number || !$specialChar) {
        return "Password must include uppercase, lowercase, number, and special character.";
    }
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        return "Passwords do not match.";
    }
    
    return "";
}

// Function to validate user type
function validateUserType($userType) {
    $allowedTypes = ['farmer', 'buyer'];
    
    if (empty($userType)) {
        return "User type is required.";
    }
    
    if (!in_array(strtolower($userType), $allowedTypes)) {
        return "Invalid user type selected.";
    }
    
    return "";
}

// Process form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect input values
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $userType = sanitizeInput($_POST['userType']);

    // Validate inputs
    $nameError = validateName($name);
    $emailError = validateEmail($email, $conn);
    $passwordError = validatePassword($password, $confirmPassword);
    $userTypeError = validateUserType($userType);

    // If there are no errors, proceed with registration
    if (empty($nameError) && empty($emailError) && empty($passwordError) && empty($userTypeError)) {
        // Hash the password before storing it
        // Use a stronger hashing method with high cost factor
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 9]);

        // Prepare and bind SQL statement with additional security
        $stmtInsertUser = $conn->prepare("INSERT INTO Users (name, email, password, user_type, created_at) VALUES (?, ?, ?, ?, NOW())");
        if ($stmtInsertUser === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        
        // Bind parameters and execute the statement
        $lowercaseUserType = strtolower($userType);
        $stmtInsertUser->bind_param("ssss", $name, $email, $hashedPassword, $lowercaseUserType);
        
        // Execute the statement and check for success
        try {
            if ($stmtInsertUser->execute()) {
                // Set session or cookie for success message
                session_start();
                $_SESSION['signup_success'] = "Registration successful! Please log in.";
                
                // Redirect to login page
                header("Location: ../VIEWS/login.html");
                exit();
            } else {
                // Log the error for admin review
                error_log("Registration failed: " . $stmtInsertUser->error);
                $successMessage = "An error occurred. Please try again later.";
            }
        } catch (Exception $e) {
            // Log any unexpected errors
            error_log("Registration exception: " . $e->getMessage());
            $successMessage = "An unexpected error occurred.";
        }

        // Close statement
        $stmtInsertUser->close();
    }
}

// Close the database connection at the end of the script only once
if ($conn) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign-Up Page</title>
  <link rel="stylesheet" href="../CSS/signup.css">
  <script src="../JS/signup.js"></script>
</head>
<body>
  <!-- Sign-Up Section -->
  <section class="signup__container">
    <h1 class="section__header">Create an Account</h1>
    <p class="section__description">Join us by selecting your role as a Farmer or Buyer to get started.</p>
    
    <div class="signup__form__container">
      <form id="signupForm" class="signup__form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
        <div class="form__group">
          <label for="name">Name</label>
          <p class="error-message" id="nameError"><?php echo $nameError; ?></p>
          <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        
        <div class="form__group">
          <label for="email">Email</label>
          <p class="error-message" id="emailError"><?php echo $emailError; ?></p>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        
        <div class="form__group">
          <label for="password">Password</label>
          <p class="error-message" id="passwordError"><?php echo $passwordError; ?></p>
          <input type="password" id="password" name="password" required>
          <small>Must be at least 8 characters with uppercase, lowercase, number, and special character (!@#$%^&*()_+\-=[]{};"':,.<>/?)</small>
        </div>
        
        <div class="form__group">
          <label for="confirmPassword">Confirm Password</label>
          <p class="error-message" id="confirmPasswordError"></p>
          <input type="password" id="confirmPassword" name="confirmPassword" required>
        </div>
        
        <div class="form__group">
          <label for="userType">User Type</label>
          <p class="error-message" id="userTypeError"><?php echo $userTypeError; ?></p>
          <select id="userType" name="userType" required>
            <option value="" disabled <?php echo empty($userType) ? 'selected' : ''; ?>>Select your role</option>
            <option value="farmer" <?php echo ($userType == 'farmer') ? 'selected' : ''; ?>>Farmer</option>
            <option value="buyer" <?php echo ($userType == 'buyer') ? 'selected' : ''; ?>>Buyer</option>
          </select>
        </div>
        
        <button type="submit" class="btn">Sign Up</button>

        <!-- Login Redirect Section -->
        <div class="login-redirect">
            <p>Already have an account? <a href="../VIEWS/login.html" class="login-link">Login here</a>.</p>
        </div>
      </form>

      <!-- Role Description -->
      <div class="role__description">
        <div class="role__card">
          <h3>Farmer</h3>
          <p>Farmers can list their produce, set prices, and connect directly with buyers interested in purchasing fresh farm products.</p>
        </div>
        <div class="role__card">
          <h3>Buyer</h3>
          <p>Buyers can explore a range of fresh produce, place orders, and directly support local farmers.</p>
        </div>
      </div>
    
    </div>
  </section>

</body>
</html>