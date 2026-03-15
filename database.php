<?php
	/*
     * DATABASE CONNECTION FILE
     * This file establishes a secure PDO connection to the MySQL database
     * It is required at the top of almost every other PHP file to interact with the database
     */
	
	$host = 'localhost';
	$dbname = 'freedomboard_db';
	$username = 'root';	// Change to actual username
	$password = '';	// Change to actual password

	try {
	    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);	// Create PDO instance and set the character set to utf8
	    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	// Configure PDO to throw exceptions on database errors
	} catch(PDOException $e) {
	    die("Database connection failed: " . $e->getMessage());	// Stop execution and display an error if the connection fails
	}
?>
