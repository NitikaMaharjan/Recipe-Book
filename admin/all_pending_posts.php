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

    $sql = "SELECT pending_post.*, user.user_name FROM pending_post JOIN user ON pending_post.user_id = user.user_id";
    $result = $conn->query($sql);

    if (isset($_POST['disapprove_post'])) {
        $post_id = $_POST['post_id'];
        $disapprove_post_sql = "DELETE FROM pending_post WHERE post_id = $post_id";
        if ($conn->query($disapprove_post_sql)== TRUE){
            echo"<script>
                alert('You have disapproved this post!');
                window.location.href = '/Recipebook/Recipe-Book/admin/all_pending_posts.php';
                exit();
            </script>"; 
        } 
    }

    if (isset($_POST['approve_post'])) {
        $post_id = $_POST['post_id'];
    
        $approve_post_sql = "SELECT * FROM pending_post WHERE post_id = $post_id";
        $result2 = $conn->query($approve_post_sql);
    
        if ($result2 && $result2->num_rows > 0) {
            $row = $result2->fetch_assoc();
    
            $imageData = $conn->real_escape_string($row['post_image']);
            $post_title = $conn->real_escape_string($row['post_title']);
            $post_ingredients = $conn->real_escape_string($row['post_ingredients']);
            $post_instructions = $conn->real_escape_string($row['post_instructions']);
            $post_keywords = $conn->real_escape_string($row['post_keywords']);
            $post_category = $conn->real_escape_string($row['post_category']);
            $user_id = $row['user_id'];
            $post_text = $conn->real_escape_string($row['post_text']);
    
            $add_into_post_table_sql = "INSERT INTO post (post_image, post_title, post_ingredients, post_instructions, post_keywords, post_category, user_id, post_text)
                                        VALUES ('$imageData', '$post_title', '$post_ingredients', '$post_instructions', '$post_keywords', '$post_category', $user_id, '$post_text')";
            $delete_pending_post_sql = "DELETE FROM pending_post WHERE post_id = $post_id";
            $conn->query($delete_pending_post_sql);

            if ($conn->query($add_into_post_table_sql) == TRUE) {
                echo "<script>
                        alert('You have approved this post!');
                        window.location.href = '/Recipebook/Recipe-Book/admin/all_pending_posts.php';
                      </script>";
            }
        }
    }    
    
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/Recipebook/Recipe-Book/admin/all_pending_posts.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>
    <body>
        <img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back" onmouseover="onHoverBack()" onmouseout="noHoverBack()">
        <h1 title="All Pending Posts"><span style="color:#333;">All</span> Pending Posts</h1>

        <table>
            <thead>
                <tr>
                    <th>Post ID</th>
                    <th>Posted By</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Hashtags</th>
                    <th>Category</th>
                    <th>Note</th>
                    <th>Posted Date</th>
                    <th>All Information</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $post_id = $row['post_id'];
                        $post_title = $row['post_title'];
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
                                    <button class='show' title='Show all information' onclick=\"window.location.href='/Recipebook/Recipe-Book/admin/pending_post_details.php?post_id=" . $row['post_id'] . "'\">Show</button>
                                </td>
                                <td>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='post_id' value='{$row['post_id']}'>
                                        <button type='submit' name='approve_post' class='approve-btn' title='Approve post' onclick='return confirmit2()' >Approve</button>
                                    </form>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='post_id' value='{$row['post_id']}'>
                                        <button type='submit' name='disapprove_post' class='disapprove-btn' title='Disapprove post' onclick='return confirmit()' >Disapprove</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' style='text-align:center;'>There are no pending posts!</td></tr>";
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

        function confirmit2(){
            var ans = confirm("Are you sure you want to approve this post?");
            return ans;
        }

        function confirmit(){
            var ans = confirm("Are you sure you want to disapprove this post?");
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
    </script>
</html>

<?php
    $conn->close();
?>