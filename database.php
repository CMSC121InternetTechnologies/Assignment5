<?php
	$host = 'localhost';
	$dbname = 'freedomboard_db';
	$username = 'root';	// Change to actual username
	$password = '';			// Change to actual password

	try {
	    // Create a PDO instance
	    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
	    
	    // Set PDO error mode to exception so errors can easily be seen
	    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
	    die("Database connection failed: " . $e->getMessage());
	}
?>
