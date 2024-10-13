<?php
require '../includes/config.php'; // Include your database connection file
session_start();

// Check if there's an existing error message to display
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']); // Clear the error message after displaying it

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: register.php"); // Redirect back to register page to display error
        exit;
    }

    try {
        // Prepare SQL statement to insert user without password hashing
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role]);

        // Redirect to login page after successful registration
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . htmlspecialchars($e->getMessage());
        header("Location: register.php"); // Redirect back to register page to display error
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>User Registration</title>
    <style>
        /* Styling for the role dropdown menu */
        select[name="role"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
            transition: border 0.3s;
        }

        select[name="role"]:focus {
            border-color: #007bff; /* Change border color on focus */
            outline: none; /* Remove outline */
        }
    </style>
</head>

<body>
    <main>
        <div class="container">
            <div class="form-container">
                <form id="registerForm" class="form" method="POST" action="register.php">
                    <h2>Register</h2>
                    <?php if ($error_message): ?>
                        <div class="error-message"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="text" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    
                    <select name="role" required>
                        <option value="">Select Role</option>
                        <option value="Reviewer">Reviewer</option>
                        <option value="User">User</option>
                    </select>
                    <button type="submit" name="register">Register</button>
                    <br>
                    <div class="error-message">Have an account? Login <a href="login.php">Here</a></div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <a href="#">Terms of Service</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Help</a>
            <a href="#">Contact Us</a>
            <div class="social-media">
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-github"></i></a>
            </div>
            <div class="language-selector">
                <select>
                    <option value="en">English</option>
                    <option value="es">Español</option>
                </select>
            </div>
        </div>
    </footer>

    <script src="../assets/js/script.js"></script>
</body>

</html>
