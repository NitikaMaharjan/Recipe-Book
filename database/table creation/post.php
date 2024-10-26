<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CREATE TABLE post(
        post_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        post_image LONGBLOB,
        post_title VARCHAR(30) NOT NULL,
        post_ingredients VARCHAR(255) NOT NULL,
        post_instructions VARCHAR(255) NOT NULL,
        post_keywords VARCHAR(100) NOT NULL,
        post_category VARCHAR(30) NOT NULL,
        post_text TEXT,
        post_posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        post_edited_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        post_like_count INT(5) DEFAULT 0,
        user_id INT(6) UNSIGNED,
        FOREIGN KEY (user_id) REFERENCES User(user_id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Post table created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $conn->close();
    
?>
