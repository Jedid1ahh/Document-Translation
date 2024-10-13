<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Document Translation System</title>
</head>
<body>
    <header>
        <div class="logo"><img src="../assets/images/logo.png" alt="Logo"></div>
        <input type="text" placeholder="Search...">
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="#">Upload Document</a></li>
                <li><a href="#">Languages</a></li>
                <li><a href="#">My Translations</a></li>
                <li><a href="#">Help</a></li>
                <li><a href="#"><?php echo $_SESSION['username']; ?></a></li>
            </ul>
        </nav>
    </header>
