<?php
session_start();
if ($_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit();
}
include('../includes/header.php');
?>

<div class="container">
    <h1>User Dashboard</h1>
    <div class="dashboard">
        <h2>My Documents</h2>
        <a href="upload_document.php" class="btn btn-primary">Upload Document</a>
        <a href="view_my_documents.php" class="btn btn-primary">View My Uploaded Documents</a>
        <a href="submission_status.php" class="btn btn-primary">Track Submission Status</a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
