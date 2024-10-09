<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RecipeBook";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['post_id'])){

    $post_id = (int)$_GET['post_id'];
    $post_title = $_POST['post_title'];
    $post_ingredients = $_POST['post_ingredients'];
    $post_instructions = $_POST['post_instructions'];
    $post_keywords = $_POST['post_keywords'];
    $post_category = $_POST['post_category'];
    $post_text = $_POST['post_text'];
    $imageData = file_get_contents($_FILES['post_image']['tmp_name']);
    $imageData = mysqli_real_escape_string($conn, $imageData);

    $sql = "UPDATE post SET post_image='$imageData', post_title='$post_title', post_ingredients='$post_ingredients', post_instructions='$post_instructions', post_keywords='$post_keywords', post_category='$post_category',post_text='$post_text' WHERE post_id='$post_id'";

    if ($conn->query($sql) === TRUE){
        echo "Post updated successfully.";
        header("Location: /RecipeBook/Recipe-Book/php/profile.php");
        exit();
    }else{
        echo "Error: " . $conn->error;
    }

}else{
    echo "No post ID provided for updating.";
}

$conn->close();
?>