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
    $pending_post_count_query = "SELECT COUNT(*) AS total_pending_posts FROM post WHERE post.post_status = 'disapproved';";
    $post_count_query = "SELECT COUNT(*) AS total_posts FROM post WHERE post.post_status = 'approved';";
    $comment_count_query = 'SELECT COUNT(*) AS total_comment FROM comment;';

    $user_count_result = $conn->query($user_count_query);
    $pending_post_count_result = $conn->query($pending_post_count_query);
    $post_count_result = $conn->query($post_count_query);
    $comment_count_result = $conn->query($comment_count_query);

    $user_count = 0; // Default value
    $pending_post_count = 0;
    $post_count = 0; 
    $comment_count = 0; 

    if ($user_count_result->num_rows > 0) {
        $user_count = $user_count_result->fetch_assoc()['total_users'];
    }
    if ($pending_post_count_result->num_rows > 0) {
        $pending_post_count = $pending_post_count_result->fetch_assoc()['total_pending_posts'];
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
        <title>Recipebook</title>
        <link rel="stylesheet" href="/Recipebook/Recipe-Book/admin/dashboard.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo.png" type="image/png">
    </head>
    <body>
        <nav class="navbar">
            <div class="logo">
                <img src="/RecipeBook/Recipe-Book/logo/logo.png" onclick="window.location.href='/RecipeBook/Recipe-Book/admin/dashboard.php'" title="Dashboard" style="width: 120px; height: 120px;"/>&nbsp;
                <h1 onclick="about()" class="recipebook" title="About Recipebook">Recipebook</h1>
            </div>
            <div>
                <h1 onclick="window.location.href='/RecipeBook/Recipe-Book/admin/dashboard.php'" class="dashboard" title="Dashboard">Dashboard</h1>
            </div>
            <div class="logout-bar">
                <img class="logout-btn" src="/RecipeBook/Recipe-Book/buttons/logout_button_black.png" onclick="confirmit()" title="Logout" onmouseover="onHoverLogout()" onmouseout="noHoverLogout()" height="45px" width="45px"/>
            </div>
        </nav>

        <h1 style="text-align:center; color:#ffbf17; margin-top:70px;">Overview</h1>

        <div class="dashboard-container">
            <div class="dashboard-buttons">
                <div class="btn1" onclick="window.location.href='/Recipebook/Recipe-Book/admin/users_page.php'" onmouseover="onHoverbtn1()" onmouseout="noHoverbtn1()" title="Users">
                    <img class="img-1" src="/RecipeBook/Recipe-Book/buttons/users_button.png" height="40px" width="40px"/><br/>
                    <button class="btn-1">users</button><br/><br/>
                    <span class="count-1" style="font-size:30px; padding-left:80px;"><?php echo $user_count; ?></span>
                </div>
                <div class="btn4" onclick="window.location.href='/Recipebook/Recipe-Book/admin/all_pending_posts.php'" onmouseover="onHoverbtn4()" onmouseout="noHoverbtn4()" title="Pending Posts">
                    <img class="img-4" src="/RecipeBook/Recipe-Book/buttons/pending_button_black.png" height="35px" width="35px"/><br/>
                    <button class="btn-4">pending posts</button><br/><br/>
                    <span class="count-4" style="font-size:30px; padding-left:80px;"><?php echo $pending_post_count; ?></span>
                </div>
                <div class="btn2" onclick="window.location.href='/Recipebook/Recipe-Book/admin/all_posts.php'" onmouseover="onHoverbtn2()" onmouseout="noHoverbtn2()" title="Posts">
                    <img class="img-2" src="/RecipeBook/Recipe-Book/buttons/posts_button.png" height="40px" width="40px"/><br/>
                    <button class="btn-2">posts</button><br/><br/>
                    <span class="count-2" style="font-size:30px; padding-left:80px;"><?php echo $post_count; ?></span>
                </div>
                <div class="btn3" onclick="window.location.href='/Recipebook/Recipe-Book/admin/all_comments.php'" onmouseover="onHoverbtn3()" onmouseout="noHoverbtn3()" title="Comments">
                    <img class="img-3" src="/RecipeBook/Recipe-Book/buttons/comment_button_black_outlined.png" height="40px" width="40px"/><br/>
                    <button class="btn-3">comments</button><br/><br/>
                    <span class="count-3" style="font-size:30px; padding-left:80px;"><?php echo $comment_count; ?></span>
                </div>
            </div>
        </div>

        <!-- pop up box for about -->
        <div id="about" class="about">
            <div class="about_content">
                <div style="text-align:right;">
                    <span class="close1" onclick="closePopup1()" style="font-size:35px; color:black; cursor:pointer;">&times;</span>
                </div>
                <img src="/RecipeBook/Recipe-Book/logo/logo.png" title="Recipebook" style="width: 300px; height: 300px;"/>
                <h1 style="color: #333;">About <span style="color:#ffbf17;">Recipebook</span></h1>
                <p style="font-size: 20px; text-align:left;">RecipeBook is a social media platform designed specifically for food enthusiasts. It allows users to share their recipes, discover creations by others, and actively connect and engage with a community of like-minded food lovers.</p>
            </div>
        </div>
    </body>
    <script>
        function onHoverLogout(){
            document.querySelector('.logout-btn').src = '/RecipeBook/Recipe-Book/buttons/logout_button_yellow.png';
        }

        function noHoverLogout(){
            document.querySelector('.logout-btn').src = '/RecipeBook/Recipe-Book/buttons/logout_button_black.png';
        }

        function confirmit() {
            var ans = confirm("Are you sure you want to log out?");
            if (ans == true) {
                window.location.href = "/RecipeBook/Recipe-Book/admin/logout_admin.php";
            }
        }

        function onHoverbtn1() {
            document.querySelector('.btn1').style.backgroundColor = "#333"; 
            document.querySelector('.img-1').src = '/RecipeBook/Recipe-Book/buttons/users_button2.png';
            document.querySelector('.btn-1').style.color = "white";
            document.querySelector('.count-1').style.color = "white";
        }

        function noHoverbtn1(){
            document.querySelector('.btn1').style.backgroundColor = "white"; 
            document.querySelector('.img-1').src = '/RecipeBook/Recipe-Book/buttons/users_button.png';
            document.querySelector('.btn-1').style.color = "black";
            document.querySelector('.count-1').style.color = "black";
        }

        function onHoverbtn4() {
            document.querySelector('.btn4').style.backgroundColor = "#333"; 
            document.querySelector('.img-4').src = '/RecipeBook/Recipe-Book/buttons/pending_button_white.png';
            document.querySelector('.btn-4').style.color = "white";
            document.querySelector('.count-4').style.color = "white";
        }

        function noHoverbtn4(){
            document.querySelector('.btn4').style.backgroundColor = "white"; 
            document.querySelector('.img-4').src = '/RecipeBook/Recipe-Book/buttons/pending_button_black.png';
            document.querySelector('.btn-4').style.color = "black";
            document.querySelector('.count-4').style.color = "black";
        }

        function onHoverbtn2() {
            document.querySelector('.btn2').style.backgroundColor = "#333"; 
            document.querySelector('.img-2').src = '/RecipeBook/Recipe-Book/buttons/posts_button2.png';
            document.querySelector('.btn-2').style.color = "white";
            document.querySelector('.count-2').style.color = "white";
        }

        function noHoverbtn2(){
            document.querySelector('.btn2').style.backgroundColor = "white"; 
            document.querySelector('.img-2').src = '/RecipeBook/Recipe-Book/buttons/posts_button.png';
            document.querySelector('.btn-2').style.color = "black";
            document.querySelector('.count-2').style.color = "black";
        }

        function onHoverbtn3() {
            document.querySelector('.btn3').style.backgroundColor = "#333"; 
            document.querySelector('.img-3').src = '/RecipeBook/Recipe-Book/buttons/comment_button_white_outlined.png';
            document.querySelector('.btn-3').style.color = "white";
            document.querySelector('.count-3').style.color = "white";
        }

        function noHoverbtn3(){
            document.querySelector('.btn3').style.backgroundColor = "white"; 
            document.querySelector('.img-3').src = '/RecipeBook/Recipe-Book/buttons/comment_button_black_outlined.png';
            document.querySelector('.btn-3').style.color = "black";
            document.querySelector('.count-3').style.color = "black";
        }

        function about() {
            document.getElementById('about').style.display = 'block';
        }
        function closePopup1() {
            document.getElementById('about').style.display = 'none';
        }
        document.querySelector('.close1').addEventListener('click', closePopup1);

        window.onclick = function(event) {
            const popup1 = document.getElementById('about');

            // Close About pop-up when clicking outside
            if (event.target == popup1) {
                closePopup1();
            }
        };
    </script>
</html>

