<?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $user_id = $_SESSION['user_id'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE user SET user_profile_picture=NULL WHERE user_id='$user_id' ";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Profile picture deleted successfully!!');
                window.location.href = '/recipebook/Recipe-Book/php/profile.php';
                </script>";
        exit();
    } else {
        echo "Error deleting profile picture: " . $conn->error;
    }

    $conn->close();
?>
