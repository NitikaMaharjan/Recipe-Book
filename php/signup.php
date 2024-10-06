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
    $user_email = $_POST['email'];
    $user_password = $_POST['password'];

    if (empty($user_name) || empty($user_email) || empty($user_password)) {
        echo 'All fields are required';
    } else {
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO User (user_name, user_email, user_password) VALUES ('$user_name', ' $user_email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            header("Location: /RecipeBook/Recipe-Book/html/login.html");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
$conn->close();
