<?php
    /*
     * DELETE POST LOGIC
     * Allow user to delete their own posts
     */

    session_start();
    require 'database.php';

    // Verify required GET parameter if user is logged in
    if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
        $post_id = $_GET['id'];
        $user_id = $_SESSION['user_id'];
        
        // Make sure user can only delete a post if their session ID matches post user_id
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $post_id, 'user_id' => $user_id]);
    }

    // Redirect back to board
    header("Location: index.php");
    exit();
?>