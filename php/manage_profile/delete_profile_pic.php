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

    $sql = "SELECT user_profile_picture FROM user WHERE user_id='$user_id'";
    $result=$conn->query($sql);
    
    if($result->num_rows==1){
        while($row=$result->fetch_assoc()){
            if($row['user_profile_picture']==NULL){
                echo "<script>
                        alert('You have not added any profile picture, cannot perform deletion!!');
                        window.location.href = '/recipebook/Recipe-Book/html/manage_profile/settings.html';
                      </script>";
                exit();
            }else{
                $sql2 = "UPDATE user SET user_profile_picture=NULL WHERE user_id='$user_id' ";
                if ($conn->query($sql2) === TRUE) {
                    echo "<script>
                            alert('Profile picture deleted successfully!!');
                            window.location.href = '/recipebook/Recipe-Book/php/profile.php';
                            </script>";
                    exit();
                } else {
                    echo "Error deleting profile picture: " . $conn->error;
                }
            }
        }
    }

    $conn->close();
?>
