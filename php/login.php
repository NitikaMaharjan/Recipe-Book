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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $user_name = $_POST['username'];
        $user_password = $_POST['password'];

        if (empty($user_name) || empty($user_password)) {
            echo "<script>
                    alert ('All fields are required!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/login.html';
                  </script>";
            exit();
        } else {
            $sql = "SELECT user_name, user_password, user_id FROM user WHERE user_name='$user_name' AND user_password='$user_password'";
            $result = $conn->query($sql);

            if ($result->num_rows==1) {
                $row = $result->fetch_assoc();

                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $row['user_name'];
                $_SESSION['user_id'] = $row['user_id'];

                echo "<script>
                    alert('Successful log in!');
                    window.location.href = '/RecipeBook/Recipe-Book/php/home.php';
                  </script>";
                exit();
            }else{
                echo "<script>
                        alert ('Incorrect username and password, Please try again!!');
                        window.location.href = '/RecipeBook/Recipe-Book/html/login.html';
                      </script>";
                exit();
            }
        }

    }

    $conn->close();
?>