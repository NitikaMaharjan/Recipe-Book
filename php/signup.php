<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $user_name = trim($_POST['username']);
        $user_email = trim($_POST['email']);
        $user_password = $_POST['password'];
        $user_password2 = $_POST['password2'];

        $pattern = "/^[a-z0-9-]+(\.[a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";

        if (empty($user_name) || empty($user_email) || empty($user_password) || empty($user_password2)){
            echo "<script>
                    alert ('All fields are required. Please try again!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                  </script>";
            exit();
        }else if (!preg_match("/^[a-zA-Z\s]*$/",$user_name)){
            echo "<script>
                    alert ('Username can only contain letters (a-z, A-Z) and whitespaces. Please try again!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                  </script>";
            exit();
        }else if (strlen($user_name) < 3 || strlen($user_name) > 25) {
            echo "<script>
                    alert('Username must be between 3 and 25 characters. Please try again!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                  </script>";
            exit();
        }else if (!preg_match($pattern, $user_email)){
            echo "<script>
                    alert ('Invalid email address format. Please enter a valid email!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                  </script>";
            exit();
        }else if (preg_match('/\s/', $user_password)) {
            echo "<script>
                    alert('Password must not contain spaces. Please try again!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                  </script>";
            exit();
        }else if (strlen($user_password) < 5 || strlen($user_password) > 8) {
            echo "<script>
                    alert('Password must be between 5 and 8 characters. Please try again!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                  </script>";
            exit();
        }else if (strpos($user_password, $user_name) !== false) {
            echo "<script>
                    alert('Password must not contain your username. Please try again!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                  </script>";
            exit();
        }else if($user_password!=$user_password2){
            echo "<script>
                    alert('The passwords do not match. Please try again.');
                    window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                    </script>";
            exit();
        }else{
            $check_user_sql = "SELECT * FROM user WHERE user_name = '$user_name' OR user_email = '$user_email'";
            $result = $conn->query($check_user_sql);

            if ($result->num_rows > 0) {
                echo "<script>
                        alert('Username or email already exists. Please try again!');
                        window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                    </script>";
                exit();
            }

            $sql = "INSERT INTO user (user_name, user_email, user_password) VALUES ('$user_name', '$user_email', '$user_password')";
    
            if ($conn->query($sql) === TRUE) {
                echo "<script>
                            alert('Registration successful. Please log in!');
                            window.location.href = '/RecipeBook/Recipe-Book/html/login.html';
                      </script>";
            } else {
                echo "<script>
                            alert('An error occurred while creating your account. Please try again!');
                            window.location.href = '/RecipeBook/Recipe-Book/html/signup.html';
                      </script>";
            }
        }
    }

    $conn->close();

?>
