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

    $user_id = $_SESSION['user_id'];

    
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
