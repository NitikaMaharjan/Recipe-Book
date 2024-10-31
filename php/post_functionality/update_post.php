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

    if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_GET['post_id'])) {

        $post_id = (int)$_GET['post_id'];
        $post_title = $_POST['post_title'];
        $post_ingredients = implode(", ", $_POST['post_ingredients']); 
        $post_instructions = implode(", ", $_POST['post_instructions']);
        $post_keywords = $_POST['post_keywords'];
        $post_category = $_POST['post_category'];
        $post_text = $_POST['post_text'];

        if (isset($_FILES['post_image']) && $_FILES['post_image']['tmp_name'] != '') {
            $imageData = file_get_contents($_FILES['post_image']['tmp_name']);
            $imageData = mysqli_real_escape_string($conn, $imageData);
            $sql = "UPDATE post SET post_image='$imageData', post_title='$post_title', post_ingredients='$post_ingredients', post_instructions='$post_instructions', post_keywords='$post_keywords', post_category='$post_category', post_text='$post_text', post_edited_date = CURRENT_TIMESTAMP WHERE post_id='$post_id'";
        } else {
            $sql = "UPDATE post SET post_title='$post_title', post_ingredients='$post_ingredients', post_instructions='$post_instructions', post_keywords='$post_keywords', post_category='$post_category', post_text='$post_text',post_edited_date = CURRENT_TIMESTAMP WHERE post_id='$post_id'";
        }

        if ($conn->query($sql) === TRUE){
            echo "<script>
                    alert ('Post updated successfully!!');
                    window.location.href = '/RecipeBook/Recipe-Book/php/post_functionality/view_post.php?post_id={$post_id}';
                  </script>";
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "<script>
                alert ('No post ID provided for updating!!');
              </script>";
        exit();
    }

    $conn->close();
?>