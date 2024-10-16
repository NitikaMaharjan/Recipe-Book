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

$sql = "SELECT *,IFNULL((SELECT COUNT(*) FROM Likes WHERE post.post_id = Likes.post_id), 0) AS post_like_count, 
                IFNULL((SELECT COUNT(*) FROM Likes WHERE post.post_id = Likes.post_id AND Likes.user_id = " . $_SESSION['user_id'] . "), 0) AS user_liked 
                FROM post WHERE user_id = $user_id ORDER BY post_id DESC";
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
        <div class="topnav">
            <button><a class="home" href="/RecipeBook/Recipe-Book/php/home.php">Home</a></button>
            <button><a href="/RecipeBook/Recipe-Book/php/favorite_page.php">View Favorites</a></button>
        </div>
    </header>
    <h1>Hello <?php echo "$user_name" ?>, welcome to your profile!!</h1>
    <button><a href="/RecipeBook/Recipe-Book/php/logout.php">Log out</a></button>
    <button><a href="/RecipeBook/Recipe-Book/html/add_post.html">Add recipe</a></button>
    <h2>All your posts</h2>
    <br />
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $liked = $row['user_liked'] > 0 ? 'liked' : '';
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
            echo "<button class='fav-btn' data-post-id='" . $row['post_id'] . "'>Add to Favorites</button>";
            echo "<button class='like-btn $liked' data-post-id='" . $row['post_id'] . "'>";
            echo "Likes: <span id='like-count-" . $row['post_id'] . "'>" . htmlspecialchars($row['post_like_count']) . "</span>";
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

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/RecipeBook/Recipe-Book/php/add_favorite.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert('Post added to your favorites!'); // alert nai bhayena
                    } else {
                        alert(response.message);
                    }
                }
            };

            xhr.send('post_id=' + postId);
        });
    });
</script>

</html>