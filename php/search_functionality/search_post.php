<?php
    session_start();


    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    }

    $user_name = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    
    $search = '';
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipebook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $search = $_POST['search'];
        
        if (empty($search)){
            header("Location: /Recipebook/Recipe-Book/php/home.php");
            exit(); 
        }

        $_SESSION['last_search'] = $search;

        header("Location: /Recipebook/Recipe-Book/php/search_functionality/search_post.php?search=" . urlencode($search));
        exit(); 
    }

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $_SESSION['last_search'] = $search;
    } else {
        $search = $_SESSION['last_search'] ?? '';
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
    $sql = "SELECT post.*, user.user_name, user.user_profile_picture,
                (SELECT COUNT(*) FROM Likes WHERE Likes.post_id = post.post_id) AS post_like_count,
                (SELECT COUNT(*) FROM Likes WHERE Likes.post_id = post.post_id AND Likes.user_id = $user_id) AS has_liked,
                (SELECT COUNT(*) FROM favourite WHERE favourite.post_id = post.post_id AND favourite.user_id = $user_id) AS has_favorited
            FROM post 
            JOIN user ON post.user_id = user.user_id 
            WHERE (post.post_title LIKE '%$search%' 
                OR user.user_name LIKE '%$search%')";

    // Split the search terms by commas and trim whitespace
    $searchTerms = array_map('trim', explode(',', $search));

    // Initialize arrays for conditions
    $categoryConditions = [];
    $keywordConditions = [];

    // Loop through each term to create conditions for both category and keywords
    foreach ($searchTerms as $term) {
        // Create dynamic conditions for both categories and keywords
        $categoryConditions[] = "post.post_category LIKE '%$term%'";
        $keywordConditions[] = "post.post_keywords LIKE '%$term%'";
    }

    // Add category conditions to the SQL query
    if (!empty($categoryConditions)) {
        $sql .= " OR (" . implode(' OR ', $categoryConditions) . ")";
    }

    // Add keyword conditions to the SQL query
    if (!empty($keywordConditions)) {
        $sql .= " OR (" . implode(' OR ', $keywordConditions) . ")";
    }

    $sql .= " ORDER BY post.post_id DESC";

    $result = $conn->query($sql);

    $profile_pic_sql = "
        SELECT user_profile_picture
        FROM user  
        WHERE user_id = $user_id
    ";
    $result2 = $conn->query($profile_pic_sql);
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/RecipeBook/Recipe-Book/css/search_post.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>
    <body>
        <nav class="navbar">
            <div class="logo">
                <img src="/RecipeBook/Recipe-Book/logo/logo4.png" onclick="window.location.href='/RecipeBook/Recipe-Book/php/home.php'" title="Home feed" style="width: 120px; height: 120px;"/>&nbsp;
                <h1  onclick="about()" class="recipebook" title="About Recipebook">Recipebook</h1> 
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
                                    echo "<img src='data:image/jpeg;base64,".base64_encode($row['user_profile_picture'])."' title='Your profile' style='max-width: 55px; max-height: 50px; border-radius: 50%;'/>";
                                } else {
                                    echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' title='Your profile' style='max-width: 50px; max-height: 50px; border-radius: 50%;'/>";
                                }
                            }
                        }
                    ?>
                </a>
            </div>
            
            <div class="rightside-bar">
                <img class="favc-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/php/favourite_functionality/favourite_page.php'" src="/RecipeBook/Recipe-Book/buttons/fav_button_black.png" title="Your favourites" onmouseover="onHoverFavc()" onmouseout="noHoverFavc()"/>
                <img class="pen-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/php/post_functionality/pending_post_page.php'" src="/RecipeBook/Recipe-Book/buttons/pending_button_black.png" title="Your pending posts" onmouseover="onHoverPen()" onmouseout="noHoverPen()"/>
                <img class="setting-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/html/manage_profile/settings.html'" src="/RecipeBook/Recipe-Book/buttons/settings_button_black_lined.png" title="Settings" onmouseover="onHoverSetting()" onmouseout="noHoverSetting()"/>
            </div>
        </nav>

        <div class="heading">
            <h1 style="text-align: center;">Results for <span style="color:#ffbf17; cursor:pointer;"><?php echo $search ?></span>:</h1><br/>
            <h2 style="text-align: center;">All posts</h2>
        </div>
        
        <a href="/RecipeBook/Recipe-Book/html/post_functionality/add_post.html"><img class="add_recipe" src="/RecipeBook/Recipe-Book/buttons/add_button.png" title="Add Recipe"></a>
        
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
                                    alt='Recipe Image' style='max-width: 450px; max-height: 450px; border-radius:8px; cursor: pointer;'/>";
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
                                    echo "<img id='fav-btn-" . $row['post_id'] . "' class='fav-btn' data-post-id='" . $row['post_id'] . "' src='/RecipeBook/Recipe-Book/buttons/fav_button_yellow_outlined.png' height='30px' width='30px' title='Add to favourites' onmouseover='onHoverFav(this)' onmouseout='noHoverFav(this)'/>";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>"; 
                    echo "<br/>";       
                }
            } else {
                echo "<p style='text-align: center; font-size: 20px;'><b>";
                echo "<img src='/RecipeBook/Recipe-Book/logo/logo4.png' title='Recipebook' style='width: 300px; height: 300px; cursor:pointer;'/><br/>";
                echo "There are no results for $search. <span style='color:#ffbf17; cursor:pointer;' onclick='add_post()'>Click Here</span> to get started with Recipebook!";
                echo "</b></p><br/>";
            }
            $conn->close();
        ?>

        <!-- pop up box for about -->
        <div id="about" class="about">
            <div class="about_content">
                <div style="text-align:right;">
                    <span class="close1" onclick="closePopup1()" style="font-size:35px; color:black; cursor:pointer;">&times;</span>
                </div>
                <img src="/RecipeBook/Recipe-Book/logo/logo4.png" title="Recipebook" style="width: 300px; height: 300px;"/>
                <h1 style="color: #333;">About <span style="color:#ffbf17;">Recipebook</span></h1>
                <p style="font-size: 20px; text-align:left;">RecipeBook is a social media platform designed specifically for food enthusiasts. It allows users to share their recipes, discover creations by others, and actively connect and engage with a community of like-minded food lovers.</p>
            </div>
        </div>
        
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
        function view_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/post_functionality/view_post.php?post_id=" + post_id;
        }

        function add_post(){
            window.location.href = "/RecipeBook/Recipe-Book/html/post_functionality/add_post.html";
        }

        function onHoverFavc(){
            document.querySelector('.favc-btn').src = '/RecipeBook/Recipe-Book/buttons/fav_button_yellow.png';
        }

        function noHoverFavc(){
            document.querySelector('.favc-btn').src = '/RecipeBook/Recipe-Book/buttons/fav_button_black.png';
        }

        function onHoverPen(){
            document.querySelector('.pen-btn').src = '/RecipeBook/Recipe-Book/buttons/pending_button_yellow.png';
        }

        function noHoverPen(){
            document.querySelector('.pen-btn').src = '/RecipeBook/Recipe-Book/buttons/pending_button_black.png';
        }

        function onHoverSetting(){
            document.querySelector('.setting-btn').src = '/RecipeBook/Recipe-Book/buttons/settings_button_yellow_lined.png';
            document.querySelector('.setting-btn').style.height="50px";
            document.querySelector('.setting-btn').style.width="50px";
        }

        function noHoverSetting(){
            document.querySelector('.setting-btn').src = '/RecipeBook/Recipe-Book/buttons/settings_button_black_lined.png';
        }

        function onHoverFav(fav) {
            fav.src = '/RecipeBook/Recipe-Book/buttons/fav_button_yellow_filled.png';
        }

        function noHoverFav(fav) {
            fav.src = '/RecipeBook/Recipe-Book/buttons/fav_button_yellow_outlined.png';
        }

        function onHoverComment(comment) {
            comment.src = '/RecipeBook/Recipe-Book/buttons/comment_button_yellow_filled.png';
        }

        function noHoverComment(comment) {
            comment.src = '/RecipeBook/Recipe-Book/buttons/comment_button_yellow_outlined.png';
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

        //ajax for favourite button
        document.querySelectorAll('.fav-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');
                console.log(postId);

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

        //about popup box
        function about() {
            //display the pop-up box
            document.getElementById('about').style.display = 'block';
        }
        function closePopup1() {
            document.getElementById('about').style.display = 'none';
        }
        document.querySelector('.close1').addEventListener('click', closePopup1);
        
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

        // Close modal on 'x' click
        document.querySelector('.close').addEventListener('click', closeModal);

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

        window.onclick = function(event) {
            const popup1 = document.getElementById('about');
            const modal = document.getElementById('commentModal');

            // Close About pop-up when clicking outside
            if (event.target == popup1) {
                closePopup1();
            }
            // Close comment popup when clicking outside
            if (event.target == modal) {
                closeModal();
            }
        };

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
