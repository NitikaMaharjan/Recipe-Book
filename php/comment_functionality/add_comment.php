<?php
    session_start();

    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
        header("Location: /recipebook/Recipe-Book/html/login.html");
        exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $postId = $_POST['post_id'];
    $commentText = $_POST['comment_text'];
    $userId = $_SESSION['user_id']; 

    $postId = intval($postId);
    $commentText = $conn->real_escape_string($commentText); // Sanitize to prevent SQL injection

    $sql = "INSERT INTO Comment (user_id, post_id, comment_text) VALUES ($userId, $postId, '$commentText')";
    if ($conn->query($sql) === TRUE) {
        echo "Comment added successfully";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
?>
