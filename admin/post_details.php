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
        echo "No post found with the given ID.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
</head>
<body>
    <img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back">

    <h1>Post Details</h1>
    <?php if (isset($row)) { ?>
        <?php 
            if (($row['post_image'])) {
                echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='width: 200px;'/>";
            } else {
                echo "No image available";
            }
        ?>
        <p><strong>Post ID:</strong> <?php echo $row['post_id']; ?></p>
        <p><strong>Title:</strong> <?php echo $row['post_title']; ?></p>
        <p><strong>Category:</strong> <?php echo $row['post_category']; ?></p>
        <p><strong>Posted Date:</strong> <?php echo $row['post_posted_date']; ?></p>
        <p><strong>Keywords:</strong> <?php echo $row['post_keywords']; ?></p>
        <p><strong>Ingredients:</strong> <?php echo $row['post_ingredients']; ?></p>
        <p><strong>Instructions:</strong> <?php echo $row['post_instructions']; ?></p>
        <p><strong>Description:</strong> <?php echo $row['post_text']; ?></p>
       
    <?php } ?>
</body>
   <script>
        function go_back() {
            window.history.back();
        }
    </script>
</html>
