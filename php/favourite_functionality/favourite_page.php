<?php
    session_start();

    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
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
        SELECT post.*, user.user_name, user.user_profile_picture,
            (SELECT COUNT(*) FROM Likes WHERE Likes.post_id = post.post_id) AS post_like_count,
           (SELECT COUNT(*) FROM Likes WHERE Likes.post_id = post.post_id AND Likes.user_id = $user_id) AS has_liked,
           (SELECT COUNT(*) FROM favourite WHERE favourite.post_id = post.post_id AND favourite.user_id = $user_id) AS has_favorited

        FROM favourite 
        JOIN post ON favourite.post_id = post.post_id
        JOIN user ON post.user_id = user.user_id
        LEFT JOIN likes ON post.post_id = Likes.post_id
        WHERE favourite.user_id = $user_id 
        GROUP BY post.post_id
        ORDER BY favourite.fav_added_date DESC
    ";
    $result = $conn->query($sql);
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/RecipeBook/Recipe-Book/css/favourite_page.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>
    <body>
        <img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back" onmouseover="onHoverBack()" onmouseout="noHoverBack()">
        <h1>Welcome to <span style="color:#ffbf17; cursor:pointer;" title="Your favourites">your Favourites</span></h1>
        <h2>All posts</h2>
        
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $postId = $row['post_id'];
                    echo "<br/>";
                    echo "<div class='container'>";
                        echo "<div class='post' onclick='view_post($postId)'>";
                            echo "<div class='post-title'>";
                                echo "<h3 style='font-size:30px;'>" . htmlspecialchars($row['post_title']) . "</h3>";
                            echo "</div>";
                            
                            echo "<div class='post-image' style='text-align:center;'>";
                                if (($row['post_image'])) {
                                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' 
                                    alt='Recipe Image' style='max-width: 450px; max-height: 450px; border-radius:8px; cursor: pointer;' onclick='inlarge_image(this)'/>";
                                } else {
                                    echo "No image available";
                                }
                            echo "</div>";

                            echo "<div class='post-description'>";
                                echo "<p>" . htmlspecialchars($row['post_keywords']) . "</p>";
                                echo "<p><b>Category : </b>" . htmlspecialchars($row['post_category']) . "</p>";
                            echo "</div>";

                            echo "<div class='post-actions'>";
                                echo "<div style='display: flex; align-items: center;'>"; 
                                    if ($row['user_profile_picture']) {
                                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' alt='Profile picture' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                                    } else {
                                        echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                                    }
                                    echo "<p><b>" . htmlspecialchars($row['user_name']) . "</p></b>";
                                echo "</div>";
                                echo "<div class='like_comment_bookmark'>";
                                    $likeButtonSrc = $row['has_liked'] ? "/RecipeBook/Recipe-Book/buttons/like_button_yellow_filled.png" : "/RecipeBook/Recipe-Book/buttons/like_button_yellow_outlined.png";
                                    echo "<img id='like-btn-" . $row['post_id'] . "' class='like-btn' data-post-id='" . $row['post_id'] . "' src='" . $likeButtonSrc . "' height='30px' width='30px' title='Likes'/>";
                                    echo "<span id='like-count-" . $row['post_id'] . "' style='color:#ffbf17; font-weight:bold;'>" . htmlspecialchars($row['post_like_count']) . "</span>&nbsp;&nbsp;&nbsp";
                                    echo "<img class='comment-btn' data-post-id='" . $postId . "' src='/RecipeBook/Recipe-Book/buttons/comment_button_yellow_outlined.png' height='30px' width='30px' title='Comment' onmouseover='onHoverComment(this)' onmouseout='noHoverComment(this)'/>&nbsp;&nbsp;&nbsp;";
                                    echo "<img class='remove-fav-btn' data-post-id='" . $row['post_id'] . "' src='/RecipeBook/Recipe-Book/buttons/fav_button_yellow_filled.png' height='30px' width='30px' title='Remove from favourites' onmouseover='onHoverFav(this)' onmouseout='noHoverFav(this)'/>";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>"; 
                    echo "<br/>";       
                }
            }else {
                echo "<p style='text-align: center; font-size: 20px;'><b>";
                echo "<img src='/RecipeBook/Recipe-Book/logo/logo4.png' title='Recipebook' style='width: 300px; height: 300px; cursor:pointer;'/><br/>";
                echo "You have not added any recipes to your favourites yet.";
                echo "</b></p><br/>";
            }
            $conn->close();
        ?>

        <!-- pop up box for comments -->
        <div id="commentModal" class="modal">
            <div class="modal-content">
                <div style="text-align:right;">
                    <span class="close" onclick="closeModal()" style="font-size:35px; color:black; cursor:pointer;">&times;</span>
                </div>
                <h2 style="color: #ffbf17; font-size:35px;">Comments</h2>
                <div id="commentList"></div><br/><br/><br/>
                <div style="display:flex; align-items:center;">
                    <textarea id="commentText" placeholder="Add your comment..."></textarea>&nbsp;&nbsp;
                    <button id="submit-comment">Submit Comment</button>
                </div>
            </div>
        </div>
    </body>
    <script>
        function go_back() {
            window.history.back();
        }
        
        function view_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/post_functionality/view_post.php?post_id=" + post_id;
        }
        
        function onHoverBack(){
            document.querySelector('.back-button').src = '/RecipeBook/Recipe-Book/buttons/back_button2.png';
        }

        function noHoverBack(){
            document.querySelector('.back-button').src = '/RecipeBook/Recipe-Book/buttons/back_button.png';
        }

        function onHoverFav(fav) {
            fav.src = '/RecipeBook/Recipe-Book/buttons/fav_button_yellow_outlined.png';
        }

        function noHoverFav(fav) {
            fav.src = '/RecipeBook/Recipe-Book/buttons/fav_button_yellow_filled.png';
        }

        function onHoverComment(comment) {
            comment.src = '/RecipeBook/Recipe-Book/buttons/comment_button_yellow_filled.png';
        }

        function noHoverComment(comment) {
            comment.src = '/RecipeBook/Recipe-Book/buttons/comment_button_yellow_outlined.png';
        }

        //pop up large image function
        function inlarge_image(image) {
            event.stopPropagation(); // Prevent any parent event from triggering
            // Create the modal container
            const modal = document.createElement('div');
            modal.classList.add('image-modal');
            modal.style.display = 'flex';

            // Add the image to the modal
            const modalImage = document.createElement('img');
            modalImage.src = image.src;
            modal.appendChild(modalImage);

            // Add a close button
            const closeBtn = document.createElement('span');
            closeBtn.classList.add('close-btn');
            closeBtn.innerHTML = '&times;';
            closeBtn.onclick = function () {
                modal.style.display = 'none';
                modal.remove();
            };
            modal.appendChild(closeBtn);

            // Add the modal to the body
            document.body.appendChild(modal);
        }

        //ajax for like button
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function (event) {
                event.stopPropagation(); // Prevent any parent event from triggering
                
                const postId = this.getAttribute('data-post-id');
                const likeBtn = document.getElementById(`like-btn-${postId}`);
                const likeCount = document.getElementById(`like-count-${postId}`);
                const isLiked = likeBtn.src.includes("like_button_yellow_filled");

                // Update Like Button UI
                if (isLiked) {
                    likeBtn.src = "/RecipeBook/Recipe-Book/buttons/like_button_yellow_outlined.png";
                    likeCount.innerHTML = parseInt(likeCount.innerHTML) - 1;
                } else {
                    likeBtn.src = "/RecipeBook/Recipe-Book/buttons/like_button_yellow_filled.png";
                    likeCount.innerHTML = parseInt(likeCount.innerHTML) + 1;
                }

                // Send AJAX Request
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "/RecipeBook/Recipe-Book/php/likes_functionality/like_post.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);

                        if (response.success) {
                            // Update Like Count in UI (already updated above, but just in case)
                            likeCount.innerText = response.newLikeCount;
                        } else {
                            alert(response.message);
                        }
                    } else {
                        console.error("Error with the AJAX request.");
                    }
                };

                xhr.send("post_id=" + postId);
            });
        });

        //ajax for remove from favourite button
        document.querySelectorAll('.remove-fav-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');
                const postElement = this.closest('.container'); // This ensures the entire post container is removed

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/RecipeBook/Recipe-Book/php/favourite_functionality/remove_favourite.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = xhr.responseText.trim();
                        if (response.includes('successfully')) {
                            alert('Removing this post from your favourites!');
                            postElement.remove();  // Remove the entire post container (not just the post)
                        } else {
                            alert('Error: ' + response);
                        }
                    } else {
                        alert('Failed to remove favorite. Please try again.');
                    }
                };

                xhr.onerror = function() {
                    alert('Request failed. Please check your connection.');
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
            document.getElementById('submit-comment').setAttribute('data-post-id', postId);
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
        document.getElementById('submit-comment').addEventListener('click', function() {
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