<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipebook";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "CREATE TABLE IF NOT EXISTS Likes (  
    like_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    post_id INT(6) UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES Post(post_id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'Likes' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}


$conn->close();
