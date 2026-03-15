<?php
    session_start();
    require 'database.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
        $message = trim($_POST['message']);
        $user_id = $_SESSION['user_id'];
        
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        
        // 1. Capture the current page from the form (default to 1 if it is missing)
        $current_page = !empty($_POST['current_page']) ? (int)$_POST['current_page'] : 1;

        if (!empty($message)) {
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, parent_id) VALUES (:user_id, :content, :parent_id)");
            
            $stmt->execute([
                'user_id' => $user_id, 
                'content' => $message,
                'parent_id' => $parent_id
            ]);
        }
        
        // 2. Redirect back to the exact page the user was on!
        header("Location: index.php?page=" . $current_page);
        exit();
    }

    header("Location: index.php");
    exit();
?>