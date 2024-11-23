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
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="/Recipebook/Recipe-Book/admin/dashboard.css" type="text/css">
    </head>
    <body>
        <div class="dashboard-container">
            <h2>Dashboard</h2>
            <p>
                <a href="/Recipebook/Recipe-Book/admin/users_page.php">Users: <?php echo $user_count; ?></a><br>
                <a href="/Recipebook/Recipe-Book/admin/all_posts.php">Posts: <?php echo $post_count; ?></a><br>
                <a href="/Recipebook/Recipe-Book/admin/all_comments.php">Comments: <?php echo $comment_count; ?></a><br>
            </p>
        </div>
    </body>
</html>

