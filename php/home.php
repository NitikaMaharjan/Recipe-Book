<?php
    session_start();

    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    }

    $user_name = $_SESSION['username'];

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

    $sql = "SELECT post.*, user.user_name, 
                    IFNULL((SELECT COUNT(*) FROM Likes WHERE post.post_id = Likes.post_id), 0) AS post_like_count, 
                    IFNULL((SELECT COUNT(*) FROM Likes WHERE post.post_id = Likes.post_id AND Likes.user_id = " . $_SESSION['user_id'] . "), 0) AS user_liked
                FROM post 
                JOIN user ON post.user_id = user.user_id 
                ORDER BY $sort_by";
    $result = $conn->query($sql);
?>

<html>
    <head>
        <title>Home page</title>
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
        <form name="search" method="post" action="/RecipeBook/Recipe-Book/php/search_functionality/search_post.php">
            <br/>
            <input type="text" id="search" name="search" placeholder="Search Recipe"/>
            <input type="submit" value="Search"/>
            <br />
        </form>
        
        <h1>Hello <?php echo "$user_name" ?>, welcome to your home feed!!</h1>
        <button><a href="/RecipeBook/Recipe-Book/php/logout.php">Log out</a></button>
        <button><a href="/RecipeBook/Recipe-Book/html/add_post.html">Add recipe</a></button>
        <h2>All posts</h2>

        <form id="sortForm" method="GET" action="">
            <label for="sort">Sort by:</label>
            <select id="sort" name="sort" onchange="document.getElementById('sortForm').submit();">
                <option value="date" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date') ? 'selected' : ''; ?>>Date</option>
                <option value="likes" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'likes') ? 'selected' : ''; ?>>Likes</option>
            </select>
        </form>
        <br/>

        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $liked = $row['user_liked'] > 0 ? 'liked' : '';
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
                    echo "<p>" . htmlspecialchars($row['post_keywords']) . "</p>";
                    echo "<button class='fav-btn' data-post-id='" . $row['post_id'] . "'>Add to Favourites</button>";

                    echo "<button class='like-btn $liked' data-post-id='" . $row['post_id'] . "'>";
                    echo "Likes: <span id='like-count-" . $row['post_id'] . "'>" . htmlspecialchars($row['post_like_count']) . "</span>";
                    echo "</button>";
                    
                    echo "</div>";
                    echo "<br/>";
                }
            } else {
                echo "<p>There are no recipes to show you, Sorry T_T </p>";
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

        //ajax for favourite button
        document.querySelectorAll('.fav-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');
                console.log(postId);
                
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