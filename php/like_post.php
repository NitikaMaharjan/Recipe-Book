<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to like a post.']);
    exit();
}

$user_id = $_SESSION['user_id']; // Assume user_id is stored in the session
$post_id = $_POST['post_id'];

// Validate the post ID
if (!isset($post_id) || !is_numeric($post_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID.']);
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "recipebook");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to check if the user already liked the post
$check_like_sql = "SELECT * FROM Likes WHERE user_id = $user_id AND post_id = $post_id";
$check_result = $conn->query($check_like_sql);

// If the user already liked the post, remove the like
if ($check_result->num_rows > 0) {
    $conn->query("DELETE FROM Likes WHERE user_id = $user_id AND post_id = $post_id");
    $conn->query("UPDATE post SET post_like_count = post_like_count - 1 WHERE post_id = $post_id");
    $action = 'unliked';
} else {
    // If the user has not liked the post yet, add the like
    $conn->query("INSERT INTO Likes (user_id, post_id) VALUES ($user_id, $post_id)");
    $conn->query("UPDATE post SET post_like_count = post_like_count + 1 WHERE post_id = $post_id");
    $action = 'liked';
}

// Get the updated like count
$count_result = $conn->query("SELECT post_like_count FROM post WHERE post_id = $post_id");
$row = $count_result->fetch_assoc();
$newLikeCount = $row['post_like_count'];

// Return a JSON response
echo json_encode(['success' => true, 'action' => $action, 'newLikeCount' => $newLikeCount]);

$conn->close();
?>
