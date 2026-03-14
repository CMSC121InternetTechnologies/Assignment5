<?php
	session_start();
	require 'database.php';

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
	    $message = trim($_POST['message']);
	    $user_id = $_SESSION['user_id'];

	    if (!empty($message)) {
		$stmt = $pdo->prepare("INSERT INTO posts (user_id, content) VALUES (:user_id, :content)");
		$stmt->execute(['user_id' => $user_id, 'content' => $message]);
	    }
	}

	header("Location: index.php");
	exit();
?>
