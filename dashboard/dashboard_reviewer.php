<?php
session_start();
if ($_SESSION['role'] !== 'reviewer') {
    header('Location: login.php');
    exit();
}
include('../includes/header.php');
?>

<div class="container">
    <h1>Reviewer Dashboard</h1>
    <div class="dashboard">
        <h2>Review Documents</h2>
        <a href="review_documents.php" class="btn btn-primary">Review Submitted Documents</a>
        <a href="upload_documents.php" class="btn btn-primary">Upload New Documents</a>
        <a href="provide_feedback.php" class="btn btn-primary">Provide Feedback</a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
