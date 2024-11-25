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

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user posts
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];

        $user_sql = "SELECT user_name FROM user WHERE user_id = $user_id";
        $user_result = $conn->query($user_sql);

        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $user_name = $user_row['user_name'];
        } else {
            $user_name = "Unknown User"; 
        }

        $sql = "SELECT post.post_id, post.post_title, post.post_image, post.post_keywords, post.post_category, post.post_ingredients, post.post_instructions,post.post_posted_date, post.post_text
                FROM post INNER JOIN user ON post.user_id = user.user_id WHERE post.user_id = $user_id";
        $result = $conn->query($sql);
    }

    if (isset($_POST['delete_post'])) {
        $post_id = $_POST['post_id'];
        //inorder to delete post, first deleting comments
        $sql_delete_comments = "DELETE FROM comment WHERE post_id = $post_id";
        if ($conn->query($sql_delete_comments) === TRUE) {
            //second deleting post from favourite
            $sql_remove_fav = "DELETE FROM favourite WHERE post_id = $post_id";
            if ($conn->query($sql_remove_fav) === TRUE) {
                $delete_post_sql = "DELETE FROM post WHERE post_id = $post_id";
                $conn->query($delete_post_sql); 
                echo"<script>
                        alert('You have deleted the post !');
                        window.location.href = '/Recipebook/Recipe-Book/admin/posts_page.php?user_id=$user_id';
                        exit();
                    </script>";
            }
        }
    }
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/Recipebook/Recipe-Book/admin/posts_page.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>
    <body>
        <img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back" onmouseover="onHoverBack()" onmouseout="noHoverBack()">
        <h1><?php echo htmlspecialchars($user_name); ?>'s Posts</h1>

        <table>
            <thead>
                <tr>
                    <th>Post ID</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Hashtags</th>
                    <th>Category</th>
                    <th>Note</th>
                    <th>Posted Date</th>
                    <th>Additional info</th>
                    <th>Comments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $post_id = $row['post_id'];
                        $comment_count_sql = "SELECT COUNT(*) AS total_comments FROM Comment WHERE post_id = $post_id";
                        $comment_result = $conn->query($comment_count_sql);
                        $comments_row = $comment_result->fetch_assoc();
                        $total_comments = $comments_row['total_comments'];
                        echo "<tr>
                                <td>{$row['post_id']}</td>
                                <td>{$row['post_title']}</td>
                                <td><img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' 
                                            alt='Recipe Image' class='thumbnail' onclick='showPopup(this)'/></td>
                                <td>{$row['post_keywords']}</td>
                                <td>{$row['post_category']}</td>
                                <td>{$row['post_text']}</td>
                                <td>{$row['post_posted_date']}</td>
                                <td>
                                    <button class='show' title='Show additional information' onclick=\"window.location.href='/Recipebook/Recipe-Book/admin/post_details.php?post_id=" . $row['post_id'] . "'\">Show</button>
                                </td>
                                <td>
                                    <form method='GET' action='/Recipebook/Recipe-Book/admin/posts_comment.php'>
                                        <input type='hidden' name='post_id' value='{$row['post_id']}'>
                                        <input type='hidden' name='user_name' value='{$user_name}'>
                                        <button type='submit' name='post_comment' class='comment-btn' title='Show Comments'>Show Comments ($total_comments)</button>
                                    </form>
                                </td>
                                <td>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='post_id' value='{$row['post_id']}'>
                                        <button type='submit' name='delete_post' class='delete-button' title='Delete post' onclick='return confirmit()' ><img class='delete-btn' src='/RecipeBook/Recipe-Book/buttons/remove_button_333.png' onmouseover='onHoverRemovePost(this)' onmouseout='noHoverRemovePost(this)' height='40px' width=''40px></button>
                                    </form>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' style='text-align:center;'>There are no posts from this user!</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div id="myModal" class="modal">
            <span class="close" onclick="closePopup()">&times;</span>
            <img class="modal-content" id="modalImg">
        </div>

        <div id="infoModal" class="modal">
            <div class="infoModel-content">
                <span class="close2" onclick="closeModal()">&times;</span>
                <h2>Post Details</h2>
                <p><strong>Ingredients:</strong></p>
                <p id="modal-ingredients"></p>
                <p><strong>Preparation Steps:</strong></p>
                <p id="modal-instructions"></p>
            </div>
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

        function onHoverRemovePost(btn){
            btn.src = '/RecipeBook/Recipe-Book/buttons/remove_button_yellow.png';
        }

        function noHoverRemovePost(btn){
            btn.src = '/RecipeBook/Recipe-Book/buttons/remove_button_333.png';
        }

        function confirmit(){
            var ans = confirm("Are you sure you want to delete this post?");
            return ans;
        }

        function showPopup(img) {
            var modal = document.getElementById("myModal");
            var modalImg = document.getElementById("modalImg");
            modal.style.display = "block";
            modalImg.src = img.src;
        }

        function closePopup() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }

        // Function to display the modal with post details
        function showInfo(ingredients, instructions) {
            document.getElementById("modal-ingredients").textContent = ingredients;
            document.getElementById("modal-instructions").textContent = instructions;

            // Show the modal
            document.getElementById("infoModal").style.display = "block";
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById("infoModal").style.display = "none";
        }

        // Close modal if user clicks outside of it
        window.onclick = function(event) {
            const modal = document.getElementById("infoModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    </script>
</html>

<?php
$conn->close();
?>
