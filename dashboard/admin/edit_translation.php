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

$translation_id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated status from the form
    $status = $_POST['status'];

    // Update the translation status
    $query = "UPDATE translations SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(1, $status, PDO::PARAM_STR); // Bind the status parameter
    $stmt->bindValue(2, $translation_id, PDO::PARAM_INT); // Bind the translation ID parameter
    $stmt->execute();

    header("Location: ../../dashboard/admin/transmgt.php"); // Redirect to translations page
    exit();
}

// Fetch the translation details
$query = "SELECT * FROM translations WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->bindValue(1, $translation_id, PDO::PARAM_INT); // Bind the translation ID parameter
$stmt->execute();
$translation = $stmt->fetch(PDO::FETCH_ASSOC); // Use fetch with PDO style

// Check if translation exists
if (!$translation) {
    echo "Translation not found.";
    exit(); // Terminate the script if no translation is found
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
</head>
<body>
    <div class="container">
        <h1>Edit Translation Status</h1>
        <form method="POST">
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="Pending" <?php if ($translation['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="In Progress" <?php if ($translation['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                <option value="Completed" <?php if ($translation['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                <option value="Reviewed" <?php if ($translation['status'] == 'Reviewed') echo 'selected'; ?>>Reviewed</option>
                <option value="Rejected" <?php if ($translation['status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
            </select>
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>

<?php
// No need to close the statement or the connection, as PDO will take care of it
?>
