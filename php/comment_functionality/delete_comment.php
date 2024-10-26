<?php
    session_start();

    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
        header("Location: /Recipebook/Recipe-Book/html/login.html");
        exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipebook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $commentId = intval($_POST['comment_id']);
    $userId = $_SESSION['user_id'];


    $sql = "SELECT * FROM Comment WHERE comment_id = $commentId AND user_id = $userId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $deleteSql = "DELETE FROM Comment WHERE comment_id = $commentId";
        if ($conn->query($deleteSql) === TRUE) {
            echo "Comment deleted successfully.";
        } else {
            echo "Error deleting comment: " . $conn->error;
        }
    } else {
        echo "You are not authorized to delete this comment.";
    }

    $conn->close();
?>
