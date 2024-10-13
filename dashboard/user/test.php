<?php
session_start();
include '../../includes/config.php'; // Ensure you are using PDO connection in 'db.php'

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);  // Use execute with array for binding in PDO
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get submitted documents count
$query = "SELECT COUNT(*) as doc_count FROM documents WHERE user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$doc_count = $stmt->fetch(PDO::FETCH_ASSOC)['doc_count'];

// Get translation requests count
$query = "SELECT COUNT(*) as trans_count FROM translations WHERE document_id IN (SELECT id FROM documents WHERE user_id = ?)";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$trans_count = $stmt->fetch(PDO::FETCH_ASSOC)['trans_count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link your CSS file -->
</head>
<body>
    <div class="dashboard">
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
        <div class="stats">
            <div class="stat">Submitted Documents: <?php echo $doc_count; ?></div>
            <div class="stat">Translation Requests: <?php echo $trans_count; ?></div>
        </div>

        <h2>Document Submission</h2>
        <form action="upload_document.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="document" required>
            <select name="language_pair" required>
                <!-- Populate with language pairs from the database -->
                <?php
                $language_pairs = $conn->query("SELECT * FROM language_pairs");
                while ($row = $language_pairs->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['source_language']}-{$row['target_language']}'>{$row['source_language']} to {$row['target_language']}</option>";
                }
                ?>
            </select>
            <button type="submit">Upload Document</button>
        </form>

        <h2>Your Documents</h2>
        <table>
            <tr>
                <th>Filename</th>
                <th>Actions</th>
            </tr>
            <?php
            $query = "SELECT * FROM documents WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$user_id]);
            while ($doc = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>{$doc['filename']}</td>
                        <td>
                            <a href='edit_document.php?id={$doc['id']}'>Edit</a> | 
                            <a href='delete_document.php?id={$doc['id']}'>Delete</a>
                        </td>
                    </tr>";
            }
            ?>
        </table>

        <h2>Profile Management</h2>
        <form action="update_profile.php" method="POST">
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
