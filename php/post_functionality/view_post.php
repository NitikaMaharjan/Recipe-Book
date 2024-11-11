<?php
    session_start();

    if (!isset($_SESSION['username']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false){
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    } 

    if (!isset($_GET['post_id'])) {
        echo "<script>
                alert ('No post ID provided for viewing!!');
              </script>";
        exit();
    }

    $post_id = $_GET['post_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
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
    $sql = "SELECT post.*, user.user_name, user.user_profile_picture
            FROM post JOIN user ON post.user_id = user.user_id WHERE post.post_id =$post_id";
    $result = $conn->query($sql);

    if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>
                alert ('No post found!!');
              </script>";
        exit();
    }
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/RecipeBook/Recipe-Book/css/styles.css">
    </head>
    <body>
        <button onclick="go_back()" class="back-button">Go Back</button><br/><br/>
        <div class="view-post-container">
            <?php
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
            ?>
            <h1>
                <?php 
                    echo htmlspecialchars($row['post_title'])."&nbsp&nbsp&nbsp&nbsp";
                    if($_SESSION['user_id']==$row['user_id']){
                        echo "<button onclick='edit_post(" . $row['post_id'] . ")'>Edit post</button>";
                        echo "<button onclick='delete_post(" . $row['post_id'] . ")'>Delete post</button>";
                    }
                ?>
            </h1>
            <p><?php echo htmlspecialchars($row['post_text']); ?></p>

            <?php

                if (($row['post_image'])) {
                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' '/>";
                } else {
                    echo "No image available";
                }

                echo "<p><b>Ingredients:</b></p>";
                $ingredients = explode(', ', $row['post_ingredients']);
                echo "<ul>";
                foreach ($ingredients as $ingredient) {
                    echo "<li>" . htmlspecialchars($ingredient) . "</li>";
                }
                echo "</ul>";

                echo "<p><b>Instructions:</b></p>";
                $steps = explode(', ', $row['post_instructions']);
                echo "<ol>";
                foreach ($steps as $step) {
                    echo "<li>" . htmlspecialchars($step) . "</li>";
                }
                echo "</ol>";

                echo "<p><b>Keywords</b>:" . htmlspecialchars($row['post_keywords']) . "</p>";
                echo "<p><b>Category</b>:" . htmlspecialchars($row['post_category']) . "</p>";

                echo "<button class='fav-btn' data-post-id='" . $row['post_id'] . "'>Add to Favourites</button>";
                echo "<button class='like-btn' data-post-id='" .  $row['post_id'] . "'>";
                echo "Likes: <span id='like-count-" .  $row['post_id'] . "'>" . htmlspecialchars($row['post_like_count']) . "</span>";
                echo "</button>";
                echo "<button class='comment-btn' data-post-id='" .  $row['post_id'] . "'>Comment</button>";
            ?>
        </div>
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
        function go_back(){
            window.history.back();
        }

        function edit_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/post_functionality/edit_post.php?post_id=" + post_id;
        }

        function delete_post(post_id) {
            var ans = confirm("Are you sure you want to delete this post?");
            if (ans == true) {
                window.location.href = "/RecipeBook/Recipe-Book/php/post_functionality/delete_post.php?post_id=" + post_id;
            }
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
            xhr.open('GET', '/recipebook/Recipe-Book/php/comment_functionality/fetch_comments.php?post_id=' + postId, true);
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
            xhr.open('POST', '/recipebook/Recipe-Book/php/comment_functionality/add_comment.php', true);
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

<?php 
    $conn->close();
?>