<?php
require '../includes/config.php'; // Include your database connection file
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if there's an existing error message to display
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']); // Clear the error message after displaying it

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Both email and password are required.";
        header("Location: login.php"); // Redirect back to login page to display error
        exit;
    }

    try {
        // Prepare SQL statement to fetch user by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debugging output
        if ($user) {
            echo "User found: ";
            var_dump($user); // Show user data for debugging
        } else {
            echo "No user found with that email.<br>";
        }

        // Check if user exists and verify password directly
        if ($user && $password === $user['password']) { // Compare plain text passwords
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            switch ($user['role']) {
                case 'Admin': 
                    header("Location: ../dashboard/admin/dashboard.php");
                    break;
                case 'Reviewer':
                    header("Location: ../dashboard/reviewer/dashboard.php");
                    break;
                case 'User':
                    header("Location: ../dashboard/user/dashboard.php");
                    break;
                default:
                    $_SESSION['error_message'] = "Invalid role.";
                    header("Location: login.php");
                    break;
            }
            exit;
        } else {
            $_SESSION['error_message'] = "Invalid email or password.";
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . htmlspecialchars($e->getMessage());
        header("Location: login.php"); // Redirect back to login page to display error
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
    <title>Login</title>
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
                <form id="registerForm" class="form" method="POST" action="login.php">
                    <h2>Login</h2>
                    <?php if ($error_message): ?>
                        <div class="error-message"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <input type="text" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
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

