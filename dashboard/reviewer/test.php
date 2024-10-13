<?php
session_start();
require '../../includes/config.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../auth/login.php');
    exit();
}


$reviewer = $_SESSION['user_id']; // Get the reviewer ID from the session
$translation_id = $_GET['id']; // Get the translation ID from the query string

// Prepare the SQL query to get translation details based on the translation ID
$query = "
    SELECT 
        t.id AS translation_id,
        d.filename AS document_title,
        u.username AS translator,
        CONCAT(t.source_language, ' => ', t.target_language) AS languages,
        t.original_text,
        t.translated_text
    FROM translations t
    JOIN documents d ON t.document_id = d.id
    JOIN users u ON d.user_id = u.id
    WHERE t.id = :translation_id;
";

// Prepare and execute the statement
$stmt = $pdo->prepare($query);
$stmt->bindParam(':translation_id', $translation_id, PDO::PARAM_INT);
$stmt->execute();

// Fetch the translation details
$translation = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if translation details were found
if ($translation) {
    echo "<h2>Translation Details</h2>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Translation ID</th>
            <th>Document Title</th>
            <th>Translator</th>
            <th>Languages</th>
            <th>Original Text</th>
            <th>Translated Text</th>
          </tr>";
    
    echo "<tr>
            <td>" . htmlspecialchars($translation['translation_id']) . "</td>
            <td>" . htmlspecialchars($translation['document_title']) . "</td>
            <td>" . htmlspecialchars($translation['translator']) . "</td>
            <td>" . htmlspecialchars($translation['languages']) . "</td>
            <td>" . nl2br(htmlspecialchars($translation['original_text'])) . "</td>
            <td>" . nl2br(htmlspecialchars($translation['translated_text'])) . "</td>
          </tr>";
    
    echo "</table>";
} else {
    echo "<p>No translation details found for the specified translation ID.</p>";
}
?>
