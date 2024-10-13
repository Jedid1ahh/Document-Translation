<?php
function approveTranslation($review_id, $translated_text) {
    require 'config.php';

    // Update the translation to approved status
    $query = "UPDATE translations SET translated_text = ?, status = 'approved', updated_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$translated_text, $review_id]);

    // Get the translator ID from the translation record
    $query = "SELECT translator_id FROM translations WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$review_id]);
    $translator = $stmt->fetch(PDO::FETCH_ASSOC);

    // Create an in-app notification for the translator
    $notificationMessage = "Your translation for review ID $review_id has been approved.";
    createNotification($translator['translator_id'], $notificationMessage);
}

function rejectTranslation($review_id, $translated_text, $feedback) {
    global $db;

    // Update the translation to rejected status and add feedback
    $query = "UPDATE translations SET translated_text = ?, feedback = ?, status = 'rejected', updated_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$translated_text, $feedback, $review_id]);

    // Get the translator ID from the translation record
    $query = "SELECT translator_id FROM translations WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$review_id]);
    $translator = $stmt->fetch(PDO::FETCH_ASSOC);

    // Create an in-app notification for the translator
    $notificationMessage = "Your translation for review ID $review_id has been rejected. Feedback: $feedback";
    createNotification($translator['translator_id'], $notificationMessage);
}

// Helper function to create a notification
function createNotification($user_id, $message) {
    require 'config.php';

    $query = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $message]);
}

?>