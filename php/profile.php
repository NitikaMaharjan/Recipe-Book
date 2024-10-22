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
        SELECT post.*, user.user_name 
        FROM post 
        JOIN user ON post.user_id = user.user_id 
        WHERE post.user_id = $user_id
        ORDER BY $sort_by
    ";

    $result = $conn->query($sql);
?>

<html>
    <head>
        <title>Profile page</title>
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
        <h1>Hello <?php echo "$user_name" ?>, welcome to your profile!!</h1>
        <button><a href="/RecipeBook/Recipe-Book/php/logout.php">Log out</a></button>
        <button><a href="/RecipeBook/Recipe-Book/html/add_post.html">Add recipe</a></button>
        <h2>All your posts</h2>

        <form id="sortForm" method="GET" action="">
            <label for="sort">Sort by:</label>
            <select id="sort" name="sort" onchange="document.getElementById('sortForm').submit();">
                <option value="date" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date') ? 'selected' : ''; ?>>Date</option>
                <option value="likes" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'likes') ? 'selected' : ''; ?>>Likes</option>
            </select>
        </form>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                echo "<div class='post' onclick='view_post(" . $row['post_id'] . ")'>";
                echo "<h3>" . htmlspecialchars($row['post_title']) . "</h3>";

                if ($row['post_edited_date'] != $row['post_posted_date']) {
                    echo "<p><b>Post edited on</b> " . htmlspecialchars($row['post_edited_date']) . "</p>";
                } else {
                    echo "<p><b>Posted on</b> " . htmlspecialchars($row['post_posted_date']) . "</p>";
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

                echo "</div>";
                echo "<br/>";
            }
        } else {
            echo "<p>You have not posted any recipes.   </p>";
        }
        $conn->close();
        ?>
    </body>
    <script>
        function view_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/view_post.php?post_id=" + post_id;
        }

        //ajax for like button
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/rohan/Recipe-Book/php/like_post.php', true);
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
                xhr.open('POST', '/RecipeBook/Recipe-Book/php/add_favourite.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('Post added to your favourites!');
                    }
                };
                xhr.send('post_id=' + postId);
            });
        });
    </script>
</html>