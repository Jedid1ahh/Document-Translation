<?php
session_start();
require '../../includes/config.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../auth/login.php');
    exit();
}

// Check if the user's role is "Reviewer"
if ($_SESSION['role'] !== 'Reviewer') {
    header('Location: ../../auth/login.php'); // or another appropriate page
    exit();
}


$reviewer = $_SESSION['user_id']; // Store the reviewer ID from the session

// Prepare the SQL query to get translation reviews for the current reviewer
$query = "
    SELECT 
        d.id AS document_id,
        d.filename AS title,
        u.username AS translator,
        CONCAT(t.source_language, ' => ', t.target_language) AS languages,
        t.created_at AS submitted,
        r.feedback AS action
    FROM translation_reviews r
    JOIN translations t ON r.translation_id = t.id
    JOIN documents d ON t.document_id = d.id
    JOIN users u ON d.user_id = u.id
    WHERE r.reviewer_id = :reviewer_id;
";

// Prepare and execute the statement
$stmt = $pdo->prepare($query);
$stmt->bindParam(':reviewer_id', $reviewer, PDO::PARAM_INT);
$stmt->execute();
$translations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$translation_id = $_GET['id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Translate Reviewer Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1a73e8;
            --secondary-blue: #4285f4;
            --accent-green: #34a853;
            --accent-orange: #fbbc05;
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

        .nav-link {
            color: var(--text-secondary);
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-blue);
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

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-success {
            background-color: var(--accent-green);
            border-color: var(--accent-green);
        }

        /* Statistics Cards */
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .stat-card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        /* Pending Reviews Table */
        .table th {
            border-top: none;
            color: var(--text-secondary);
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .dark-mode .navbar,
        .dark-mode .sidebar,
        .dark-mode .card {
            background-color: #2d2d2d;
            color: #ffffff;
        }

        .dark-mode .nav-link {
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
                <form class="d-flex mx-4">
                    <input class="form-control me-2" type="search" placeholder="Search translations..." aria-label="Search">
                </form>
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="" alt="Profile" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
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
                <a class="nav-link active" href="#"><i class="fas fa-home"></i>Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-tasks"></i>Pending Reviews</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-history"></i>Review History</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-chart-bar"></i>Statistics</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-cog"></i>Settings</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid fade-in">
            <h1 class="mb-4">Reviewer Dashboard</h1>
            
            <!-- Statistics Row -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body">
                            <i class="fas fa-clock"></i>
                            <h5 class="card-title">Pending Reviews</h5>
                            <h2>12</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <i class="fas fa-check-circle"></i>
                            <h5 class="card-title">Completed Today</h5>
                            <h2>8</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <i class="fas fa-calendar-check"></i>
                            <h5 class="card-title">This Week</h5>
                            <h2>47</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-warning text-white">
                        <div class="card-body">
                            <i class="fas fa-star"></i>
                            <h5 class="card-title">Accuracy Rate</h5>
                            <h2>98%</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Reviews Table -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pending Reviews</h5>
                    <button class="btn btn-primary btn-sm">View All</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                
                                <tr>
                                    <th>Document ID</th>
                                    <th>Title</th>
                                    <th>Translator</th>
                                    <th>Languages</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($translations): ?>
                                <?php foreach ($translations as $translation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($translation['document_id']); ?></td>
                                    <td><?php echo htmlspecialchars($translation['title']); ?></td>
                                    <td><?php echo htmlspecialchars($translation['translator']); ?></td>
                                    <td><?php echo htmlspecialchars($translation['languages']); ?></td>
                                    <td><?php echo htmlspecialchars($translation['submitted']); ?></td>
                                    <td>
                                        <a href="../../dashboard/reviewer/review_translation.php?id=<?php echo htmlspecialchars($translation['document_id']); ?>"><button class="btn btn-primary btn-sm">Review</button></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="6">No translation reviews assigned.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>
