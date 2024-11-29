<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CREATE TABLE admin(
        admin_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        admin_name VARCHAR(30) NOT NULL UNIQUE,
        admin_email VARCHAR(50) NOT NULL UNIQUE,
        admin_password VARCHAR(20) NOT NULL,
        admin_created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Admin table created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $conn->close();
    
?>
