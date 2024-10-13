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

if ($translation_id) {
    // Delete the translation
    $query = "DELETE FROM translations WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':id', $translation_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: ../../dashboard/admin/transmgt.php"); // Redirect to translations page
    exit();
}
?>
