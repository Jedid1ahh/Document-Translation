<?php
session_start();
require '../../includes/config.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../auth/login.php');
    exit();
}

// Check if the user's role is "Admin"
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../../auth/login.php'); // or another appropriate page
    exit();
}

// Retrieve users from the database
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalUsers = count($users); // Count total registered users

// Retrieve translations from the database
$stmt = $pdo->query("SELECT * FROM translations");
$translation = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalTranslations = count($translation); // Count total registered users

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $password, $role])) {
        // Redirect back to the same page after user creation
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $username = $_POST['username'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
    if ($stmt->execute([$username])) {
        // Redirect back to the same page after deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch all translations
$query = "SELECT t.id, d.filename, t.source_language, t.target_language, t.status, t.created_at, t.updated_at 
          FROM translations t
          JOIN documents d ON t.document_id = d.id";
$result = $pdo->query($query);

// Fetch reviewers
$reviewers_query = "SELECT id, username FROM users WHERE role = 'Reviewer'";
$reviewers_result = $pdo->query($reviewers_query);

// Get translation ID from the query parameter
$translation_id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reviewer_id = $_POST['reviewer_id'];

    // Assign the translation to the reviewer
    $query = "INSERT INTO translation_reviews (translation_id, reviewer_id, created_at) VALUES (?, ?, CURRENT_TIMESTAMP)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$translation_id, $reviewer_id]);

    // Update the status field in the translations table to 'In Progress'
    $updateQuery = "UPDATE translations SET status = 'In Progress' WHERE id = ?";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([$translation_id]);

    // Redirect to the translations page
    header("Location: ../../dashboard/admin/transmgt.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Translation System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">

    <style>
        /* General styles */
        body {
            font-family: 'Open Sans', sans-serif;
        }
        /* Profile picture styling */
        .profile-pic {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .statistics {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .stat {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }
        .stat h3 {
            margin: 0 0 10px;
        }
        #create-user {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }
        #create-user:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #e9ecef;
        }
        main {
            margin-top: 100px;
            padding: 20px;
        }
    </style>
</head>
<body>
<!-- Main Body -->
    <div class="container">
        <h1>Assign Translation</h1>
        <form method="POST">
            <div class="form-group">
                <label for="reviewer_id">Select Reviewer</label>
                <select name="reviewer_id" id="reviewer_id" class="form-control">
                    <?php while ($reviewer = $reviewers_result->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $reviewer['id']; ?>"><?php echo $reviewer['username']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Assign</button>
        </form>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
