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

    if (isset($_GET['post_id'])){
        $post_id = (int)$_GET['post_id'];

        $sql = "SELECT * FROM post WHERE post_id = $post_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "<script>
                    alert ('No post found for the provided ID!!');
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert ('No post ID provided for editing!!');
              </script>";
        exit();
    }
?>

<html>
    <head>
        <title>Edit Post</title>
    </head>
    <body>
        <button onclick="go_back()">Go Back</button>
        <h1>Edit your Post</h1>
        <form action="/RecipeBook/Recipe-Book/php/post_functionality/update_post.php?post_id=<?php echo $post_id;?>" method="POST" enctype="multipart/form-data">

            <label for="post_image">Post Image:</label>
            <?php 
                if (($row['post_image'])) {
                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
                } else {
                    echo "No image available";
                } 
            ?><br/><br/>
            <input type="file" name="post_image" accept="image" id="post_image" value="<?php $row['post_image']; ?>"/><br/><br/>

            <label for="post_title">Post Title:</label>
            <input type="text" name="post_title" id="post_title" value="<?php echo htmlspecialchars($row['post_title']); ?>" required/><br/><br/>

            <label for="post_ingredients">Ingredients:</label>
            <textarea name="post_ingredients" id="post_ingredients" required><?php echo htmlspecialchars($row['post_ingredients']); ?></textarea><br/><br/>

            <label for="post_instructions">Instructions:</label>
            <textarea name="post_instructions" id="post_instructions" required><?php echo htmlspecialchars($row['post_instructions']); ?></textarea><br/><br/>

            <label for="post_keywords">Keywords:</label>
            <input type="text" name="post_keywords" id="post_keywords" value="<?php echo htmlspecialchars($row['post_keywords']); ?>" required/><br/><br/>

            <label for="post_category">Category:</label>
            <input type="text" name="post_category" id="post_category" value="<?php echo htmlspecialchars($row['post_category']); ?>" required/><br/><br/>

            <label for="post_text">Description :</label>
            <input type="text" name="post_text" id="post_text" value="<?php echo htmlspecialchars($row['post_text']); ?>" required/><br/><br/>

            <input type="submit" value="Update Post"/>
        </form>
    </body>
    <script>
        function go_back(){
            window.history.back();
        }
    </script>
</html>

<?php
    $conn->close();
?>