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

$sql = "SELECT * FROM post WHERE user_id = $user_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <h1>Welcome, <?php echo "$user_name" ?></h1>
    <button><a href="/RecipeBook/Recipe-Book/php/logout.php">Log out</a></button>
    <button><a href="/RecipeBook/Recipe-Book/html/create_post.html">Add recipe</a></button>
    <?php
    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            $_SESSION['post_to_be_deleted'] = $row['post_id'];
            echo "<div class='post' onclick='viewPost(" . $row['post_id'] . ")'>";
            echo "<h3>Title:" . htmlspecialchars($row['post_title']) . "</h3>";
            echo "<p>" . htmlspecialchars($row['post_text']) . "</p>";

            if (($row['post_image'])) {
                echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
            } else {
                echo "No image available";
            }
            if ($row['post_edited_date'] != $row['post_posted_date']) {
                echo "<p><b>Post edited on</b>: " . htmlspecialchars($row['post_edited_date']) . "</p>";
            } else {
                echo "<p><b>Posted on</b>: " . htmlspecialchars($row['post_posted_date']) . "</p>";
            }
            echo "<button onclick='edit(" . $row['post_id'] . ")'>Edit post</button>";
            echo "<button onclick='confirm_box(" . $row['post_id'] . ")'>Delete post</button>";
            echo "</div>";
        }
    } else {
        echo "<p>You have not posted any recipes.   </p>";
    }
    $conn->close();
    ?>

</body>
<script>
    function confirm_box(post_id) {
        event.stopPropagation();
        var ans = confirm("Are you sure you want to delete this post?");
        if (ans == true) {
            window.location.href = "/RecipeBook/Recipe-Book/php/delete_post.php?post_id=" + post_id;
        }
    }

    function edit(post_id) {
        event.stopPropagation();
        window.location.href = "/RecipeBook/Recipe-Book/php/edit_post.php?post_id=" + post_id;
    }

    function viewPost(post_id) {
        window.location.href = "/RecipeBook/Recipe-Book/php/view_post.php?post_id=" + post_id;
    }
</script>

</html>