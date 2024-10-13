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

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $username = $_POST['filename'];

    $stmt = $pdo->prepare("DELETE FROM documents WHERE filename = ?");
    if ($stmt->execute([$username])) {
        // Redirect back to the same page after deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

/// Get user data
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

/* Extend the existing dashboard styles */

/* Table styles */
.table {
    color: var(--text-color);
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,.05);
}

.table th {
    border-top: none;
    border-bottom: 2px solid var(--secondary-color);
}

/* Pagination styles */
.pagination .page-link {
    color: var(--primary-color);
    background-color: var(--background-color);
    border-color: var(--secondary-color);
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Search and filter styles */
#searchDocs, #filterStatus, #filterLanguage {
    margin-bottom: 1rem;
}

/* Modal styles */
.modal-content {
    background-color: var(--background-color);
    color: var(--text-color);
}

.modal-header {
    border-bottom-color: var(--secondary-color);
}

.modal-footer {
    border-top-color: var(--secondary-color);
}

/* Action button styles */
.btn-action {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .btn-toolbar {
        justify-content: center;
        margin-top: 1rem;
    }
}

/* Animation for new or updated rows */
@keyframes highlightRow {
    0% { background-color: var(--accent-color); }
    100% { background-color: transparent; }
}

.highlight-row {
    animation: highlightRow 2s ease-in-out;
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
                        <li class="nav-item">
                            <a class="nav-link" href="#">Upload Document</a>
                        </li>
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
                <!-- Sidebar content (same as dashboard) -->
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../../dashboard/user/dashboard.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <!--<li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-file-upload"></i> Upload Document
                            </a>
                        </li>-->
                        <li class="nav-item">
                            <a class="nav-link" href="#">
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
                    <h1 class="h2">My Documents</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="uploadNewDoc">Upload New</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshDocs">Refresh</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span data-feather="calendar"></span>
                            Sort by
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item sort-option" href="#" data-sort="name">Name</a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort="date">Date</a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort="status">Status</a></li>
                        </ul>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="searchDocs" placeholder="Search documents...">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filterStatus">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="in-progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filterLanguage">
                            <option value="">All Languages</option>
                            <option value="en">English</option>
                            <option value="es">Spanish</option>
                            <option value="fr">French</option>
                            <!-- Add more language options as needed -->
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date Uploaded</th>
                                <!--<th>Source Language</th>
                                <th>Target Language</th> -->
                                <th>File Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <?php
                            $query = "SELECT * FROM documents WHERE user_id = ?";
                            $stmt = $pdo->prepare($query);
                            $stmt->execute([$user_id]);
                            while ($doc = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <tbody>
                            <?php
                                echo "<tr>
                                <td>{$doc['filename']}</td>
                                <td>{$doc['created_at']}</td>
                                <td>{$doc['file_type']}</td>
                                <td>
                                    <input type='hidden' name='action' value='delete'>
                                    <button type='submit' class='btn btn-danger btn-sm'>Delete</button>"
                                    ?>
                                    <a href="../../dashboard/user/view.php?id=<?php echo htmlspecialchars($doc['id']); ?>"><button class='btn btn-approve me-2'>View Translations</button></a>
                                    <?php echo "</td>
                                </tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Documents pagination">
                    <ul class="pagination justify-content-center" id="documentsPagination">
                        <!-- Pagination will be dynamically inserted here -->
                    </ul>
                </nav>
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

        <!-- Modal for document actions -->
        <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalLabel">Document Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Modal content will be dynamically inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>

// Pagination settings
const itemsPerPage = 10;
let currentPage = 1;

// Sort and filter settings
let currentSort = { field: 'dateUploaded', direction: 'desc' };
let currentFilter = { search: '', status: '', language: '' };

// DOM elements
const documentsTableBody = document.getElementById('documentsTableBody');
const documentsPagination = document.getElementById('documentsPagination');
const searchDocs = document.getElementById('searchDocs');
const filterStatus = document.getElementById('filterStatus');
const filterLanguage = document.getElementById('filterLanguage');
const sortOptions = document.querySelectorAll('.sort-option');
const uploadNewDoc = document.getElementById('uploadNewDoc');
const refreshDocs = document.getElementById('refreshDocs');

// Event listeners
searchDocs.addEventListener('input', updateFilters);
filterStatus.addEventListener('change', updateFilters);
filterLanguage.addEventListener('change', updateFilters);
sortOptions.forEach(option => option.addEventListener('click', updateSort));
uploadNewDoc.addEventListener('click', showUploadModal);
refreshDocs.addEventListener('click', refreshDocuments);

// Initial render
renderDocuments();

function renderDocuments() {
    const filteredDocs = filterDocuments(documents);
    const sortedDocs = sortDocuments(filteredDocs);
    const paginatedDocs = paginateDocuments(sortedDocs);

    renderTable(paginatedDocs);
    renderPagination(filteredDocs.length);
}

function filterDocuments(docs) {
    return docs.filter(doc => 
        doc.name.toLowerCase().includes(currentFilter.search.toLowerCase()) &&
        (currentFilter.status === '' || doc.status === currentFilter.status) &&
        (currentFilter.language === '' || doc.sourceLanguage === currentFilter.language || doc.targetLanguage === currentFilter.language)
    );
}

function sortDocuments(docs) {
    return docs.sort((a, b) => {
        let comparison = 0;
        if (a[currentSort.field] > b[currentSort.field]) {
            comparison = 1;
        } else if (a[currentSort.field] < b[currentSort.field]) {
            comparison = -1;
        }
        return currentSort.direction === 'asc' ? comparison : -comparison;
    });
}

function paginateDocuments(docs) {
    const startIndex = (currentPage - 1) * itemsPerPage;
    return docs.slice(startIndex, startIndex + itemsPerPage);
}

function renderTable(docs) {
    documentsTableBody.innerHTML = '';
    docs.forEach(doc => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${doc.name}</td>
            <td>${doc.dateUploaded}</td>
            <td>${doc.sourceLanguage}</td>
            <td>${doc.targetLanguage}</td>
            <td>${doc.status}</td>
            <td>
                <button class="btn btn-sm btn-primary btn-action" onclick="viewDocument(${doc.id})">View</button>
                <button class="btn btn-sm btn-secondary btn-action" onclick="editDocument(${doc.id})">Edit</button>
                <button class="btn btn-sm btn-danger btn-action" onclick="deleteDocument(${doc.id})">Delete</button>
            </td>
        `;
        documentsTableBody.appendChild(row);
    });
}

function renderPagination(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    let paginationHTML = '';
    for (let i = 1; i <= totalPages; i++) {
        paginationHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>
        `;
    }
    documentsPagination.innerHTML = paginationHTML;
}

function updateFilters() {
    currentFilter = {
        search: searchDocs.value,
        status: filterStatus.value,
        language: filterLanguage.value
    };
    currentPage = 1;
    renderDocuments();
}

function updateSort(e) {
    e.preventDefault();
    const field = e.target.getAttribute('data-sort');
    if (field === currentSort.field) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort = { field, direction: 'asc' };
    }
    renderDocuments();
}

function changePage(page) {
    currentPage = page;
    renderDocuments();
}

function viewDocument(id) {
    const doc = documents.find(d => d.id === id);
    if (doc) {
        const modal = new bootstrap.Modal(document.getElementById('documentModal'));
        document.getElementById('documentModalLabel').textContent = 'View Document';
        document.querySelector('.modal-body').innerHTML = `
            <p><strong>Name:</strong> ${doc.name}</p>
            <p><strong>Date Uploaded:</strong> ${doc.dateUploaded}</p>
            <p><strong>Source Language:</strong> ${doc.sourceLanguage}</p>
            <p><strong>Target Language:</strong> ${doc.targetLanguage}</p>
            <p><strong>Status:</strong> ${doc.status}</p>
        `;
        document.getElementById('saveChanges').style.display = 'none';
        modal.show();
    }
}
// ... (previous code remains the same)

function editDocument(id) {
    const doc = documents.find(d => d.id === id);
    if (doc) {
        const modal = new bootstrap.Modal(document.getElementById('documentModal'));
        document.getElementById('documentModalLabel').textContent = 'Edit Document';
        document.querySelector('.modal-body').innerHTML = `
            <form id="editDocumentForm">
                <div class="mb-3">
                    <label for="editName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="editName" value="${doc.name}">
                </div>
                <div class="mb-3">
                    <label for="editSourceLanguage" class="form-label">Source Language</label>
                    <select class="form-select" id="editSourceLanguage">
                        <option value="en" ${doc.sourceLanguage === 'en' ? 'selected' : ''}>English</option>
                        <option value="es" ${doc.sourceLanguage === 'es' ? 'selected' : ''}>Spanish</option>
                        <option value="fr" ${doc.sourceLanguage === 'fr' ? 'selected' : ''}>French</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="editTargetLanguage" class="form-label">Target Language</label>
                    <select class="form-select" id="editTargetLanguage">
                        <option value="en" ${doc.targetLanguage === 'en' ? 'selected' : ''}>English</option>
                        <option value="es" ${doc.targetLanguage === 'es' ? 'selected' : ''}>Spanish</option>
                        <option value="fr" ${doc.targetLanguage === 'fr' ? 'selected' : ''}>French</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="editStatus" class="form-label">Status</label>
                    <select class="form-select" id="editStatus">
                        <option value="pending" ${doc.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="in-progress" ${doc.status === 'in-progress' ? 'selected' : ''}>In Progress</option>
                        <option value="completed" ${doc.status === 'completed' ? 'selected' : ''}>Completed</option>
                    </select>
                </div>
            </form>
        `;
        document.getElementById('saveChanges').style.display = 'block';
        document.getElementById('saveChanges').onclick = () => saveDocumentChanges(id);
        modal.show();
    }
}

function saveDocumentChanges(id) {
    const doc = documents.find(d => d.id === id);
    if (doc) {
        doc.name = document.getElementById('editName').value;
        doc.sourceLanguage = document.getElementById('editSourceLanguage').value;
        doc.targetLanguage = document.getElementById('editTargetLanguage').value;
        doc.status = document.getElementById('editStatus').value;
        
        renderDocuments();
        bootstrap.Modal.getInstance(document.getElementById('documentModal')).hide();
        showToast('Document updated successfully!');
    }
}

function deleteDocument(id) {
    if (confirm('Are you sure you want to delete this document?')) {
        documents = documents.filter(d => d.id !== id);
        renderDocuments();
        showToast('Document deleted successfully!');
    }
}

function showUploadModal() {
    const modal = new bootstrap.Modal(document.getElementById('documentModal'));
    document.getElementById('documentModalLabel').textContent = 'Upload New Document';
    document.querySelector('.modal-body').innerHTML = `
        <form id="uploadDocumentForm">
            <div class="mb-3">
                <label for="uploadName" class="form-label">Document Name</label>
                <input type="text" class="form-control" id="uploadName" required>
            </div>
            <div class="mb-3">
                <label for="uploadFile" class="form-label">File</label>
                <input type="file" class="form-control" id="uploadFile" required>
            </div>
            <div class="mb-3">
                <label for="uploadSourceLanguage" class="form-label">Source Language</label>
                <select class="form-select" id="uploadSourceLanguage" required>
                    <option value="en">English</option>
                    <option value="es">Spanish</option>
                    <option value="fr">French</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="uploadTargetLanguage" class="form-label">Target Language</label>
                <select class="form-select" id="uploadTargetLanguage" required>
                    <option value="en">English</option>
                    <option value="es">Spanish</option>
                    <option value="fr">French</option>
                </select>
            </div>
        </form>
    `;
    document.getElementById('saveChanges').style.display = 'block';
    document.getElementById('saveChanges').textContent = 'Upload';
    document.getElementById('saveChanges').onclick = uploadNewDocument;
    modal.show();
}

function uploadNewDocument() {
    const name = document.getElementById('uploadName').value;
    const file = document.getElementById('uploadFile').files[0];
    const sourceLanguage = document.getElementById('uploadSourceLanguage').value;
    const targetLanguage = document.getElementById('uploadTargetLanguage').value;

    if (name && file && sourceLanguage && targetLanguage) {
        const newDoc = {
            id: documents.length + 1,
            name: name,
            dateUploaded: new Date().toISOString().split('T')[0],
            sourceLanguage: sourceLanguage,
            targetLanguage: targetLanguage,
            status: 'pending'
        };
        documents.unshift(newDoc);
        renderDocuments();
        bootstrap.Modal.getInstance(document.getElementById('documentModal')).hide();
        showToast('Document uploaded successfully!');
    } else {
        showToast('Please fill in all fields', 'error');
    }
}

function refreshDocuments() {
    // In a real application, this would fetch the latest documents from the server
    // For this mock-up, we'll just re-render the existing documents
    renderDocuments();
    showToast('Documents refreshed!');
}

function showToast(message, type = 'success') {
    const toastContainer = document.createElement('div');
    toastContainer.style.position = 'fixed';
    toastContainer.style.top = '20px';
    toastContainer.style.right = '20px';
    toastContainer.style.zIndex = '9999';

    const toastElement = document.createElement('div');
    toastElement.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');

    toastElement.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toastElement);
    document.body.appendChild(toastContainer);

    const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
    toast.show();

    toastElement.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toastContainer);
    });
}

// Dark mode toggle (extend from dashboard.js)
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

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
    </script>
</body>
</html>