<?php
    session_start();

    if (!(isset($_SESSION['adminname']) && isset($_SESSION['admin_loggedin']) && isset($_SESSION['admin_id']))) {
        header("Location: /Recipebook/Recipe-Book/admin/login_admin.html");
        exit();
    }
    $admin_name = $_SESSION['adminname'];
    $admin_id = $_SESSION['admin_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipebook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $user_count_query = 'SELECT COUNT(*) AS total_users FROM user;';
    $post_count_query = 'SELECT COUNT(*) AS total_posts FROM post;';
    $comment_count_query = 'SELECT COUNT(*) AS total_comment FROM comment;';

    $user_count_result = $conn->query($user_count_query);
    $post_count_result = $conn->query($post_count_query);
    $comment_count_result = $conn->query($comment_count_query);

    if ($user_count_result->num_rows > 0) {
        $user_count = $user_count_result->fetch_assoc()['total_users'];
    }
    if ($post_count_result->num_rows > 0) {
        $post_count = $post_count_result->fetch_assoc()['total_posts'];
    }

    if ($comment_count_result->num_rows > 0) {
        $comment_count = $comment_count_result->fetch_assoc()['total_comment'];
    }
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/Recipebook/Recipe-Book/admin/dashboard.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>
    <body>
        <form action="/Recipebook/Recipe-Book/admin/logout_admin.php" method="post">
            <button type="submit">Logout</button>
        </form>

        <div class="dashboard-container">
            <h1>Dashboard</h1>
            <div class="dashboard-buttons">
                <div>
                    <button onclick="window.location.href='/Recipebook/Recipe-Book/admin/users_page.php'">Users: <?php echo $user_count; ?></button>
                </div>
                <div>
                    <button onclick="window.location.href='/Recipebook/Recipe-Book/admin/all_posts.php'">Posts: <?php echo $post_count; ?></button>
                </div>
                <div>
                    <button onclick="window.location.href='/Recipebook/Recipe-Book/admin/all_comments.php'">Comments: <?php echo $comment_count; ?></button>
                </div>
            </div>
        </div>

    </body>
</html>

