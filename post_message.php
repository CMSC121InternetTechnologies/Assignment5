<?php
    /*
     * CREATE POST/REPLY LOGIC
     * Handles insertion of new messages into database
     * Supports both top-level posts and nested replies
     */
    session_start();
    require 'database.php';

    // Ensure it's a POST request and user is authenticated
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
        $message = trim($_POST['message']);
        $user_id = $_SESSION['user_id'];
        
        // Capture parent_id from form
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        if (!empty($message)) {
            // Prepare the INSERT statement with prepared parameters to prevent SQL injection
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, parent_id) VALUES (:user_id, :content, :parent_id)");
            
            $stmt->execute([
                'user_id' => $user_id, 
                'content' => $message,
                'parent_id' => $parent_id
            ]);
        }
    }

    // Redirect back to board regardless
    header("Location: index.php");
    exit();
?>