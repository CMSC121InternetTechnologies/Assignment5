<?php
	session_start();
	require 'database.php';

	if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
	    $post_id = $_GET['id'];
	    $user_id = $_SESSION['user_id'];
	    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id AND user_id = :user_id");
	    $stmt->execute(['id' => $post_id, 'user_id' => $user_id]);
	}

	header("Location: index.php");
	exit();
?>
