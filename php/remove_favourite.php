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

    $sql = "DELETE FROM favourite WHERE user_id = $user_id AND post_id = $post_id";
    if ($conn->query($sql) === TRUE){
        echo "Post removed from favourites successfully";
    } else {
        echo "Error removing post from favourites: " . $conn->error;
    }
    $conn->close();
?>