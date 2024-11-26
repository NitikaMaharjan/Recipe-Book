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



    $sql = "SELECT post.post_id, post.post_title, post.post_image, post.post_keywords, post.post_category, post.post_ingredients, post.post_instructions,post.post_posted_date, post.post_text, user.user_name FROM post INNER JOIN user ON post.user_id = user.user_id";
    $result = $conn->query($sql);


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
                }
            }
        header("Location: /Recipebook/Recipe-Book/admin/all_posts.php");
        exit();
    }
?>

<html>
    <head>
        <title>All Posts</title>
    </head>
    <body>
        <img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back">

        <h1>All Posts</h1>

        <table>
            <thead>
                <tr>
                    <th>Post ID</th>
                    <th>Posted By</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Keywords</th>
                    <th>Category</th>
                    <th>Description</th>
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
                                <td>{$row['user_name']}</td>
                                <td>{$row['post_title']}</td>
                                <td><img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' 
                                            alt='Recipe Image' class='thumbnail' onclick='showPopup(this)'/></td>
                                <td>{$row['post_keywords']}</td>
                                <td>{$row['post_category']}</td>
                                <td>{$row['post_text']}</td>
                                <td>{$row['post_posted_date']}</td>
                                <td>
                                    <button onclick=\"showInfo(
                                        '" . addslashes($row['post_ingredients']) . "',
                                        '" . addslashes($row['post_instructions']) . "'
                                    )\">Show</button>
                                </td>
                                <td>
                                    <form method='GET' action='/Recipebook/Recipe-Book/admin/posts_comment.php'>
                                        <input type='hidden' name='post_id' value='{$row['post_id']}'>
                                        <button type='submit' name='post_comment' class='comment-btn'>Comment ($total_comments)</button>
                                    </form>
                                </td>
                                <td>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='post_id' value='{$row['post_id']}'>
                                        <button type='submit' name='delete_post' class='delete-btn'>Delete</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' style='text-align:center;'>No posts found for this user</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- The Modal -->
        <div id="myModal" class="modal">
            <span class="close" onclick="closePopup()">&times;</span>
            <img class="modal-content" id="modalImg">
        </div>
        <div id="infoModal" class="modal">
            <div class="infoModel-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Post Details</h2>
                <p><strong>Ingredients:</strong></p>
                <p id="modal-ingredients"></p>
                <p><strong>Instructions:</strong></p>
                <p id="modal-instructions"></p>
            </div>
        </div>
    </body>
    <script>
        function go_back() {
            window.history.back();
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
