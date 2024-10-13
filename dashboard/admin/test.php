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

$user_id = $_GET['id'] ?? null; // Get the user ID from the URL

if ($user_id) {
    // Fetch translation logs for the specified user
    $query = "SELECT tl.id, t.document_id, tl.action, tl.created_at, u.username
              FROM translation_logs tl
              JOIN translations t ON tl.translation_id = t.id
              JOIN users u ON tl.user_id = u.id
              WHERE u.id = :user_id"; // Use a named placeholder for PDO

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Use bindParam with PDO
    $stmt->execute(); // Execute the query

    // Fetch the results
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // You can now process $result as needed
} else {
    // If no user ID is provided, redirect to the dashboard
    header("Location: ../../dashboard/admin/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TranslateAdmin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #818cf8;
            --accent-color: #f472b6;
            --background-light: #f3f4f6;
            --text-light: #1f2937;
            --background-dark: #111827;
            --text-dark: #f9fafb;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s, color 0.3s;
            background-color: var(--background-light);
            color: var(--text-light);
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--primary-color);
            color: white;
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar.collapsed {
            width: 80px;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.2s;
            border-radius: 8px;
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.2em;
        }
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .main-content.expanded {
            margin-left: 80px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        .chart-card {
            background-color: white;
        }
        #toggleSidebar {
            cursor: pointer;
            font-size: 1.5em;
            color: var(--primary-color);
        }
        .chart-container {
            height: 300px;
        }
        .search-bar {
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
        }
        .search-bar:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
            border-color: var(--primary-color);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        .user-menu {
            position: relative;
        }
        
        .user-menu .dropdown-toggle::after {
            display: none;
        }
        
        .user-menu .dropdown-menu {
            right: 0;
            left: auto;
            margin-top: 0.5rem;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
        }
        
        .user-menu .dropdown-item {
            padding: 0.5rem 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .user-menu .dropdown-item i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }
        
        .user-menu .dropdown-divider {
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="p-3 text-center">
                <h3 class="h4 mb-0">TranslateAdmin</h3>
            </div>
            <ul class="nav flex-column p-3">
                <li class="nav-item"><a href="../../dashboard/admin/dashboard.php" class="nav-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <!--<li class="nav-item"><a href="view_users.php" class="nav-link"><i class="fas fa-users"></i> <span>Users</span></a></li>-->
                <li class="nav-item"><a href="../../dashboard/admin/translogs.php" class="nav-link"><i class="fas fa-language"></i> <span>Translations Logs</span></a></li>
                <li class="nav-item"><a href="" class="nav-link"><i class="fas fa-language"></i> <span>Translations Management</span></a></li>
                <li class="nav-item"><a href="upload_document.php" class="nav-link"><i class="fas fa-file-alt"></i> <span>Documents Management</span></a></li>
                <li class="nav-item"><a href="view_translations.php" class="nav-link"><i class="fas fa-language"></i> <span>My Translations</span></a></li>
            </ul>
            <div class="mt-auto p-3">
                <button id="toggleTheme" class="btn btn-light w-100"><i class="fas fa-moon"></i> Dark Mode</button>
            </div>
        </nav>

        <!-- Main content -->
        <div id="main-content" class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <i id="toggleSidebar" class="fas fa-bars me-3"></i>
                    <h1 class="h3 mb-0">Dashboard Overview</h1>
                </div>
                <div class="d-flex align-items-center">
                    <input type="text" class="form-control search-bar me-3" placeholder="Search...">
                    <div class="user-menu dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../../assets/images/logo.jpg" alt="User Avatar" class="rounded-circle user-avatar">
                        </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-chart-bar"></i> Reporting</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-shield-alt"></i> Security</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-white-50">Total Users</h5>
                            <p class="card-text display-5 fw-bold"><?php echo $totalUsers; ?></p>
                            <p class="mb-0"><i class="fas fa-arrow-up me-2"></i>5% increase</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-white-50">Active Translations</h5>
                            <p class="card-text display-5 fw-bold">56</p>
                            <p class="mb-0"><i class="fas fa-arrow-down me-2"></i>2% decrease</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-white-50">Completed Translations</h5>
                            <p class="card-text display-5 fw-bold"><?php echo $totalTranslations; ?></p>
                            <p class="mb-0"><i class="fas fa-equals me-2"></i>No change</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-white-50">Pending Reviews</h5>
                            <p class="card-text display-5 fw-bold">7</p>
                            <p class="mb-0"><i class="fas fa-arrow-up me-2"></i>3% increase</p>
                        </div>
                    </div>
                </div>
            </div>
    </header>
            <header class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Translation Management</h1>
            </header>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                               <tr>
                                    <th>Log ID</th>
                                    <th>Document ID</th>
                                    <th>Action</th>
                                    <th>Date</th>
                                    <th>Username</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($result) > 0): ?>  <!-- Use count() to check the number of rows -->
                                <?php foreach ($result as $log): ?> <!-- Use foreach for iterating over the array -->
                                <tr>
                                    <td><?php echo htmlspecialchars($log['id']); ?></td>
                                    <td><?php echo htmlspecialchars($log['document_id']); ?></td>
                                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                                    <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($log['username']); ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($log['username']); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?> <!-- End of foreach loop -->
                                <?php else: ?>
                                <tr>
                                    <td colspan="5">No logs found for this user.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="User list pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Toggle sidebar
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleSidebarBtn = document.getElementById('toggleSidebar');
        
        const toggleSidebar = () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        };
        
        toggleSidebarBtn.addEventListener('click', toggleSidebar);

        // Toggle theme
        const toggleThemeBtn = document.getElementById('toggleTheme');
        const body = document.body;
        let isDarkMode = false;

        toggleThemeBtn.addEventListener('click', () => {
            isDarkMode = !isDarkMode;
            body.style.backgroundColor = isDarkMode ? 'var(--background-dark)' : 'var(--background-light)';
            body.style.color = isDarkMode ? 'var(--text-dark)' : 'var(--text-light)';
            
            const chartCard = document.querySelector('.chart-card');
            chartCard.style.backgroundColor = isDarkMode ? 'var(--background-dark)' : 'white';
            chartCard.style.color = isDarkMode ? 'var(--text-dark)' : 'var(--text-light)';
            
            toggleThemeBtn.innerHTML = isDarkMode ? '<i class="fas fa-sun"></i> Light Mode' : '<i class="fas fa-moon"></i> Dark Mode';
            toggleThemeBtn.classList.toggle('btn-light');
            toggleThemeBtn.classList.toggle('btn-dark');
        });

        // Chart
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Translations',
                    data: [40, 30, 60, 40, 70, 50, 30],
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>