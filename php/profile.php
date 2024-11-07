<?php
    session_start();

    if (!isset($_SESSION['username']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) {
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    }

    $user_name = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sort_by = 'post.post_id DESC'; // Default sort by date
    if (isset($_GET['sort']) && $_GET['sort'] == 'likes') {
        $sort_by = 'post_like_count DESC'; // Sort by likes
    }

    // Update post_like_count on post table
    $update_sql = "
        UPDATE post 
        SET post_like_count = (
            SELECT COUNT(*) 
            FROM Likes 
            WHERE Likes.post_id = post.post_id
        )
    ";

    $conn->query($update_sql);

    $sql = "
        SELECT post.*, user.user_name, user.user_profile_picture
        FROM post 
        JOIN user ON post.user_id = user.user_id 
        WHERE post.user_id = $user_id
        ORDER BY $sort_by
    ";

    $profile_pic_sql = "
        SELECT user_profile_picture
        FROM user  
        WHERE user_id = $user_id
    ";
    // Query to get total likes
    $total_likes_sql = "
        SELECT COUNT(*) AS total_likes 
        FROM Likes
        WHERE post_id IN (SELECT post_id FROM post WHERE user_id = $user_id)
    ";

    $result = $conn->query($sql);
    $result2 = $conn->query($profile_pic_sql);
    $total_likes_result = $conn->query($total_likes_sql);

    // Fetch total likes
    $total_likes = 0;
    if ($total_likes_result && $total_likes_row = $total_likes_result->fetch_assoc()) {
        $total_likes = $total_likes_row['total_likes'];
    }
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/RecipeBook/Recipe-Book/css/styles.css">
    </head>
    <body>
    
        <nav class="navbar">
            <div class="logo">
                <img src="/RecipeBook/Recipe-Book/logo/logo2.JPG" onclick="about()" style="width: 120px; height: 100px; border-radius: 50%;"/>&nbsp;&nbsp;
                <h1  onclick="window.location.href='/RecipeBook/Recipe-Book/php/home.php'" class="recipebook">Recipebook</h1>
            </div>
            <button class="home-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/php/home.php'">Home</button>

            <div class="leftside-bar">
                <button class="favc-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/php/favourite_functionality/favourite_page.php'">My Favourites<img src="/RecipeBook/Recipe-Book/buttons/fav_button.png" height="20px" style="margin-left:3px"/></button>
                <button class="setting-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/html/manage_profile/settings.html'">Settings</button>
            </div>
        </nav>
        
        <div class="heading">
            <a href="/RecipeBook/Recipe-Book/php/profile.php"><?php
                    if ($result2->num_rows==1) {
                        while($row = $result2->fetch_assoc()) {
                            if (($row['user_profile_picture'])) {
                                echo "<img src='data:image/jpeg;base64,".base64_encode($row['user_profile_picture'])."' alt='Profile picture' style='max-width: 300px; max-height: 150px; border-radius: 50%; margin-right: 10px;'/>";
                            } else {
                                echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='max-width: 300px; max-height: 150px; border-radius: 50%; margin-right: 10px;'/>";
                            }
                        }
                    }
                    ?>
                </a>
            <h2><?php echo "$user_name" ?></h2>
            <p><?php echo "Total likes: " . $total_likes; ?></p>
            <h2>All your posts</h2>

            <form id="sortForm" method="GET" action="">
                <label for="sort">Sort by:</label>
                <select id="sort" name="sort" onchange="document.getElementById('sortForm').submit();">
                    <option value="date" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date') ? 'selected' : ''; ?>>Date</option>
                    <option value="likes" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'likes') ? 'selected' : ''; ?>>Likes</option>
                </select>
            </form>
        </div>

        <a href="/RecipeBook/Recipe-Book/html/post_functionality/add_post.html"><img  class="add_recipe" src="/RecipeBook/Recipe-Book/buttons/add_button.png" title="Add Recipe"></a>

       

        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $postId = $row['post_id'];
                    echo "<div class='container'>";
                     echo "<div class='post-title'>";
                        echo "<h3 style='font-size:25px;'>" . htmlspecialchars($row['post_title']) . "</h3>";
                    echo "</div>";
                    echo "<div class='post' onclick='view_post(" . $row['post_id'] . ")'>";

                    if ($row['post_edited_date'] != $row['post_posted_date']) {
                        // Post has been edited
                        echo "<div style='display: flex; align-items: center;'>"; 
                        if ($row['user_profile_picture']) {
                            echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' alt='Profile picture' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                        } else {
                            echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                        }
                        echo "<p><b>" . htmlspecialchars($row['user_name']) . "</b> edited on <b>" . htmlspecialchars($row['post_edited_date']) . "</b></p>";
                        echo "</div>"; 
                    } else {
                        // Post has not been edited
                        echo "<div style='display: flex; align-items: center;'>"; 
                        if ($row['user_profile_picture']) {
                            echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' alt='Profile picture' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                        } else {
                            echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                        }
                        echo "<p><b>" . htmlspecialchars($row['user_name']) . "</b> posted on <b>" . htmlspecialchars($row['post_posted_date']) . "</b></p>";
                        echo "</div>"; 
                    }

                    echo "<p>Category : " . htmlspecialchars($row['post_category']) . "</p>";
                    if (($row['post_image'])) {
                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
                    } else {
                        echo "No image available";
                    }

                    echo "<p>" . htmlspecialchars($row['post_text']) . "</p>";
                    echo "<p>" . htmlspecialchars($row['post_keywords']) . "</p>";
                    echo "<button class='fav-btn' data-post-id='" . $row['post_id'] . "'>Add to Favourites</button>";

                    
                    echo "<button class='like-btn' data-post-id='" .  $row['post_id'] . "'>";
                    echo "Likes: <span id='like-count-" .  $row['post_id'] . "'>" . htmlspecialchars($row['post_like_count']) . "</span>";
                    echo "</button>";
                    echo "<button class='comment-btn' data-post-id='" . $postId . "'>Comment</button>";

                    echo "</div>";
                    echo "</div>";
                    echo "<br/>";
                }
            } else {
                echo "<p>You have not posted any recipes.   </p>";
            }
            $conn->close();
        ?>
        <!-- pop up box for comments -->
        <div id="commentModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Comments</h2>
                <div id="commentList"></div>
                <textarea id="commentText" placeholder="Add your comment..."></textarea><br/><br/>
                <button id="submitComment">Submit Comment</button>
            </div>
        </div>
    </body>
    <script>
        function view_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/post_functionality/view_post.php?post_id=" + post_id;
        }

        //ajax for like button
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/recipebook/Recipe-Book/php/likes_functionality/like_post.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            const likeCountSpan = document.getElementById('like-count-' + postId);
                            likeCountSpan.innerText = response.newLikeCount;
                        } else {
                            alert(response.message);
                        }
                    }
                };
                xhr.send('post_id=' + postId);
            });
        });
        
        //ajax for favourite button
        document.querySelectorAll('.fav-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/RecipeBook/Recipe-Book/php/favourite_functionality/add_favourite.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('Post added to your favourites!');
                    }
                };
                xhr.send('post_id=' + postId);
            });
        });

        // ajax and js for comments section
        let commentPollingInterval; // Variable to hold the interval ID

        function openModal(postId) {
            // Fetch existing comments
            fetchComments(postId);
            
            // Start polling for new comments
            commentPollingInterval = setInterval(() => {
                fetchComments(postId);
            }, 3000); // Fetch new comments every 3 seconds
            
            // Display the modal
            document.getElementById('commentModal').style.display = 'block';
            
            // Set the postId in the button
            document.getElementById('submitComment').setAttribute('data-post-id', postId);
        }

        function closeModal() {
            document.getElementById('commentModal').style.display = 'none';
            
            // Stop polling when the modal is closed
            clearInterval(commentPollingInterval);
        }

        function fetchComments(postId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '/RecipeBook/Recipe-Book/php/comment_functionality/fetch_comments.php?post_id=' + postId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('commentList').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Submit comment
        document.getElementById('submitComment').addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const commentText = document.getElementById('commentText').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/RecipeBook/Recipe-Book/php/comment_functionality/add_comment.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('commentText').value = ''; // Clear the text area
                    fetchComments(postId); // Refresh comments
                }
            };
            xhr.send('post_id=' + postId + '&comment_text=' + encodeURIComponent(commentText));
        });

        // Handle comment button clicks
        document.querySelectorAll('.comment-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent post click
                const postId = this.getAttribute('data-post-id');
                openModal(postId);
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('commentModal');
            if (event.target == modal) {
                closeModal();
            }
        };

        // Close modal on 'x' click
        document.querySelector('.close').addEventListener('click', closeModal);

        //deleting comment
        function deleteComment(commentId) {
            if (confirm("Are you sure you want to delete this comment?")){
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/Recipebook/Recipe-Book/php/comment_functionality/delete_comment.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert(xhr.responseText);
                    }
                };
                xhr.send('comment_id=' + commentId);
            }
        }
    </script>
</html>