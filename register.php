<?php
	session_start();
	require 'database.php';

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	    $username = trim($_POST['username']);
	    $password = $_POST['password'];

	    if (!empty($username) && !empty($password)) {
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
		
		try {
		    $stmt->execute(['username' => $username, 'password' => $hashed_password]);
		    $_SESSION['user_id'] = $pdo->lastInsertId();
		    $_SESSION['username'] = $username;
		    
		    header("Location: index.php");
		    exit();
		} catch (PDOException $e) {
		    $error = "Username already exists. Please choose another.";
		}
	    } else {
		$error = "Please fill in all fields.";
	    }
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="UTF-8">
	    <title>Register - Freedom Board</title>
	    <link rel="stylesheet" href="style.css"/>
	</head>
	<body>
	    <div class="nav">
		<div><a href="index.php">Home</a></div>
		<div><a href="login.php">Login</a></div>
	    </div>

	    <h2>Register</h2>
	    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
	    
	    <form action="register.php" method="POST">
		<input type="text" name="username" placeholder="Choose a Username" required>
		<input type="password" name="password" placeholder="Choose a Password" required>
		<button type="submit">Register</button>
	    </form>
	</body>
</html>
