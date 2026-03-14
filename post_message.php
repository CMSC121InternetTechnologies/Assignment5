<?php
    session_start();
    require 'database.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
        $message = trim($_POST['message']);
        $user_id = $_SESSION['user_id'];
        
        // 1. Capture the parent_id from the form. 
        // If it doesn't exist or is empty, default it to null (top-level post).
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        if (!empty($message)) {
            // 2. Update the SQL statement to include the parent_id column
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, parent_id) VALUES (:user_id, :content, :parent_id)");
            
            // 3. Pass the parent_id into the execution array
            $stmt->execute([
                'user_id' => $user_id, 
                'content' => $message,
                'parent_id' => $parent_id
            ]);
        }
    }

    header("Location: index.php");
    exit();
?>