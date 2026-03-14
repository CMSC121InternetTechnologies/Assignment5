<?php
	$host = 'localhost';
	$dbname = 'freedomboard_db';
	$username = 'root';	// Change to actual username
	$password = '';		// Change to actual password

	try {
	    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
	    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
	    die("Database connection failed: " . $e->getMessage());
	}
?>
