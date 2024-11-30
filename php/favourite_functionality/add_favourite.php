<?php
    session_start();

    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
        header("Location: /RecipeBook/Recipe-Book/php/home_for_all.php");
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

    $check_sql = "SELECT * FROM favourite WHERE user_id = $user_id AND post_id = $post_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows>0) {
        $conn->close();
        exit();
    }else{
        $sql = "INSERT INTO favourite (user_id, post_id) VALUES ($user_id, $post_id)";
        if ($conn->query($sql) === TRUE) {
            echo "Post added to favourites successfully";
        } else {
            echo "Error adding post to favourites: " . $conn->error;
        }
    }
    $conn->close();
?>