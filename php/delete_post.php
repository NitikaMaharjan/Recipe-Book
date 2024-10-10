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

    if (isset($_GET['post_id'])){

        $post_id = (int)$_GET['post_id'];
        
        $sql = "DELETE FROM post WHERE post_id = " . $post_id;

        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully";
            header("Location: /RecipeBook/Recipe-Book/php/profile.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "No post ID provided for deletion.";
    }

    $conn->close();
?>
