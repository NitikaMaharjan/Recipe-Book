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

        $current_password = $_POST['currentpassword'];
        $new_password = $_POST['newpassword'];
        $confirm_password = $_POST['confirmpassword'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            echo "<script>
                    alert('All fields are required!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/change_password.html';
                </script>";
            exit();
        } else {
            $sql = "SELECT user_password, user_name FROM user WHERE user_id='$user_id'";
            $result = $conn->query($sql);

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $stored_password = $row['user_password'];
                $user_name = $row['user_name'];

                if ($current_password == $stored_password) {

                    if (strlen($new_password) < 5 || strlen($new_password) > 8) {
                        echo "<script>
                                alert('New password must be between 5 and 8 characters. Please try again!!');
                                window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/change_password.html';
                            </script>";
                        exit();
                    }

                    if (preg_match('/\s/', $new_password)) {
                        echo "<script>
                                alert('New password must not contain spaces. Please try again!!');
                                window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/change_password.html';
                            </script>";
                        exit();
                    }

                    if (strpos($new_password, $user_name) !== false) {
                        echo "<script>
                                alert('New password must not contain your username. Please try again!!');
                                window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/change_password.html';
                            </script>";
                        exit();
                    }

                    if ($new_password != $confirm_password) {
                        echo "<script>
                                alert('New password and confirm password must match. Please try again!!');
                                window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/change_password.html';
                            </script>";
                        exit();
                    }

                    $sql2 = "UPDATE user SET user_password='$new_password' WHERE user_id='$user_id'";

                    if ($conn->query($sql2) === TRUE) {
                        echo "<script>
                                alert('Password updated successfully!');
                                window.location.href = '/RecipeBook/Recipe-Book/php/profile.php';
                            </script>";
                        exit();
                    } else {
                        echo "Error updating password: " . $conn->error;
                    }

                } else {
                    echo "<script>
                            alert('Incorrect current password. Please try again!!');
                            window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/change_password.html';
                        </script>";
                    exit();
                }
            }
        }
    }
    $conn->close();
?>