<?php
session_start();

// Check if the admin is logged in
if (!(isset($_SESSION['adminname']) && isset($_SESSION['admin_loggedin']) && isset($_SESSION['admin_id']))) {
    header("Location: /Recipebook/Recipe-Book/admin/login_admin.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipebook";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    //inorder to delete post, first deleting comments
    $sql_delete_comments = "DELETE FROM comment WHERE user_id = $user_id";
    if ($conn->query($sql_delete_comments) === TRUE) {
        //second deleting post from favourite
        $sql_remove_fav = "DELETE FROM favourite WHERE user_id = $user_id";
        if ($conn->query($sql_remove_fav) === TRUE) {
            //third delete the posts 
            $delete_post_sql = "DELETE FROM post WHERE user_id = $user_id";
            if( $conn->query($delete_post_sql) === TRUE){
                //then only can delete user, natra foreign key constarint le error dincha
                $delete_user_sql = "DELETE FROM user WHERE user_id = $user_id";
                $conn->query($delete_user_sql);
            } 
        }
    }
   
    header("Location: /Recipebook/Recipe-Book/admin/users_page.php");
    exit();
}

// Fetch user data
$sql = "SELECT user_id,user_profile_picture ,user_name, user_email, user_reg_date FROM user";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
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
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .delete-btn, .show-posts-btn {
            color: white;
            background-color: red;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .delete-btn:hover, .show-posts-btn:hover {
            background-color: darkred;
        }
        .show-posts-btn {
            background-color: #007bff;
        }
        .show-posts-btn:hover {
            background-color: darkblue;
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
    </style>
</head>
<body>
<img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back">
<h1>All Users</h1>

<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Profile Picture</th>
            <th>Username</th>
            <th>Email</th>
            <th>Registration Date</th>
            <th>Posts</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $user_id = $row['user_id'];
                $posts_sql = "SELECT COUNT(*) AS total_posts FROM post WHERE user_id = $user_id";
                $posts_result = $conn->query($posts_sql);
                $posts_row = $posts_result->fetch_assoc();
                $total_posts = $posts_row['total_posts'];

                echo "<tr>
                        <td>{$row['user_id']}</td>
                        <td>";
                            if ($row['user_profile_picture']) {
                                echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' 
                                    alt='Profile Picture' class='thumbnail' onclick='showPopup(this)' />";
                            } else {
                                echo "<img src='/Recipebook/Recipe-Book/admin/default_profile_picture.jpg' 
                                    alt='Default Profile Picture' class='thumbnail' />";
                            }
                        echo "</td>
                        <td>{$row['user_name']}</td>
                        <td>{$row['user_email']}</td>
                        <td>{$row['user_reg_date']}</td>
                        <td>
                            <form method='GET' action='/Recipebook/Recipe-Book/admin/posts_page.php'>
                                <input type='hidden' name='user_id' value='{$row['user_id']}'>
                                <button type='submit' class='show-posts-btn'>Show Posts ($total_posts)</button>
                            </form>
                        </td>
                        <td>
                            <form method='POST' action=''>
                                <input type='hidden' name='user_id' value='{$row['user_id']}'>
                                <button type='submit' name='delete_user' class='delete-btn'>Delete</button>
                            </form>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>No users found</td></tr>";
        }
        ?>
    </tbody>
</table>
    <!-- The Modal -->
    <div id="myModal" class="modal">
    <span class="close" onclick="closePopup()">&times;</span>
    <img class="modal-content" id="modalImg">
    </div>

    <script>
        function go_back() {
            window.history.back();
        }
        // Function to show the image in a popup
        function showPopup(img) {
            var modal = document.getElementById("myModal");
            var modalImg = document.getElementById("modalImg");
            modal.style.display = "block";
            modalImg.src = img.src;
        }

        // Function to close the popup
        function closePopup() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
