<?php
    session_start();

    if (!(isset($_SESSION['adminname']) && isset($_SESSION['admin_loggedin']) && isset($_SESSION['admin_id']))) {
        header("Location: /Recipebook/Recipe-Book/admin/login_admin.html");
        exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipebook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_GET['post_id'])) {

        $post_id = $_GET['post_id'];

        $sql = "SELECT * FROM post WHERE post_id = $post_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "No post found with the given ID";
            exit();
        }
    } else {
        echo "Invalid request.";
        exit();
    }
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/Recipebook/Recipe-Book/admin/post_details.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>
    <body>
        <img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back" onmouseover="onHoverBack()" onmouseout="noHoverBack()">
        <h1><span style="color:#333;">About</span> <?php echo htmlspecialchars($row['post_title'])?></h1>

        <div class="post-container">
            <?php if (isset($row)) { ?>
                <?php
                    echo "<div style='text-align:center;'>";
                        if (($row['post_image'])) {
                            echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='width: 300px; border-radius:8px;'/>";
                        } else {
                            echo "No image available";
                        }
                    echo "</div>";
                ?>
                <p><strong>Post ID:</strong> <?php echo $row['post_id']; ?></p>
                <p><strong>Post Title:</strong> <?php echo $row['post_title']; ?></p>
                <p><strong>Ingredients:</strong> <?php echo $row['post_ingredients']; ?></p>
                <p><strong>Preparation Steps:</strong> <?php echo $row['post_instructions']; ?></p>
                <p><strong>Hashtags</strong> <?php echo $row['post_keywords']; ?></p>
                <p><strong>Category:</strong> <?php echo $row['post_category']; ?></p>
                <p><strong>Note:</strong> <?php echo $row['post_text']; ?></p>
                <p><strong>Posted Date:</strong> <?php echo $row['post_posted_date']; ?></p>
            
            <?php } ?>
        </div>
    </body>
    <script>
        function go_back() {
            window.history.back();
        }

        function onHoverBack(){
            document.querySelector('.back-button').src = '/RecipeBook/Recipe-Book/buttons/back_button2.png';
        }

        function noHoverBack(){
            document.querySelector('.back-button').src = '/RecipeBook/Recipe-Book/buttons/back_button.png';
        }
    </script>
</html>
