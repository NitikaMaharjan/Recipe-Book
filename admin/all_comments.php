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



$sql = "SELECT c.comment_text,u.user_id, u.user_name,u.user_profile_picture, c.commented_at , c.comment_id,c.post_id
        FROM Comment c 
        JOIN User u ON c.user_id = u.user_id 
        ORDER BY c.commented_at DESC";    
$result = $conn->query($sql);


if (isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    $delete_comment_sql = "DELETE FROM Comment WHERE comment_id = $comment_id";
    $conn->query($delete_comment_sql); 
    header("Location: /Recipebook/Recipe-Book/admin/all_comments.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All comments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .thumbnail {
            max-width: 50px;
            max-height: 50px;
            border-radius: 8px;
            cursor: pointer;
        }
        /* Popup Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            padding-top: 60px;
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
        .delete-btn {
            color: white;
            background-color: red;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back">
    
    <h1>All Comments</h1>

    <table>
        <thead>
            <tr>
                <th>Comment ID</th>
                <th>Commented by:</th>
                <th>User Image</th>
                <th>Commented on post id:</th>
                <th>Text</th>
                <th>Commented Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['comment_id']}</td>
                                <td>{$row['user_name']}</td>
                                <td>";
                                    if ($row['user_profile_picture']) {
                                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' 
                                            alt='Profile Picture' class='thumbnail' />";
                                    } else {
                                        echo "<img src='/Recipebook/Recipe-Book/admin/default_profile_picture.jpg' 
                                            alt='Default Profile Picture' class='thumbnail' />";
                                    }
                        echo "</td>
                                 <td>
                                    <a href='/Recipebook/Recipe-Book/admin/post_details.php?post_id=" . $row['post_id'] . "'>
                                        " . $row['post_id'] . "
                                    </a>
                                </td>
                                <td>{$row['comment_text']}</td>
                                <td>{$row['commented_at']}</td>
                                <td>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='comment_id' value='{$row['comment_id']}'>
                                        <button type='submit' name='delete_comment' class='delete-btn'>Delete</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center;'>No comments found for this post</td></tr>";
                }
            ?>

        </tbody>
    </table>
    <script>
        function go_back() {
            window.history.back();
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
