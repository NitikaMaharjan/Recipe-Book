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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $current_password = $_POST['currentpassword'];
        $new_password = $_POST['newpassword'];
        $confirm_password = $_POST['confirmpassword'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            echo "<script>
                    alert ('All fields are required!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/change_password.html';
                  </script>";
        } else {
            $sql = "SELECT user_password FROM user WHERE user_id='$user_id' AND user_password='$current_password'";
            $result = $conn->query($sql);

            if ($result->num_rows==1){
                if($new_password==$confirm_password){

                    $sql2 = "UPDATE user SET user_password='$new_password' WHERE user_id='$user_id'";

                    if ($conn->query($sql2) === TRUE) {
                        echo "<script>
                                alert ('Password updated successfully!!');
                                window.location.href = '/RecipeBook/Recipe-Book/php/profile.php';
                              </script>";
                    } else {
                        echo "Error updating password: " . $conn->error;
                    }
                }else{
                    echo "<script>
                        alert ('New password and Confirm password should match, Please try again!!');
                        window.location.href = '/RecipeBook/Recipe-Book/html/change_password.html';
                      </script>".$conn->error;
                } 
            }else{
                echo "<script>
                    alert ('Incorrect password, Please try again!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/change_password.html';
                  </script>".$conn->error;
            }
        }
    }
    $conn->close();
?>