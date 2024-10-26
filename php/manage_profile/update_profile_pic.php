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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['tmp_name'] != '') {
            $imageFileType = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            $check = getimagesize($_FILES['profile_pic']['tmp_name']);
    
            if ($check !== false && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                if ($_FILES['profile_pic']['size'] > 5000000) { // 5MB limit
                    echo "<script>
                            alert ('Sorry, your file size is too large!!');
                            window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/add_profile_pic.html';
                          </script>";
                    exit();
                }
    
                $imageData = file_get_contents($_FILES['profile_pic']['tmp_name']);
                $imageData = mysqli_real_escape_string($conn, $imageData);

                $sql = "UPDATE user SET user_profile_picture='$imageData' WHERE user_id='$user_id'";
            } else {
                echo "<script>
                        alert ('Invalid image format. Only JPG, JPEG, PNG & GIF files are allowed!!');
                        window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/add_profile_pic.html';
                      </script>";
                exit();
            }
        }

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert ('Profile picture updated successfully!!');
                    window.location.href = '/RecipeBook/Recipe-Book/php/profile.php';
                  </script>";
            exit();
        } else {
            echo "Error adding profile picture: " . $conn->error;
        }
    }

    $conn->close();
?>