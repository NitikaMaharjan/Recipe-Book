<?php
    session_start();

    if (!isset($_SESSION['username']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false){
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    } 

    if (!isset($_GET['post_id'])) {
        echo "<script>
                alert ('No post ID provided for viewing!!');
              </script>";
        exit();
    }

    $post_id = $_GET['post_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT post.*, user.user_name FROM post JOIN user ON post.user_id = user.user_id WHERE post.post_id =$post_id";
    $result = $conn->query($sql);

    if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>
                alert ('No post found!!');
              </script>";
        exit();
    }
?>

<html>
    <head>
        <title>Post Details</title>
    </head>
    <body>
        <header>
            <div class="topnav">
                <button><a class="profile" href="/RecipeBook/Recipe-Book/php/profile.php">Profile</a></button>
                <button><a class="home" href="/RecipeBook/Recipe-Book/php/home.php">Home</a></button>     
            </div>
        </header>
        <h1><?php echo htmlspecialchars($row['post_title']); ?></h1>
        <p><?php echo htmlspecialchars($row['post_text']); ?></p>

        <?php
            if (($row['post_image'])) {
                echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
            } else {
                echo "No image available";
            }
            echo "<p><b>Ingrediants</b>:" . htmlspecialchars($row['post_ingredients']) . "</p>";
            echo "<p><b>Instructions</b>:" . htmlspecialchars($row['post_instructions']) . "</p>";
            echo "<p><b>Keywords</b>:" . htmlspecialchars($row['post_keywords']) . "</p>";
            echo "<p><b>Category</b>:" . htmlspecialchars($row['post_category']) . "</p>";
            
            if ($row['post_edited_date'] != $row['post_posted_date']) {
                echo "<p>Posted by <b>" . htmlspecialchars($row['user_name']) . " </b>and edited on<b> ".htmlspecialchars($row['post_edited_date'])."</b></p>";
            } else {
                echo "<p>Posted by <b>" . htmlspecialchars($row['user_name']) . " </b>on<b> ".htmlspecialchars($row['post_edited_date'])."</b></p>";
            }

            if($_SESSION['user_id']==$row['user_id']){
                echo "<button onclick='edit_post(" . $row['post_id'] . ")'>Edit post</button>";
                echo "<button onclick='delete_post(" . $row['post_id'] . ")'>Delete post</button>";
            }
        ?>
    </body>
    <script>
        function edit_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/edit_post.php?post_id=" + post_id;
        }

        function delete_post(post_id) {
            var ans = confirm("Are you sure you want to delete this post?");
            if (ans == true) {
                window.location.href = "/RecipeBook/Recipe-Book/php/delete_post.php?post_id=" + post_id;
            }
        }     
    </script>
</html>

<?php 
    $conn->close();
?>