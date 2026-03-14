<?php
	session_start();
	require 'database.php';

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	    $username = trim($_POST['username']);
	    $password = $_POST['password'];
	    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
	    $stmt->execute(['username' => $username]);
	    $user = $stmt->fetch(PDO::FETCH_ASSOC);

	    if ($user && password_verify($password, $user['password'])) {
		$_SESSION['user_id'] = $user['id'];
		$_SESSION['username'] = $user['username'];
		header("Location: index.php");
		exit();
	    } else {
		$error = "Invalid username or password.";
	    }
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="UTF-8">
	    <title>Login - Freedom Board</title>
	    <link rel="stylesheet" href="style.css"/>
	</head>
	<body>
	    <div class="nav">
		<div><a href="index.php">Home</a></div>
		<div><a href="register.php">Register</a></div>
	    </div>

	    <h2>Login</h2>
	    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
	    
	    <form action="login.php" method="POST">
		<input type="text" name="username" placeholder="Username" required>
		<input type="password" name="password" placeholder="Password" required>
		<button type="submit">Login</button>
	    </form>
	</body>
</html>
