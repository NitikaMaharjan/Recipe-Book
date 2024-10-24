<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $postId = $_GET['post_id'];

    
    $postId = intval($postId);

    $sql = "SELECT c.comment_text, u.user_name, c.commented_at 
            FROM Comment c 
            JOIN User u ON c.user_id = u.user_id 
            WHERE c.post_id = $postId 
            ORDER BY c.commented_at ASC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<p><b>" . htmlspecialchars($row['user_name']) . ":</b> " . htmlspecialchars($row['comment_text']) . " <i>(" . $row['commented_at'] . ")</i></p>";
        }
    } else {
        echo "<p>No comments yet.</p>";
    }

    $conn->close();
?>
