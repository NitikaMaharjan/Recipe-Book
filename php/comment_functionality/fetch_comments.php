<?php
    session_start();

    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
        header("Location: /recipebook/Recipe-Book/html/login.html");
        exit();
    }
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

    $sql = "SELECT c.comment_id, c.comment_text,u.user_id, u.user_name,u.user_profile_picture
            FROM Comment c 
            JOIN User u ON c.user_id = u.user_id 
            WHERE c.post_id = $postId 
            ORDER BY c.commented_at ASC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div style='display: flex; align-items:center; justify-content: space-between; gap:10px;'>";
                echo "<div style='display: flex; align-items:center;'>";
                    if (!empty($row['user_profile_picture'])) {
                        // If the user has a profile picture
                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' alt='Profile picture' style='width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;' />";
                    } else {
                        // Default profile picture
                        echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;' />";
                    }
                    echo "<p><b>" . htmlspecialchars($row['user_name']) . ":</b> " . htmlspecialchars($row['comment_text']) ."</p>";
                echo "</div>";
                echo "<div>";
                    if ($row['user_id'] == $_SESSION['user_id']) {   
                        echo "<button class='delete-btn' onclick=\"deleteComment(" . $row['comment_id'] . ")\" style='background-color: #ffbf17; color: white; font-weight:bold; cursor: pointer; border: none; border-radius: 30px; padding: 8px 14px;'>Delete</button>";
                    }
                echo "</div>";
            echo "</div>";  
        }
    } else {
        echo "<p>No comments yet.</p>";
    }

    $conn->close();
    
?>