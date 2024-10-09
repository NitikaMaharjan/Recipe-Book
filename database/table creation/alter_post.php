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

$sql = "ALTER TABLE Post
ADD COLUMN post_text TEXT;
";

if ($conn->query($sql) === TRUE) {
    echo "post_text column added susccesfully";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
