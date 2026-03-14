<?php
	session_start();
	require 'database.php';
?>

<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="UTF-8">
	    <title>Freedom Board</title>
	    <link rel="stylesheet" href="style.css"/>
	</head>
	<body>
	    <div class="nav">
		<div>
		    <a href="index.php">Home</a>
		</div>
		<div>
		    <?php if (isset($_SESSION['user_id'])): ?>
		        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! 
		        <a href="logout.php" style="margin-left: 15px;">Logout</a>
		    <?php else: ?>
		        <a href="register.php">Register</a>
		        <a href="login.php">Login</a>
		    <?php endif; ?>
		</div>
	    </div>

	    <h1>Freedom Board</h1>

	    <?php if (isset($_SESSION['user_id'])): ?>
		<form action="post_message.php" method="POST">
		    <textarea name="message" placeholder="What's on your mind?" required></textarea>
		    <button type="submit">Post Message</button>
		</form>
	    <?php else: ?>
		<div style="background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
		    <p>Please <a href="login.php">Login</a> or <a href="register.php">Register</a> to post a message.</p>
		</div>
	    <?php endif; ?>

	    <hr>
	    <h2>Recent Messages</h2>
	    
	    <?php
		    $sql = "SELECT posts.id, posts.content, posts.created_at, posts.user_id, users.username 
			    FROM posts 
			    JOIN users ON posts.user_id = users.id 
			    ORDER BY posts.created_at DESC";
		    $stmt = $pdo->query($sql);
		    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

		    if (count($posts) > 0) {
			foreach ($posts as $post) {
			    echo "<div class='post'>";
			    echo "<strong>" . htmlspecialchars($post['username']) . "</strong>: " . htmlspecialchars($post['content']);
			    echo "<div class='meta'>Posted on: " . $post['created_at'];
			    
			    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
				echo "<a href='delete_post.php?id=" . $post['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this post?\");'>Delete</a>";
			    }
			    
			    echo "</div>";
			    echo "</div>";
			}
		    } else {
			echo "<p>No messages yet. Be the first to post!</p>";
		    }
	    ?>
	</body>
</html>
