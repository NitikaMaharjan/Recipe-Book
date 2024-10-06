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

    $user_name = $_POST['username'];
    $user_password = $_POST['password'];

    if (empty($user_name) || empty($user_password)) {
        echo 'All fields are required';
    } else {
        $sql = "SELECT user_name, user_password FROM user WHERE user_name='$user_name' AND user_password='$user_password'";
        $result=$conn->query($sql);

        if ($result->num_rows>0){
            header("Location: /RecipeBook/Recipe-Book/html/homepage2.html");
            exit();
        } else {
            echo "Incorrect username and password " . $conn->error;
        }
    }
}
$conn->close();
