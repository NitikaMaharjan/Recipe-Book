<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo "<script>
        alert('You haven\'t logged in !!!');
        window.location.href = '/RecipeBook/Recipe-Book/html/login.html';
    </script>";
    exit();
}
?>
<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RecipeBook";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_title = $_POST['post_title'];
    $post_ingredients = $_POST['post_ingredients'];
    $post_instructions = $_POST['post_instructions'];
    $post_keywords = $_POST['post_keywords'];
    $post_category = $_POST['post_category'];
    $post_text = $_POST['post_text'];
    $user_id = $_SESSION['user_id'];
    $imageData = file_get_contents($_FILES['post_image']['tmp_name']);
    $imageData = mysqli_real_escape_string($conn, $imageData);


    $sql = "INSERT INTO Post (post_image, post_title, post_ingredients, post_instructions, post_keywords, post_category, user_id, post_text)
        VALUES ('$imageData', '$post_title', '$post_ingredients', '$post_instructions', '$post_keywords', '$post_category', '$user_id','$post_text')";


    if ($conn->query($sql) === TRUE) {
        echo "Post created successfully.";
        header("Location: /RecipeBook/Recipe-Book/php/profile.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>