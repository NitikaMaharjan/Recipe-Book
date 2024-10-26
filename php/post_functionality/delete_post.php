<?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_GET['post_id'])) {
        $post_id = (int)$_GET['post_id'];

        // Delete comments first 
        $sql_delete_comments = "DELETE FROM comment WHERE post_id = $post_id";
        if ($conn->query($sql_delete_comments) === TRUE) {
            
            // then delete from favourite
            $sql_remove_fav = "DELETE FROM favourite WHERE post_id = $post_id";
            if ($conn->query($sql_remove_fav) === TRUE) {

                // Now delete the post
                $sql = "DELETE FROM post WHERE post_id = $post_id";
                if ($conn->query($sql) === TRUE) {
                    echo "<script>
                            alert('Post deleted successfully!!');
                            window.location.href = '/recipebook/Recipe-Book/php/profile.php';
                        </script>";
                    exit();
                } else {
                    echo "Error deleting post: " . $conn->error;
                }
            } else {
                echo "Error deleting from favourite: " . $conn->error;
            }
        } else {
            echo "Error deleting comments: " . $conn->error;
        }
    } else {
        echo "<script>
                alert('No post ID provided for deletion!!');
            </script>";
        exit();
    }

    $conn->close();
?>