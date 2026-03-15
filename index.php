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
            $sql = "SELECT posts.id, posts.content, posts.created_at, posts.user_id, posts.parent_id, users.username 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                ORDER BY posts.created_at ASC";
            $stmt = $pdo->query($sql);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($posts) > 0) {
                $posts_by_parent = [];
				$author_map = [];

                foreach ($posts as $post) {
                    $parent = $post['parent_id'] ? $post['parent_id'] : 0;
                    $posts_by_parent[$parent][] = $post;

					$author_map[$post['id']] = $post['username'];
                }

                function display_comments($posts_by_parent, $author_map,$parent_id = 0, $level = 0) {
                    if (isset($posts_by_parent[$parent_id])) {
                        
                        $current_posts = $posts_by_parent[$parent_id];
                        if ($parent_id == 0) {
                            $current_posts = array_reverse($current_posts);
                        }

                        foreach ($current_posts as $post) {
                            $margin = $level * 30; 
                            
                            echo "<div class='post thread-post' style='margin-left: {$margin}px;'>";
                            echo "<strong>" . htmlspecialchars($post['username']) . "</strong>" ;
							
							if (!empty($post['parent_id']) && isset($author_map[$post['parent_id']])) {
                                $parent_name = $author_map[$post['parent_id']];
                                echo " <span class='reply-to-text'> Replying to @" . htmlspecialchars($parent_name) . "</span>";
                            }

							echo "<p class='message-text'>" . htmlspecialchars($post['content']) . "</p>";
                            
                            echo "<div class='meta'>Posted on: " . $post['created_at'];
                            
							if (isset($_SESSION['user_id'])) {
								echo " | <button type='button' class='reply-btn' onclick='toggleReplyForm(" . $post['id'] . ")'>Reply</button>";
							}

                            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
                                echo " | <a href='delete_post.php?id=" . $post['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this post?\");'>Delete</a>";
                            }
                            echo "</div>"; 

                            if (isset($_SESSION['user_id'])) {
                                echo "<form action='post_message.php' method='POST' class='reply-form' id='reply-form-" . $post['id'] . "'>";
                                echo "<input type='hidden' name='parent_id' value='" . $post['id'] . "'>";
                                echo "<input type='text' id = 'reply-box' name='message' placeholder='Reply to " . htmlspecialchars($post['username']) . "...' required>";
                                echo "<button type='submit'>Reply</button>";
                                echo "</form>";
                            }

                            echo "</div>"; 

                           display_comments($posts_by_parent, $author_map, $post['id'], $level + 1);
                        }
                    }
                }

                display_comments($posts_by_parent, $author_map, 0, 0);

            } else {
                echo "<p>No messages yet. Be the first to post!</p>";
            }
        ?>
		<script src = "script.js"> </script>
    </body>
</html>