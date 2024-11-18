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
    $dbname = "recipebook";

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
        SELECT post.*, 
           user.user_name, 
           user.user_profile_picture,
           (SELECT COUNT(*) FROM Likes WHERE Likes.post_id = post.post_id) AS post_like_count,
           (SELECT COUNT(*) FROM Likes WHERE Likes.post_id = post.post_id AND Likes.user_id = $user_id) AS has_liked,
           (SELECT COUNT(*) FROM favourite WHERE favourite.post_id = post.post_id AND favourite.user_id = $user_id) AS has_favorited
        FROM post 
        JOIN user ON post.user_id = user.user_id 
        ORDER BY $sort_by
    ";

    $profile_pic_sql = "
        SELECT user_profile_picture
        FROM user  
        WHERE user_id = $user_id
    ";

    $result = $conn->query($sql);
    $result2 = $conn->query($profile_pic_sql);
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/RecipeBook/Recipe-Book/css/home.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>
    <body>
        <nav class="navbar">
            <div class="logo">
                <img src="/RecipeBook/Recipe-Book/logo/logo4.png" onclick="window.location.href='/RecipeBook/Recipe-Book/php/home.php'" title="Home feed" style="width: 120px; height: 120px;"/>&nbsp;
                <h1  onclick="window.location.href='/RecipeBook/Recipe-Book/php/home.php'" class="recipebook" title="Home feed">Recipebook</h1> 
            </div>

            <div class="search-bar">
                <img src="/RecipeBook/Recipe-Book/buttons/search_icon.png" height="30px"/>
                <form name="search" method="post" action="/RecipeBook/Recipe-Book/php/search_functionality/search_post.php">
                    <input type="text" id="search" name="search" placeholder="Search Recipe"/>
                    <input class="search-btn" type="submit" value="Search"/>
                </form> 
            </div>

            <div class="profile">
                <a href="/RecipeBook/Recipe-Book/php/profile.php">
                    <?php
                        if ($result2->num_rows==1) {
                            while($row = $result2->fetch_assoc()) {
                                if (($row['user_profile_picture'])) {
                                    echo "<img src='data:image/jpeg;base64,".base64_encode($row['user_profile_picture'])."' alt='Profile picture' title='Your profile' style='max-width: 55px; max-height: 50px; border-radius: 50%;'/>";
                                } else {
                                    echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='max-width: 50px; max-height: 50px; border-radius: 50%;'/>";
                                }
                            }
                        }
                    ?>
                </a>
            </div>
            
            <div class="rightside-bar">
                <img class="favc-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/php/favourite_functionality/favourite_page.php'" src="/RecipeBook/Recipe-Book/buttons/fav_button_black.png" title="Your favourites" onmouseover="onHoverFavc()" onmouseout="noHoverFavc()"/>
                <img class="setting-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/html/manage_profile/settings.html'" src="/RecipeBook/Recipe-Book/buttons/settings_button_black_lined.png" title="Settings" onmouseover="onHoverSetting()" onmouseout="noHoverSetting()"/>
            </div>
        </nav>

        <div class="heading">
            <h1 style="text-align: center;">Welcome to <span style="color:#ffbf17; cursor:pointer;" onclick="window.location.href='/RecipeBook/Recipe-Book/php/home.php'" title="Home feed">Home feed</span></h1><br/>
            <h2 style="text-align: center;">All posts</h2>

            <form id="sortForm" method="GET" action="">
                <label for="sort">Sort by:</label>&nbsp;&nbsp;
                <select id="sort" name="sort" onchange="document.getElementById('sortForm').submit();">
                    <option value="date" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date') ? 'selected' : ''; ?>>Date</option>
                    <option value="likes" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'likes') ? 'selected' : ''; ?>>Likes</option>
                </select>
            </form>
        </div>

        <a href="/RecipeBook/Recipe-Book/html/post_functionality/add_post.html"><img class="add_recipe" src="/RecipeBook/Recipe-Book/buttons/add_button.png" title="Add Recipe"></a>
        <div class="post-wrapper">
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $postId = $row['post_id'];
                        echo "<br/>";
                        echo "<div class='container' onmouseover='onHover(this)' onmouseout='noHover(this)'>";

                            echo "<div class='post-title'>";
                                echo "<h3 style='font-size:20px;'>" . htmlspecialchars($row['post_title']) . "</h3>";
                            echo "</div>";
                            
                            echo "<div class='post' onclick='view_post($postId)'>";
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

                                echo "<p><b>Category : </b>" . htmlspecialchars($row['post_category']) . "</p>";

                                if (($row['post_image'])) {
                                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px; border-radius:30px;'/>";
                                } else {
                                    echo "No image available";
                                }
                                echo "<p>" . htmlspecialchars($row['post_text']) . "</p>";
                                echo "<p>" . htmlspecialchars($row['post_keywords']) . "</p>";

                                $likeButtonSrc = $row['has_liked'] ? "/RecipeBook/Recipe-Book/buttons/like_button_yellow_filled.png" : "/RecipeBook/Recipe-Book/buttons/like_button_black_outlined.png";
                                $favButtonSrc = $row['has_favorited'] ? "/RecipeBook/Recipe-Book/buttons/fav_button_yellow_filled.png" : "/RecipeBook/Recipe-Book/buttons/fav_button_black_outlined.png";

                                echo "<img id='like-btn-" . $row['post_id'] . "' class='like-btn' data-post-id='" . $row['post_id'] . "' src='" . $likeButtonSrc . "' height='30px' width='30px' title='Likes' />";
                                echo "<span id='like-count-" . $row['post_id'] . "'>" . htmlspecialchars($row['post_like_count']) . "</span>&nbsp;&nbsp;&nbsp;";
                                echo "<img class='comment-btn' data-post-id='" . $postId . "' src='/RecipeBook/Recipe-Book/buttons/comment_button_black_outlined.png' height='30px' width='30px' title='Comment'/>&nbsp;&nbsp;&nbsp;";
                                echo "<img id='fav-btn-" . $row['post_id'] . "' class='fav-btn' data-post-id='" . $row['post_id'] . "' src='" . $favButtonSrc . "' height='30px' width='30px' title='Add to favourites' />";

                                echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>There are no recipes to show you, Sorry T_T </p>";
                }
                $conn->close();
            ?>
        </div>
        <!-- pop up box for comments -->
        <div id="commentModal" class="modal">
            <div class="modal-content">
                <div style="text-align:right;">
                    <span class="close" onclick="closeModal()" style="font-size:35px; color:black; cursor:pointer;">&times;</span>
                </div>
                <h2>Comments</h2>
                <div id="commentList"></div>
                <textarea id="commentText" placeholder="Add your comment..."></textarea><br/><br/>
                <button id="submit-comment">Submit Comment</button>
            </div>
        </div>
    </body>
    <script>
        function view_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/post_functionality/view_post.php?post_id=" + post_id;
        }
                function onHoverFavc(){
            document.querySelector('.favc-btn').src = '/RecipeBook/Recipe-Book/buttons/fav_button_yellow.png';
        }

        function noHoverFavc(){
            document.querySelector('.favc-btn').src = '/RecipeBook/Recipe-Book/buttons/fav_button_black.png';
        }

        function onHoverSetting(){
            document.querySelector('.setting-btn').src = '/RecipeBook/Recipe-Book/buttons/settings_button_yellow_lined.png';
            document.querySelector('.setting-btn').style.height="50px";
            document.querySelector('.setting-btn').style.width="50px";
        }

        function noHoverSetting(){
            document.querySelector('.setting-btn').src = '/RecipeBook/Recipe-Book/buttons/settings_button_black_lined.png';
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
                    likeBtn.src = "/RecipeBook/Recipe-Book/buttons/like_button_black_outlined.png";
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

        document.querySelectorAll('.fav-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent parent events from triggering

                const postId = this.getAttribute('data-post-id');
                const favBtn = document.getElementById(`fav-btn-${postId}`);
                const isFavored = favBtn.src.includes("fav_button_yellow_filled");

                // Toggle Favorite Button UI
                if (isFavored) {
                    favBtn.src = "/RecipeBook/Recipe-Book/buttons/fav_button_black_outlined.png";
                } else {
                    favBtn.src = "/RecipeBook/Recipe-Book/buttons/fav_button_yellow_filled.png";
                }

                // Send AJAX Request
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "/RecipeBook/Recipe-Book/php/favourite_functionality/add_favourite.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('Post added to your favourites!');
                    } else {
                        console.error("AJAX request failed.");
                    }
                };

                xhr.send("post_id=" + postId); 
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