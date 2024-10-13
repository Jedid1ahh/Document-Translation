<?php
session_start();
require '../../includes/config.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../auth/login.php');
    exit();
}

// Check if the user's role is "User"
if ($_SESSION['role'] !== 'User') {
    header('Location: ../../auth/login.php'); // or another appropriate page
    exit();
}

// Include the Composer autoloader
require '../../vendor/autoload.php';
use DeepL\Translator;

// Your DeepL API key
$authKey = "43b99bba-bf47-43e7-ae92-cac6158f21c4:fx";


// Fetch the user ID from the database based on the session username
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
$stmt->execute([':username' => $_SESSION['username']]);
$user = $stmt->fetch();

if (!$user) {
    die('User not found.');
}

$userId = $user['id']; // Use this user ID for document insertion

// Check if a file has been uploaded and a language has been selected
if (isset($_FILES['document']) && $_FILES['document']['error'] == 0 && isset($_POST['language'])) {
    // Define the upload directory within the project directory
    $uploadDir = __DIR__ . '../../uploads/';

    // Ensure the uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create directory with read/write permissions
    }

    // Get the uploaded file information
    $uploadedFile = $_FILES['document']['tmp_name'];
    $originalFileName = basename($_FILES['document']['name']);
    $uploadedFilePath = $uploadDir . $originalFileName;

    // Determine the file type based on the file extension
    $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    $fileType = '';
    if ($fileExtension === 'pdf') {
        $fileType = 'PDF';
    } elseif ($fileExtension === 'docx') {
        $fileType = 'DOCX';
    } elseif ($fileExtension === 'csv') {
        $fileType = 'CSV';
    } elseif ($fileExtension === 'txt') {
        $fileType = 'TXT';
    }elseif ($fileExtension === 'ppt') {
        $fileType = 'PPT';
    }
    else {
        $message = 'Unsupported file type.';
        exit;
    }

    // Debugging step: Check if file type is correctly determined
    error_log("File type determined: " . $fileType); // This logs the file type to the server log

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($uploadedFile, $uploadedFilePath)) {
        // Initialize the DeepL Translator
        $translator = new Translator($authKey);

        // Get the selected language from the dropdown
        $targetLanguage = $_POST['language'];
        $sourceLanguage = 'en';  // Assuming source language is always English

        // Define the output file name
        $outputFilePath = $uploadDir . 'translated_' . $originalFileName;

        // Insert document details into the 'documents' table
        $stmt = $pdo->prepare("
            INSERT INTO documents (user_id, filename, file_type) 
            VALUES (:user_id, :filename, :file_type)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':filename' => $originalFileName,
            ':file_type' => $fileType
        ]);

        // Get the last inserted document ID
        $documentId = $pdo->lastInsertId();

        // Insert translation details into the 'translations' table (set status as 'Pending')
        $stmt = $pdo->prepare("
            INSERT INTO translations (document_id, source_language, target_language, status) 
            VALUES (:document_id, :source_language, :target_language, 'Pending')
        ");
        $stmt->execute([
            ':document_id' => $documentId,
            ':source_language' => $sourceLanguage,
            ':target_language' => $targetLanguage
        ]);

        // Log the action in translation_logs table
        $translation_id = $pdo->lastInsertId();
        $action = 'Created'; // Action type
        $stmt = $pdo->prepare("
            INSERT INTO translation_logs (translation_id, action, user_id) 
            VALUES (:document_id, :source_language, :target_language, 'Pending')
        ");
        $stmt = $pdo->prepare("
        INSERT INTO translation_logs (translation_id, action, user_id) 
        VALUES (:translation_id, :action, :user_id)");
        $stmt->execute([
            ':translation_id' => $translation_id, 
            ':action' => $action, 
            ':user_id' => $userId
        ]);
        
        // Try to translate the document
        try {
            $translator->translateDocument(
                $uploadedFilePath,
                $outputFilePath,
                $sourceLanguage,  // Source language (English)
                $targetLanguage,  // Target language from user selection
                ['formality' => 'more']
            );
            
            // Update the translation status to 'Completed'
            $stmt = $pdo->prepare("
                UPDATE translations 
                SET status = 'Pending', updated_at = NOW() 
                WHERE document_id = :document_id
            ");
            $stmt->execute([':document_id' => $documentId]);

            $message = 'Document Successfully Translated.';
        } catch (\DeepL\DocumentTranslationException $error) {
            $message = 'Error occurred while translating document: ' . ($error->getMessage() ?? 'unknown error');
            if ($error->documentHandle) {
                $handle = $error->documentHandle;
                $message .= " Document ID: {$handle->documentId}, document key: {$handle->documentKey}";
            } else {
                $message .= 'Unknown document handle';
            }
            
            // Update the translation status to 'Rejected'
            $stmt = $pdo->prepare("
                UPDATE translations 
                SET status = 'Rejected', updated_at = NOW() 
                WHERE document_id = :document_id
            ");
            $stmt->execute([':document_id' => $documentId]);
        }
    } else {
        $message = 'Error moving the uploaded file.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Translation Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Color Scheme */
:root {
    --primary-color: #0056b3;
    --secondary-color: #6c757d;
    --accent-color: #28a745;
    --background-color: #f8f9fa;
    --text-color: #333;
    --light-text-color: #666;
}

/* Dark Mode */
.dark-mode {
    --primary-color: #4a90e2;
    --secondary-color: #a9a9a9;
    --accent-color: #32cd32;
    --background-color: #2c2c2c;
    --text-color: #f0f0f0;
    --light-text-color: #d3d3d3;
}

body {
    font-family: 'Roboto', sans-serif;
    background-color: var(--background-color);
    color: var(--text-color);
    transition: background-color 0.3s, color 0.3s;
}

/* Typography */
h1, h2, h3, h4, h5, h6 {
    font-family: 'Roboto', sans-serif;
    font-weight: 700;
}

/* Header */
.navbar {
    background-color: var(--primary-color) !important;
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
}

.navbar-brand img {
    max-height: 40px;
}

.navbar-light .navbar-nav .nav-link {
    color: #fff;
}

.navbar-light .navbar-nav .nav-link:hover {
    color: var(--accent-color);
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar-sticky {
    position: relative;
    top: 0;
    height: calc(100vh - 48px);
    padding-top: .5rem;
    overflow-x: hidden;
    overflow-y: auto;
}

.sidebar .nav-link {
    font-weight: 500;
    color: var(--text-color);
}

.sidebar .nav-link:hover {
    color: var(--accent-color);
}

.sidebar .nav-link.active {
    color: var(--primary-color);
}

.sidebar .nav-link i {
    margin-right: 4px;
    color: var(--secondary-color);
}

/* Main Content */
main {
    padding-top: 60px;
}

.card {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
}

/* Upload Area */
#drop-area {
    border: 2px dashed var(--secondary-color);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease-in-out;
}

#drop-area.highlight {
    background-color: rgba(var(--accent-color), 0.1);
    border-color: var(--accent-color);
}

/* Language Selection */
.form-select {
    border: 1px solid var(--secondary-color);
    color: var(--text-color);
    background-color: var(--background-color);
}

/* Translate Button */
#translateBtn {
    background-color: var(--accent-color);
    border: none;
    transition: all 0.3s ease-in-out;
}

#translateBtn:hover {
    background-color: darken(var(--accent-color), 10%);
    transform: translateY(-2px);
}

/* Translation Preview */
#originalText, #translatedText {
    width: 100%;
    min-height: 200px;
    border: 1px solid var(--secondary-color);
    border-radius: 5px;
    padding: 10px;
    background-color: var(--background-color);
    color: var(--text-color);
}

/* Footer */
.footer {
    background-color: var(--primary-color);
    color: #fff;
}

.footer a {
    color: #fff;
    text-decoration: none;
}

.footer a:hover {
    color: var(--accent-color);
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        position: static;
        height: auto;
        padding-top: 0;
    }

    main {
        padding-top: 20px;
    }

    .navbar-brand img {
        max-height: 30px;
    }
}

/* Accessibility */
.visually-hidden {
    position: absolute !important;
    height: 1px;
    width: 1px;
    overflow: hidden;
    clip: rect(1px 1px 1px 1px);
    clip: rect(1px, 1px, 1px, 1px);
    white-space: nowrap;
}

/* Focus styles for better keyboard navigation */
a:focus, button:focus, input:focus, select:focus, textarea:focus {
    outline: 2px solid var(--accent-color);
    outline-offset: 2px;
}

/* Dark mode toggle */
.dark-mode-toggle {
    background: none;
    border: none;
    color: var(--text-color);
    font-size: 1.5rem;
    cursor: pointer;
    transition: color 0.3s ease;
}

.dark-mode-toggle:hover {
    color: var(--accent-color);
}

/* Loading animation */
.loading {
    display: inline-block;
    width: 50px;
    height: 50px;
    border: 3px solid rgba(var(--accent-color), 0.3);
    border-radius: 50%;
    border-top-color: var(--accent-color);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
    </style>
</head>
<body>
    <header class="fixed-top">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    Welcome <?php echo $_SESSION['username']; ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <form class="d-flex mx-auto">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <!--<li class="nav-item">
                            <a class="nav-link" href="#">Upload Document</a>
                        </li>-->
                        <li class="nav-item">
                            <a class="nav-link" href="#">Languages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">My Translations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Help</a>
                        </li>
                    </ul>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../../assets/images/logo.jpg" alt="User" class="rounded-circle" width="30" height="30">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../../dashboard/admin/logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="">
                                <i class="fas fa-file-upload"></i> Upload Document
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my_documents.php">
                                <i class="fas fa-folder"></i> My Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-question-circle"></i> Support
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-star"></i> Favorites
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <span data-feather="calendar"></span>
                            This week
                        </button>
                    </div>
                </div>

                <form action="dashboard.php" method="post" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Document Upload</h5>
                                <?php if (isset($message)): ?>
                                    <div class="alert alert-info">
                                        <?php echo $message; ?>
                                    </div>
                                <?php endif; ?>
                                <div id="drop-area" class="border border-2 border-dashed p-5 text-center">
                                    <input type="file" name="document" id="document" accept=".docx,.rtf,.pdf,.txt,.xlsx,.ppt" onchange="handleFiles(this.files)" class="form-control" required>
                                    <input type="file" id="fileElem" accept=".docx,.rtf,.pdf,.txt,.xlsx,.ppt" onchange="handleFiles(this.files)" class="d-none">
                                    <button class="btn btn-primary" onclick="document.getElementById('fileElem').click()">Select Files</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Language Selection</h5>
                                <div class="row">
                                    <div class="col-md-5">
                                        <select class="form-select" id="sourceLanguage">
                                            <option selected>Source Language</option>
                                            <option value="en">English</option>
                                            <option value="es">Spanish</option>
                                            <option value="fr">French</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <div class="col-md-5">
                                        <select name="language" class="form-select" id="targetLanguage">
                                            <option selected>Target Language</option>
                                            <option value="fr">French</option>
                                            <option value="de">German</option>
                                            <option value="es">Spanish</option>
                                            <option value="pt-BR">Portuguese (Brazilian)</option>
                                            <option value="pt-PT">Portuguese</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-success w-100">Translate Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Translation Preview</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Original Text</h6>
                                        <textarea class="form-control" id="originalText" rows="10" readonly></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Translated Text</h6>
                                        <textarea class="form-control" id="translatedText" rows="10" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-dark">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="text-white">Quick Links</h5>
                    <ul class="list-unstyled text-small">
                        <li><a class="link-secondary" href="#">Terms of Service</a></li>
                        <li><a class="link-secondary" href="#">Privacy Policy</a></li>
                        <li><a class="link-secondary" href="#">Help</a></li>
                        <li><a class="link-secondary" href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="text-white">Connect With Us</h5>
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-facebook"></i></a></li>
                        <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-twitter"></i></a></li>
                        <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-linkedin"></i></a></li>
                        <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-github"></i></a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="text-white">Language</h5>
                    <select class="form-select" id="languageSelect">
                        <option value="en">English</option>
                        <option value="es">Español</option>
                        <option value="fr">Français</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <small class="text-white">&copy; 2023 Translation Dashboard. All rights reserved.</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript code will be added here

        // Dark mode toggle
const darkModeToggle = document.getElementById('darkModeToggle');
const body = document.body;

darkModeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    const isDarkMode = body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDarkMode);
    darkModeToggle.innerHTML = isDarkMode ? '☀️' : '🌙';
});

// Check for saved dark mode preference
if (localStorage.getItem('darkMode') === 'true') {
    body.classList.add('dark-mode');
    darkModeToggle.innerHTML = '☀️';
}

// File upload functionality
const dropArea = document.getElementById('drop-area');
const fileElem = document.getElementById('fileElem');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

function highlight() {
    dropArea.classList.add('highlight');
}

function unhighlight() {
    dropArea.classList.remove('highlight');
}

dropArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles(files);
}

function handleFiles(files) {
    ([...files]).forEach(uploadFile);
}

function uploadFile(file) {
    const reader = new FileReader();
    reader.readAsText(file);
    reader.onload = function(e) {
        document.getElementById('originalText').value = e.target.result;
    }
}


function mockTranslate(text, source, target) {
    // This is a very simple mock translation. Replace with actual API call.
    return `Translated from ${source} to ${target}: ${text.split('').reverse().join('')}`;
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Language selector functionality
const languageSelect = document.getElementById('languageSelect');
languageSelect.addEventListener('change', (e) => {
    // Here you would typically call a function to change the UI language
    console.log(`Language changed to ${e.target.value}`);
});

// Sidebar collapse functionality for mobile
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');

sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('show');
});

// Add fade-in animation to cards
document.querySelectorAll('.card').forEach(card => {
    card.classList.add('fade-in');
});

// Form validation
const form = document.querySelector('form');
form.addEventListener('submit', (e) => {
    e.preventDefault();
    // Add your form validation logic here
});

// Tooltip initialization (if using Bootstrap's tooltip component)
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
    </script>
</body>
</html>