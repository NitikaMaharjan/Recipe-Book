<?php
    session_start();

    // Check if the admin is logged in
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

    // Fetch user data
    $sql = "SELECT user_id,user_profile_picture ,user_name, user_email, user_reg_date FROM user";
    $result = $conn->query($sql);

    if (isset($_POST['delete_user'])){
        $user_id = $_POST['user_id'];
        $user_name= $_POST['user_name'];

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
                    echo"<script>
                            alert('You have deleted user $user_name !');
                            window.location.href = '/RecipeBook/Recipe-Book/admin/users_page.php';
                            exit();
                        </script>";
                } 
            }
        }
    }
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/Recipebook/Recipe-Book/admin/users_page.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo.png" type="image/png">
    </head>
    <body>
        <img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back" onmouseover="onHoverBack()" onmouseout="noHoverBack()">
        <h1 title="All Users"><span style="color:#333;">All</span> Users</h1>

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

                        $posts_sql = "SELECT COUNT(*) AS total_posts FROM post WHERE post.post_status = 'approved' AND user_id = $user_id";
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
                                            alt='Default Profile Picture' class='thumbnail' onclick='showPopup(this)' />";
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
                                        <input type='hidden' name='user_name' value='{$row['user_name']}'>
                                        <button type='submit' name='delete_user' class='delete-button' title='Delete user' onclick='return confirmit()' ><img class='delete-btn' src='/RecipeBook/Recipe-Book/buttons/remove_button_333.png' onmouseover='onHoverRemoveUser(this)' onmouseout='noHoverRemoveUser(this)' height='40px' width=''40px></button>
                                    </form>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center;'>There are no users!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </body>
    <script>
        function go_back() {
            window.location.href="/RecipeBook/Recipe-Book/admin/dashboard.php"
        }

        function onHoverBack(){
            document.querySelector('.back-button').src = '/RecipeBook/Recipe-Book/buttons/back_button2.png';
        }

        function noHoverBack(){
            document.querySelector('.back-button').src = '/RecipeBook/Recipe-Book/buttons/back_button.png';
        }

        function onHoverRemoveUser(btn){
            btn.src = '/RecipeBook/Recipe-Book/buttons/remove_button_yellow.png';
        }

        function noHoverRemoveUser(btn){
            btn.src = '/RecipeBook/Recipe-Book/buttons/remove_button_333.png';
        }

        function confirmit(){
            var ans = confirm("Are you sure you want to delete this user?");
            return ans;
        }

        // Function to show the image in a popup
        function showPopup(img) {
            // Create modal container
            const modal = document.createElement("div");
            modal.classList.add("image-modal");

            // Create enlarged image
            const modalImg = document.createElement("img");
            modalImg.src = img.src;
            modalImg.classList.add("modal-image");
            modal.appendChild(modalImg);

            // Append modal to the document body
            document.body.appendChild(modal);

            // Close modal when clicking outside the image
            modal.addEventListener("click", function (event) {
                if (event.target === modal) {
                    modal.remove();
                }
            });
        }
    </script>
</html>

<?php
    $conn->close();
?>
