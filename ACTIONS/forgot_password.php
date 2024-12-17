<?php
// Start session
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Include database connection
include 'db_connect.php'; // Ensure this file sets up and provides `$conn`

// Autoload PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require '../vendor/autoload.php'; // Ensure this path is correct

// Function to handle errors
function handleError($message) {
    echo "<p style='color:red;'>$message</p>";
}

// Generate reset token
function generateResetToken($email) {
    global $conn; // Use the `$conn` object from `db_connect.php`

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        return false;
    }

    // Generate unique token
    $token = bin2hex(random_bytes(32)); // 64-character token
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store token in database
    $stmt = $conn->prepare("UPDATE Users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
    $stmt->bind_param("sss", $token, $expiry, $email);
    $stmt->execute();

    return $token;
}
function sendPasswordResetEmail($email, $token) {
    // $reset_link = sprintf(
    //     "%s://%s%s/reset_password.php?token=%s",
    //     isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
    //     $_SERVER['HTTP_HOST'],
    //     dirname($_SERVER['PHP_SELF']),
    //     urlencode($token)
    // );
    // Get the current server protocol (http or https)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    
    // Get the server name (domain)
    $server_name = $_SERVER['HTTP_HOST'];
    
    // Get the current script's directory path
    $script_path = dirname($_SERVER['PHP_SELF']);
    
    // Construct the full reset link dynamically
    $reset_link = $protocol . "://" . $server_name . $script_path . "/reset_password.php?token=" . urlencode($token);
    
    $mail = new PHPMailer(true);
    
    try {
        // [Rest of your existing email sending code remains the same]
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'nzu9963@gmail.com';
        $mail->Password = 'coodiwtigcyohkil';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port = 587;

        $mail->setFrom('nzu9963@gmail.com', 'AgroLink');
        $mail->addAddress($email);

        $mail->isHTML(false);
        $mail->Subject = "Password Reset Request";
        $mail->Body = "Click the following link to reset your password: \n\n" . $reset_link . "\n  \nThis link will expire in 1 hour.";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        handleError("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
// // Send password reset email using PHPMailer
// function sendPasswordResetEmail($email, $token) {
//     $reset_link = "https://yourwebsite.com/reset_password.php?token=" . urlencode($token);
    
//     $mail = new PHPMailer(true);
    
//     try {
//         // Server settings (replace with your SMTP details)
//         $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
//         $mail->SMTPAuth = true;
//         $mail->Username = 'nzu9963@gmail.com'; // Replace with your SMTP username
//         $mail->Password = 'coodiwtigcyohkil'; // Replace with your SMTP password
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
//         $mail->Port = 587; // Replace with your SMTP port

//         // Recipients
//         $mail->setFrom('nzu9963@gmail.com', 'AgroLink');
//         $mail->addAddress($email);

//         // Content
//         $mail->isHTML(false);
//         $mail->Subject = "Password Reset Request";
//         $mail->Body = "Click the following link to reset your password:\n\n" . $reset_link . "\n\nThis link will expire in 1 hour.";
        
//         $mail->send();
//         return true;
        
//     } catch (Exception $e) {
//         handleError("Mailer Error: " . $mail->ErrorInfo);
//         return false;
//     }
// }

// Handle password reset request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        handleError("Invalid email format");
        renderPasswordResetRequestForm();
        exit;
    }

    $token = generateResetToken($email);
    
    if ($token) {
        if (sendPasswordResetEmail($email, $token)) {
            echo "Password reset link sent to your email.";
        } else {
            handleError("Failed to send reset email.");
            renderPasswordResetRequestForm();
            exit;
        }
    } else {
        handleError("No user found with this email address.");
        renderPasswordResetRequestForm();
        exit;
    }
}

// Validate reset token
function validateResetToken($token) {
    global $conn;

    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    return ($stmt->get_result()->fetch_assoc() !== null);
}

// Reset password function
function resetPassword($token, $new_password) {
    global $conn;

    // Validate token first
    if (!validateResetToken($token)) {
        return false;
    }

    // Hash the new password securely
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update password and clear reset token
    $stmt = $conn->prepare("UPDATE users SET 
        password = ?, 
        reset_token = NULL, 
        reset_token_expiry = NULL 
        WHERE id = (SELECT id FROM users WHERE reset_token = ?)");
    
    $stmt->bind_param("ss", $hashed_password, $token);
    return $stmt->execute();
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_token'])) {
    $token = $_POST['reset_token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        handleError("Passwords do not match");
        exit;
    }

    if (resetPassword($token, $new_password)) {
        echo "Password successfully reset. You can now login.";
        exit;
    } else {
        handleError("Failed to reset password.");
        exit;
    }
}

// Render HTML for password reset request form
function renderPasswordResetRequestForm() {
?>
<style>
form {
   max-width: 400px;
   margin: auto;
   padding: 20px;
   border: 1px solid #ccc;
   border-radius: 5px;
   background-color: #f9f9f9;
}
input[type="email"],
input[type="password"] {
   width: 100%;
   padding: 10px;
   margin: 10px 0;
   border: 1px solid #ccc;
   border-radius: 4px;
}
button {
   width: 100%;
   padding: 10px;
   background-color: #4CAF50; /* Green */
   color: white;
   border: none;
   border-radius: 4px;
   cursor: pointer;
}
button:hover {
   background-color: #45a049; /* Darker green */
}
</style>
<form method="POST" action="">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Reset Link</button>
</form>
<?php
}

// Render HTML for password reset form
function renderPasswordResetForm($token) {
?>
<style>
form {
   max-width: 400px;
   margin: auto;
   padding: 20px;
   border: 1px solid #ccc;
   border-radius: 5px;
   background-color: #f9f9f9;
}
input[type="password"] {
   width: 100%;
   padding: 10px;
   margin: 10px 0;
   border: 1px solid #ccc;
   border-radius: 4px;
}
button {
   width: 100%;
   padding: 10px;
   background-color: #4CAF50; /* Green */
   color: white;
   border: none;
   border-radius: 4px;
   cursor: pointer;
}
button:hover {
   background-color: #45a049; /* Darker green */
}
</style>
<form method="POST" action="">
    <input type="hidden" name="reset_token" value="<?php echo htmlspecialchars($token); ?>">
    <input type="password" name="new_password" placeholder="New Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
    <button type="submit">Reset Password</button>
</form>
<?php
}

// Check and render appropriate form based on token presence
if (isset($_GET['token'])) {
    if (validateResetToken($_GET['token'])) {
        renderPasswordResetForm($_GET['token']);
    } else {
        handleError("Invalid or expired reset token.");
        renderPasswordResetRequestForm();
        exit;
    }
} else {
   renderPasswordResetRequestForm();
}
?>