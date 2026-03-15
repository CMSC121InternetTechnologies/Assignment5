<?php
    /*
     * LOGOUT SCRIPT
     * Destroy session and clear session cookies
     */
    
    session_start();

    // Clear all session variables in $_SESSION array
    $_SESSION = array();

    // Delete actual session cookie from user's browser
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();  // Destroy session data on server

    // Redirect user back to login page
    header("Location: login.php");
    exit();
?>