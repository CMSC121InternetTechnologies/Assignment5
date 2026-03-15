<?php
    /*
     * MAIN FREEDOM BOARD PAGE
     * Handles the display of the UI, pagination, and the recursive rendering of nested message threads
     */

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
            <input type="hidden" name="current_page" value="<?php echo $page; ?>">
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
        
        <!-- Pagination Implementation -->
        <?php
            // Limit to 10 posts per page
            $posts_per_page = 10;
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

            $page = max(1, $page);

            $offset = ($page - 1) * $posts_per_page;

            // Count only top-level posts to determine the total number of pages
            $count_stmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE parent_id IS NULL");
            $total_top_posts = $count_stmt->fetchColumn();
            $total_pages = ceil($total_top_posts / $posts_per_page);

            // Fetch top-level posts for current page
            $sql_top = "SELECT posts.id, posts.content, posts.created_at, posts.user_id, posts.parent_id, users.username 
                        FROM posts 
                        JOIN users ON posts.user_id = users.id 
                        WHERE posts.parent_id IS NULL
                        ORDER BY posts.created_at DESC
                        LIMIT :limit OFFSET :offset";
            $stmt_top = $pdo->prepare($sql_top);
            $stmt_top->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
            $stmt_top->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt_top->execute();
            $top_posts = $stmt_top->fetchAll(PDO::FETCH_ASSOC);

            // Fetch all replies
            $sql_replies = "SELECT posts.id, posts.content, posts.created_at, posts.user_id, posts.parent_id, users.username 
                            FROM posts 
                            JOIN users ON posts.user_id = users.id 
                            WHERE posts.parent_id IS NOT NULL
                            ORDER BY posts.created_at ASC";
            $replies = $pdo->query($sql_replies)->fetchAll(PDO::FETCH_ASSOC);

            // Combine top posts and replies for processing
            $posts = array_merge($top_posts, $replies);

            // Nested replies correctness
            if (count($posts) > 0) {
                $posts_by_parent = [];
                $author_map = [];
                $fetched_ids = array_column($top_posts, 'id');

                // Group posts by parent_id
                foreach ($posts as $post) {
                    $parent = $post['parent_id'] ? $post['parent_id'] : 0;
                    
                    // If a reply belongs to a top-level post not on this page, ignore it
                    if($parent != 0 && !in_array($parent, $fetched_ids) && !isset($author_map[$parent])) continue;
                    
                    $posts_by_parent[$parent][] = $post;
                    $author_map[$post['id']] = $post['username'];
                }

                // Recursive function to display comments and their nested replies
                function display_comments($posts_by_parent, $author_map, $parent_id = 0, $level = 0) {
                    global $page;
                    if (isset($posts_by_parent[$parent_id])) {
                        $current_posts = $posts_by_parent[$parent_id];
                        
                        foreach ($current_posts as $post) {
                           
                    $margin = min($level * 30, 210); 
                            
                            echo "<div class='post thread-post' style='margin-left: {$margin}px;'>";
                            echo "<strong>" . htmlspecialchars($post['username']) . "</strong>" ;
                            
                            // Display "Replying to @username" context
                            if (!empty($post['parent_id']) && isset($author_map[$post['parent_id']])) {
                                $parent_name = $author_map[$post['parent_id']];
                                echo " <span class='reply-to-text'> Replying to @" . htmlspecialchars($parent_name) . "</span>";
                            }

                            echo "<p class='message-text'>" . htmlspecialchars($post['content']) . "</p>";
                            echo "<div class='meta'>Posted on: " . $post['created_at'];
                            
                            // Reply button for authenticated users
                            if (isset($_SESSION['user_id'])) {
                                echo " | <button type='button' class='reply-btn' onclick='toggleReplyForm(" . $post['id'] . ")'>Reply</button>";
                            }

                            // Delete Button for post owner only
                            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
                                echo " | <a href='delete_post.php?id=" . $post['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this post? All replies will also be deleted.\");'>Delete</a>";
                            }
                            echo "</div>"; 

                            // Hidden reply form
                            if (isset($_SESSION['user_id'])) {
                                echo "<form action='post_message.php' method='POST' class='reply-form' id='reply-form-" . $post['id'] . "'>";
                                echo "<input type='hidden' name='parent_id' value='" . $post['id'] . "'>";
                                echo "<input type='hidden' name='current_page' value='" . $page . "'>";
                                echo "<input type='text' id='reply-box' name='message' placeholder='Reply to " . htmlspecialchars($post['username']) . "...' required>";
                                echo "<button type='submit'>Reply</button>";
                                echo "</form>";
                            }

                            echo "</div>"; 

                            // Recursively call function to check for and display replies to this post
                            display_comments($posts_by_parent, $author_map, $post['id'], $level + 1);
                        }
                    }
                }
                
                // Start rendering from top-level posts (parent_id = 0)
                display_comments($posts_by_parent, $author_map, 0, 0);

            } else {
                echo "<p>No messages yet. Be the first to post!</p>";
            }

            // Pagination Controls
            if($total_pages > 1){
                echo "<div class='pagination'>";
                
                if($page > 1) echo "<a class='pagination-btn' href='?page=" . ($page - 1) . "'>&laquo; Previous </a>";
                
                echo "<span class='pagination-info'> Page $page of $total_pages </span>";
                
                if($page < $total_pages) echo "<a class='pagination-btn' href='?page=" . ($page + 1) . "'> Next &raquo;</a>";
                
                echo "</div>";
            }
        ?>
        <script src="script.js"></script>
    </body>
</html>