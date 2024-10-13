<?php
session_start();
require '../../includes/config.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../auth/login.php');
    exit();
}

// Check if the user's role is "Reviewer"
if ($_SESSION['role'] !== 'User') {
    header('Location: ../../auth/login.php'); // or another appropriate page
    exit();
}

$translation_id = $_GET['id'] ?? null;

// Get document_id from the URL
if (isset($_GET['id'])) {
    $document_id = intval($_GET['id']);

    // Prepare the SQL query to get translation details based on document_id
    $query = "
        SELECT 
            d.filename AS document_title,
            CONCAT(t.source_language, ' -> ', t.target_language) AS languages,
            t.original_text,
            t.translated_text
        FROM translations t
        JOIN documents d ON t.document_id = d.id
        WHERE t.document_id = :document_id;
    ";

    // Prepare and execute the statement
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
    $stmt->execute();
    $translationDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if we found any translation details
    if ($translationDetails) {

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Translation System - Translation Review</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1a73e8;
            --secondary-blue: #4285f4;
            --accent-green: #34a853;
            --accent-orange: #fbbc05;
            --accent-red: #ea4335;
            --background-light: #f8f9fa;
            --text-primary: #333333;
            --text-secondary: #666666;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-light);
            color: var(--text-primary);
        }

        /* Header Styles */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand img {
            height: 40px;
        }

        /* Sidebar Styles */
        .sidebar {
            background-color: white;
            height: calc(100vh - 56px);
            position: fixed;
            padding-top: 20px;
            box-shadow: 2px 0 4px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            padding: 10px 15px;
            color: var(--text-secondary);
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--background-light);
            color: var(--primary-blue);
            border-left: 3px solid var(--primary-blue);
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .translation-header {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .translation-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .translation-panel {
            flex: 1;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .translation-text {
            height: 500px;
            overflow-y: auto;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .highlight {
            background-color: #ffeeba;
            padding: 2px 4px;
            border-radius: 3px;
            cursor: pointer;
        }

        .feedback-panel {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-approve {
            background-color: var(--accent-green);
            border-color: var(--accent-green);
            color: white;
        }

        .btn-reject {
            background-color: var(--accent-red);
            border-color: var(--accent-red);
            color: white;
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .dark-mode .navbar,
        .dark-mode .sidebar,
        .dark-mode .translation-header,
        .dark-mode .translation-panel,
        .dark-mode .feedback-panel {
            background-color: #2d2d2d;
            color: #ffffff;
        }

        .dark-mode .translation-text {
            background-color: #1a1a1a;
            border-color: #404040;
            color: #ffffff;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="/api/placeholder/120/40" alt="Logo">
            </a>
            <div class="d-flex align-items-center">
                <div class="dropdown me-3">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="/api/placeholder/32/32" alt="Profile" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                <button class="btn btn-link" id="darkModeToggle">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar d-none d-lg-block">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-home me-2"></i>Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#"><i class="fas fa-tasks me-2"></i>Pending Reviews</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-history me-2"></i>Review History</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-chart-bar me-2"></i>Statistics</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid fade-in">
            <!-- Translation Header -->
            <div class="translation-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <p>
                            <br>
                        <h1 class="mb-2">Review Translation #<?php echo $translation_id; ?></h1>
                        <p class="mb-0"><strong>Document:</strong> <?php echo htmlspecialchars($translationDetails['document_title']); ?></p>
                        <p class="mb-0"><strong>Languages:</strong><?php echo htmlspecialchars($translationDetails['languages']); ?></p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <button class="btn btn-approve me-2"><i class="fas fa-check me-2"></i>Approve</button>
                        <button class="btn btn-reject"><i class="fas fa-times me-2"></i>Reject</button>
                    </div>
                </div>
            </div>

            <!-- Translation Panels -->
            <div class="translation-container">
                <!-- Original Text Panel -->
                <div class="translation-panel">
                    <h5 class="mb-3">Original Text</h5>
                    <div class="translation-text">
                        <?php echo nl2br(htmlspecialchars($translationDetails['original_text'])); ?>
                    </div>
                </div>

                <!-- Translated Text Panel -->
                <div class="translation-panel">
                    <h5 class="mb-3">Translated Text</h5>
                    <div class="translation-text">
                        <?php echo nl2br(htmlspecialchars($translationDetails['translated_text'])); ?>
                </div>
            </div>
            <?php
            } else {
                echo "<p>No translation details found for the specified document ID.</p>";
            }
        } else {
            echo "<p>No document ID specified.</p>";
        }
            ?>
            <!-- Feedback Panel -->
            <div class="feedback-panel">
                <h5 class="mb-3">Feedback</h5>
                <div class="mb-3">
                    <label for="feedbackType" class="form-label">Type of Feedback</label>
                    <select class="form-select" id="feedbackType">
                        <option>Grammar</option>
                        <option>Terminology</option>
                        <option>Style</option>
                        <option>Accuracy</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="feedbackText" class="form-label">Comments</label>
                    <textarea class="form-control" id="feedbackText" rows="3" placeholder="Enter your feedback here..."></textarea>
                </div>
                <button class="btn btn-primary">Add Feedback</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dark Mode Toggle
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });

        // Highlight functionality
        document.querySelectorAll('.highlight').forEach(element => {
            element.addEventListener('click', function() {
                // Toggle highlight class
                this.classList.toggle('active');
                
                // Here you could add functionality to add a comment
                // or link this highlighted text to feedback
            });
        });
    </script>
</body>
</html>
