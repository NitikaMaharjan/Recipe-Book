<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CREATE TABLE favourite(
            fav_id INT(6) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(6) UNSIGNED,
            post_id INT(6) UNSIGNED,
            fav_added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES User(user_id),
            FOREIGN KEY (post_id) REFERENCES Post(post_id)
        )";

    if ($conn->query($sql) === TRUE) {
        echo "Favourite table created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $conn->close();

?>