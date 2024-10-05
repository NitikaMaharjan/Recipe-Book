<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RecipeBook";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sql = "CREATE TABLE UserTable (
user_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_name VARCHAR(30) NOT NULL UNIQUE,
user_email VARCHAR(50) NOT NULL UNIQUE,
user_password VARCHAR(20) NOT NULL,
user_profile_picture LONGBLOB,
user_reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "UserTabel created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
