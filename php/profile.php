<?php
session_start();

if (!isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    header("Location: /RecipeBook/Recipe-Book/html/login.php");
    exit();
}
$user_name = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RecipeBook";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Post WHERE user_id = $user_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile page</title>
</head>

<body>
    <h1>Welcome, <?php echo "$user_name" ?></h1>
    <button><a href="/RecipeBook/Recipe-Book/html/create_post.html">Add recipe</a></button>
    <?php
    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<h3>Title:" . htmlspecialchars($row['post_title']) . "</h3>";

            if (($row['post_image'])) {
                echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
            } else {
                echo "No image available";
            }
            echo "<p><b>Ingrediants</b>:" . htmlspecialchars($row['post_ingredients']) . "</p>";
            echo "<p><b>Instructions</b>:" . htmlspecialchars($row['post_instructions']) . "</p>";
            echo "<p><b>Keywords</b>:" . htmlspecialchars($row['post_keywords']) . "</p>";
            echo "<p><b>Category</b>:" . htmlspecialchars($row['post_category']) . "</p>";
            echo "<p><b>Posted on</b>: " . htmlspecialchars($row['post_posted_date']) . "</p>";
            echo "</div><hr>";
        }
    } else {
        echo "<p>You have not posted any recipes.   </p>";
    }


    $conn->close();
    ?>

</body>

</html>