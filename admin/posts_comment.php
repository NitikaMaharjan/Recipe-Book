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

    if (isset($_GET['post_comment'])) {
        $post_id = $_GET['post_id'];
        $post_title = $_GET['post_title'];

        $postid_sql = "SELECT post_id FROM post WHERE post_id = $post_id";
        $postid_result = $conn->query($postid_sql);

        if ($postid_result->num_rows > 0) {
            $postid_row = $postid_result->fetch_assoc();
            $postId = $postid_row['post_id'];
        } else {
            $postId = "Unknown post"; 
        }

        $sql = "SELECT c.comment_text,u.user_id, u.user_name,u.user_profile_picture, c.commented_at , c.comment_id
                FROM Comment c 
                JOIN User u ON c.user_id = u.user_id 
                WHERE c.post_id = $post_id 
                ORDER BY c.commented_at ASC";    
        $result = $conn->query($sql);
    }

    if (isset($_POST['delete_comment'])) {
        $comment_id = $_POST['comment_id'];
        $delete_comment_sql = "DELETE FROM Comment WHERE comment_id = $comment_id";
        $conn->query($delete_comment_sql);
        echo"<script>
                alert('You have deleted this comment!');
                window.location.href = '/Recipebook/Recipe-Book/admin/posts_comment.php?post_id=$post_id&post_title=$post_title&post_comment=true';
                exit();
            </script>";
    }
?>

<html>
    <head>    
        <title>Recipebook</title>
        <link rel="stylesheet" href="/Recipebook/Recipe-Book/admin/posts_comment.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>
    <body>
        <img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back" onmouseover="onHoverBack()" onmouseout="noHoverBack()">
        <h1><span style="color:#333;">Comments in</span> <?php echo htmlspecialchars($post_title); ?></h1>

        <table>
            <thead>
                <tr>
                    <th>Comment ID</th>
                    <th>Commented By</th>
                    <th>User Image</th>
                    <th>Comment</th>
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
                            echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' class='thumbnail' onclick='showPopup(this)' />";
                        } else {
                            echo "<img src='/Recipebook/Recipe-Book/admin/default_profile_picture.jpg' class='thumbnail' onclick='showPopup(this)' />";
                        }
                        echo "</td>
                                <td>{$row['comment_text']}</td>
                                <td>{$row['commented_at']}</td>
                                <td>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='comment_id' value='{$row['comment_id']}'>
                                        <button type='submit' name='delete_comment' class='delete-button' title='Delete comment' onclick='return confirmit()' ><img class='delete-btn' src='/RecipeBook/Recipe-Book/buttons/remove_button_333.png' onmouseover='onHoverComment(this)' onmouseout='noHoverComment(this)' height='40px' width=''40px></button>
                                    </form>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>There are no comments in this post!</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div id="myModal" class="modal">
            <span class="close" onclick="closePopup()">&times;</span>
            <img class="modal-content" id="modalImg">
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

            function onHoverComment(btn){
                btn.src = '/RecipeBook/Recipe-Book/buttons/remove_button_yellow.png';
            }

            function noHoverComment(btn){
                btn.src = '/RecipeBook/Recipe-Book/buttons/remove_button_333.png';
            }

            function confirmit(){
                var ans = confirm("Are you sure you want to delete this comment?");
                return ans;
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
