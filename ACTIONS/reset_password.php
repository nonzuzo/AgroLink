<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Include database connection and PHPMailer
require_once 'db_connect.php';
require_once '../vendor/autoload.php';

// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to handle and display errors
function displayError($message) {
    echo "<div style='color: red; border: 1px solid red; padding: 10px; margin: 10px 0;'>";
    echo htmlspecialchars($message);
    echo "</div>";
}

// Function to validate reset token
function validateResetToken($token) {
    global $conn;

    // Prepare statement to check token validity
    $stmt = $conn->prepare("SELECT user_id, email FROM Users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Function to reset password
function resetUserPassword($user_id, $new_password) {
    global $conn;

    // Validate password strength
    if (strlen($new_password) < 8) {
        throw new Exception("Password must be at least 8 characters long");
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Prepare statement to update password and clear reset token
    $stmt = $conn->prepare("UPDATE Users SET 
        password = ?, 
        reset_token = NULL, 
        reset_token_expiry = NULL 
        WHERE user_id = ?");
    
    $stmt->bind_param("si", $hashed_password, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update password: " . $stmt->error);
    }

    return true;
}

// Render password reset form
function renderResetPasswordForm($token) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .reset-form {
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="reset-form">
        <h2>Reset Your Password</h2>
        <form method="POST" action="">
            <input type="hidden" name="reset_token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="password" name="new_password" placeholder="New Password" required minlength="8">
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required minlength="8">
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
<?php
}

// Main script logic
try {
    // Check if token is provided in GET request
    if (!isset($_GET['token']) && !isset($_POST['reset_token'])) {
        throw new Exception("No reset token provided");
    }

    $token = isset($_GET['token']) ? $_GET['token'] : $_POST['reset_token'];

    // Validate the reset token
    $user = validateResetToken($token);
    if (!$user) {
        throw new Exception("Invalid or expired reset token");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate password match
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            throw new Exception("Passwords do not match");
        }

        // Reset the password
        resetUserPassword($user['user_id'], $_POST['new_password']);

        // Display success message
        echo "<div style='text-align: center; color: green; margin-top: 50px;'>";
        echo "<h2>Password Reset Successfully</h2>";
        echo "<p>You can now log in with your new password.</p>";
        echo "</div>";
        exit;
    }

    // If no POST data, render the reset password form
    renderResetPasswordForm($token);

} catch (Exception $e) {
    // Display any errors that occur
    displayError($e->getMessage());
    
    // Optionally, render the form again or redirect
    renderResetPasswordForm($token);
}
?>