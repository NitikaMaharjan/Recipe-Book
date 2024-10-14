<?php
session_start();
if (!(isset($_SESSION['user_id']) && isset($_POST['post_id']))) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
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

$post_id = intval($_POST['post_id']);
$user_id = $_SESSION['user_id'];

// Check if the user has already liked this post
$check_like = $conn->query("SELECT * FROM Likes WHERE post_id = $post_id AND user_id = $user_id");

if ($check_like->num_rows > 0) {
    // Unlike the post (delete the like)
    $conn->query("DELETE FROM Likes WHERE post_id = $post_id AND user_id = $user_id");
    $new_like_count = $conn->query("SELECT COUNT(*) AS like_count FROM Likes WHERE post_id = $post_id")->fetch_assoc()['like_count'];
    echo json_encode(['success' => true, 'liked' => false, 'newLikeCount' => $new_like_count]);
} else {
    // Like the post (insert a new like)
    $conn->query("INSERT INTO Likes (post_id, user_id) VALUES ($post_id, $user_id)");
    $new_like_count = $conn->query("SELECT COUNT(*) AS like_count FROM Likes WHERE post_id = $post_id")->fetch_assoc()['like_count'];
    echo json_encode(['success' => true, 'liked' => true, 'newLikeCount' => $new_like_count]);
}

$conn->close();
