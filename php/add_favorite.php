<?php
session_start();
if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
    header("Location: /RecipeBook/Recipe-Book/html/login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RecipeBook";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if the post is already in the user's favorites
$check_sql = "SELECT * FROM Favourite WHERE user_id = $user_id AND post_id = $post_id";
$check_result = $conn->query($check_sql);
if ($check_result->num_rows > 0) {
    echo "<script> alert ('This post is already in your favorites');</script>";
    $conn->close();
    exit();
}
// Insert the favorite entry into the Favourite table
$sql = "INSERT INTO Favourite (user_id, post_id) VALUES ($user_id, $post_id)";
if ($conn->query($sql) === TRUE) {
    echo "<script> alert ('Post added to your favorites');</script>"; // this line didnt work
} else {
    echo "Error adding to favourites: " . $conn->error;
}
$conn->close();