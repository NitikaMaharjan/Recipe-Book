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
// Retrieve the favorite posts for the logged-in user
$sql = "SELECT post.*, user.user_name 
        FROM Favourite 
        JOIN post ON Favourite.post_id = post.post_id 
        JOIN user ON post.user_id = user.user_id 
        WHERE Favourite.user_id = $user_id 
        ORDER BY Favourite.fav_added_date DESC";
$result = $conn->query($sql);
?>
<html>
<head>
    <title>My Favorite Posts</title>
</head>
<style>
    .post {
        cursor: pointer;
        padding: 10px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
        transition: background-color 0.3s ease;
    }
</style>
<body>
    <h1><?php echo "$user_name's Favorite Posts" ?></h1>
    <a href="/Recipebook/Recipe-Book/php/home.php">Back to Home</a>
    <br /><br />
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='post'>";
            echo "<h3>" . htmlspecialchars($row['post_title']) . "</h3>";
            echo "<p>Posted by <b>" . htmlspecialchars($row['user_name']) . "</b> on <b>" . htmlspecialchars($row['post_posted_date']) . "</b></p>";
            echo "<p>Category : " . htmlspecialchars($row['post_category']) . "</p>";
            if (($row['post_image'])) {
                echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
            } else {
                echo "No image available";
            }
            echo "<p>" . htmlspecialchars($row['post_text']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>You have no favorite posts.</p>";
    }
    $conn->close();
    ?>
</body>
</html>