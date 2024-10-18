<?php
    session_start();

    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['username'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $sql="SELECT post.*, user.user_name, 
        IFNULL((SELECT COUNT(*) FROM Likes WHERE Likes.post_id = post.post_id), 0) AS post_like_count,
        IFNULL((SELECT COUNT(*) FROM Likes WHERE Likes.post_id = post.post_id AND Likes.user_id = " . $_SESSION['user_id'] . "), 0) AS user_liked
        FROM favourite 
        JOIN post ON favourite.post_id = post.post_id
        JOIN user ON post.user_id = user.user_id
        LEFT JOIN likes ON post.post_id = Likes.post_id
        WHERE favourite.user_id = $user_id 
        GROUP BY post.post_id
        ORDER BY favourite.fav_added_date DESC";
    $result = $conn->query($sql);
?>

<html>
    <head>
        <title>My Favourite Posts</title>
        <style>
            .post {
                cursor: pointer;
                padding: 10px;
                border: 1px solid #ccc;
                margin-bottom: 10px;
                transition: background-color 0.3s ease;
            }
        </style>
    </head>
    <body>
        <header>
            <div>
                <button><a href="/RecipeBook/Recipe-Book/php/home.php">Home</a></button>
                <button><a href="/RecipeBook/Recipe-Book/php/profile.php">Profile</a></button>
                <button><a href="/RecipeBook/Recipe-Book/php/favourite_page.php">My Favourites</a></button>
            </div>
        </header>
        <h1>Hello <?php echo "$user_name" ?>, welcome to your favourites!!</h1>
        <h2>All your favourites</h2>
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()){

                    echo "<div class='post' onclick='view_post(" . $row['post_id'] . ")'>";
                    echo "<h3>" . htmlspecialchars($row['post_title']) . "</h3>";

                    if ($row['post_edited_date'] != $row['post_posted_date']) {
                        echo "<p>Posted by <b>" . htmlspecialchars($row['user_name']) . " </b>and edited on<b> " . htmlspecialchars($row['post_edited_date']) . "</b></p>";
                    } else {
                        echo "<p>Posted by <b>" . htmlspecialchars($row['user_name']) . " </b>on<b> " . htmlspecialchars($row['post_edited_date']) . "</b></p>";
                    }

                    echo "<p>Category : " . htmlspecialchars($row['post_category']) . "</p>";

                    if (($row['post_image'])) {
                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
                    } else {
                        echo "No image available";
                    }

                    echo "<p>" . htmlspecialchars($row['post_text']) . "</p>";

                    echo "<button class='remove-fav-btn' data-post-id='" . $row['post_id'] . "'>Remove from Favourites</button>";

                    $liked = $row['user_liked'] > 0 ? 'liked' : '';
                    echo "<button class='like-btn $liked' data-post-id='" . $row['post_id'] . "'>";
                    echo "Likes: <span id='like-count-" . $row['post_id'] . "'>" . htmlspecialchars($row['post_like_count']) . "</span>";
                    echo "</button>";

                    echo "</div>";
                    echo "<br/>";
                }
            } else {
                echo "<p>You have not added any posts to your favourites T T</p>";
            }
            $conn->close();
        ?>
    </body>
    <script>
        function view_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/view_post.php?post_id=" + post_id;
        }

        function go_back(){
            window.history.back();
        }

        //ajax for like button
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');
                
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/RecipeBook/Recipe-Book/php/like_post.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            const likeBtn = document.querySelector('.like-btn[data-post-id="' + postId + '"]');
                            likeBtn.classList.toggle('liked');
                            document.getElementById('like-count-' + postId).innerText = response.newLikeCount;
                        } else {
                            alert(response.message);

                        }
                    }
                };
                xhr.send('post_id=' + postId);
            });
        });

        //ajax for remove from favourite button
        document.querySelectorAll('.remove-fav-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');
                const postElement = this.closest('.post');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/RecipeBook/Recipe-Book/php/remove_favourite.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = xhr.responseText.trim();
                        if (response.includes('successfully')) {
                            alert('Post removed from your favourites!');
                            postElement.remove();
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
    </script>
</html>