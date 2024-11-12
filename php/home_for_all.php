<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipebook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "
        SELECT post.*, user.user_name, user.user_profile_picture
        FROM post 
        JOIN user ON post.user_id = user.user_id 
        ORDER BY post.post_id DESC
    ";

    $result = $conn->query($sql);

?>

<html>

    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/RecipeBook/Recipe-Book/css/home_for_all.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>

    <body>
        <nav class="navbar">
            <div class="logo">
                <img src="/RecipeBook/Recipe-Book/logo/logo4.png" onclick="about()" title="About Recipebook" style="width: 120px; height: 120px;"/>&nbsp;
                <h1 onclick="about()" class="recipebook" title="About Recipebook">Recipebook</h1>
            </div>

            <div class="search-bar">
                <img src="/RecipeBook/Recipe-Book/buttons/search_icon.png" onclick="popup()" height="30px"/>
                <input type="text" placeholder="Search Recipe" onclick="popup()"/>
                <button class="search-btn" onclick="popup()">Search</button>
            </div>
            <div class="loginbar">
                <button class="login-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/html/login.html'" title="Log in">Log in</button>&nbsp;&nbsp;
                <button class="signup-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/html/signup.html'" title="Sign up">Sign up</button>
            </div>
        </nav>
        
        <div class="background">
            <div class="heading">
                <h1 style="text-align: center;">Welcome to <span style="color:#ffbf17; cursor:pointer;" onclick="about()" title="About Recipebook">Recipebook</span></h1><br/>
                <h2 style="text-align: center;">All posts</h2>

                <label>Sort by:</label>&nbsp;&nbsp;
                <select onchange="popup()">
                    <option value="date">Date</option>
                    <option value="likes">Likes</option>
                </select>
            </div>

            <img class="add_recipe" src="/RecipeBook/Recipe-Book/buttons/add_button.png" onclick="popup()" title="Add Recipe"/>

            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<br/>";
                        echo "<div class='container' onclick='popup()' onmouseover='onHover(this)' onmouseout='noHover(this)'>";

                            echo "<div class='post-title'>";
                                echo "<h3 style='font-size:25px;'>" . htmlspecialchars($row['post_title']) . "</h3>";
                            echo "</div>";
                            
                            echo "<div class='post'>";
                                if ($row['post_edited_date'] != $row['post_posted_date']) {
                                    // Post has been edited
                                    echo "<div style='display: flex; align-items: center;'>"; 
                                    if ($row['user_profile_picture']) {
                                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' alt='Profile picture' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                                    } else {
                                        echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                                    }
                                    echo "<p><b>" . htmlspecialchars($row['user_name']) . "</b> edited on <b>" . htmlspecialchars($row['post_edited_date']) . "</b></p>";
                                    echo "</div>"; 
                                } else {
                                    // Post has not been edited
                                    echo "<div style='display: flex; align-items: center;'>"; 
                                    if ($row['user_profile_picture']) {
                                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' alt='Profile picture' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                                    } else {
                                        echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;' />";
                                    }
                                    echo "<p><b>" . htmlspecialchars($row['user_name']) . "</b> posted on <b>" . htmlspecialchars($row['post_posted_date']) . "</b></p>";
                                    echo "</div>"; 
                                }

                                echo "<p><b>Category : </b>" . htmlspecialchars($row['post_category']) . "</p>";

                                if (($row['post_image'])) {
                                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px; border-radius:30px;'/>";
                                } else {
                                    echo "No image available";
                                }
                                echo "<p>" . htmlspecialchars($row['post_text']) . "</p>";
                                echo "<p>" . htmlspecialchars($row['post_keywords']) . "</p>";

                                
                                echo "<img class='fav' src='/RecipeBook/Recipe-Book/buttons/fav_button_black_outlined.png' onclick='popup()' height='30px' width='30px'/>&nbsp;&nbsp;&nbsp;";

                                $postId = $row['post_id'];
                                echo "<img class='like' src='/RecipeBook/Recipe-Book/buttons/like_button_black_outlined.png' onclick='popup()' height='30px' width='30px'/><span id='like-count-" . $postId . "'>" . htmlspecialchars($row['post_like_count']) . "</span>&nbsp;&nbsp;&nbsp;";

                                echo "<img class='comment' src='/RecipeBook/Recipe-Book/buttons/comment_button_black_outlined.png' onclick='popup()' height='30px' width='30px'/>";
                            echo "</div>";

                        echo "</div>";
                    }
                } else {
                    echo "<p>There are no recipes to show you, Sorry T_T </p>";
                }
                $conn->close();
            ?>
        </div>
        <!-- pop up box for about -->
        <div id="about" class="about">
            <div class="about_content">
                <div style="text-align:right;">
                    <span class="close1" onclick="closePopup1()" style="font-size:35px; color:black; cursor:pointer;">&times;</span>
                </div>
                <img src="/RecipeBook/Recipe-Book/logo/logo.png" style="width: 300px; height: 300px;"/>
                <h1 style="color:black;">About Recipebook</h1>
                <p style="font-size: 20px; text-align:left;">Recipebook is an online platform that serves as a social networking and content-sharing system which enables users to share their own recipes. It's an interactive and personalized place for food enthusiasts, which enables users to post recipes, like and comment on posts, and search for recipes by recipe title, username of users, categories and hashtags.</p>
            </div>
        </div>

        <!-- pop up box for signup and login -->
        <div id="signup_login_popup" class="popup">
            <div class="popup_content">
                <div style="text-align:right;">
                    <span class="close2" onclick="closePopup2()" style="font-size:35px; color:black; cursor:pointer;">&times;</span>
                </div>
                <div>
                <h1>Sign up to get started!!</h1>
                <form name="signup" method="post" action="/RecipeBook/Recipe-Book/php/signup.php">
                    <input type="text" name="username" placeholder="Username" required/><br/><br/>
                    <input type="email" name="email" placeholder="Email" required/><br/><br/>
                    <input type="password" name="password" placeholder="Password" required/><br/><br/>
                    <input type="password" name="password2" placeholder="Confirm password" required/><br/><br/>
                    <input type="submit" value="Sign up"/>
                </form>
                <h1>Already have an account??</h1><button><a href="/RecipeBook/Recipe-Book/html/login.html">Log in</a></button>
                </div>
            </div>
        </div>
    </body>
    <script>
        function onHover(container) {
            const favIcon = container.querySelector('.fav');
            const likeIcon = container.querySelector('.like');
            const commentIcon = container.querySelector('.comment');

            favIcon.src = "/RecipeBook/Recipe-Book/buttons/fav_button_yellow_outlined.png";
            favIcon.style.height="35px";
            favIcon.style.width="35px";
            likeIcon.src = "/RecipeBook/Recipe-Book/buttons/like_button_yellow_outlined.png";
            likeIcon.style.height="35px";
            likeIcon.style.width="35px";
            commentIcon.src = "/RecipeBook/Recipe-Book/buttons/comment_button_yellow_outlined.png";
            commentIcon.style.height="35px";
            commentIcon.style.width="35px";
        }

        function noHover(container) {
            const favIcon = container.querySelector('.fav');
            const likeIcon = container.querySelector('.like');
            const commentIcon = container.querySelector('.comment');

            favIcon.src = "/RecipeBook/Recipe-Book/buttons/fav_button_black_outlined.png";
            favIcon.style.height="30px";
            favIcon.style.width="30px";
            likeIcon.src = "/RecipeBook/Recipe-Book/buttons/like_button_black_outlined.png";
            likeIcon.style.height="30px";
            likeIcon.style.width="30px";
            commentIcon.src = "/RecipeBook/Recipe-Book/buttons/comment_button_black_outlined.png";
            commentIcon.style.height="30px";
            commentIcon.style.width="30px";
        }

        //about popup box
        function about() {
            //display the pop-up box
            document.getElementById('about').style.display = 'block';
        }
        function closePopup1() {
            document.getElementById('about').style.display = 'none';
        }
        document.querySelector('.close1').addEventListener('click', closePopup1);

        //signup login popup box
        function popup() {
            document.getElementById('signup_login_popup').style.display = 'block';
        }
        function closePopup2() {
            document.getElementById('signup_login_popup').style.display = 'none';
            location.reload();
        }
        document.querySelector('.close2').addEventListener('click', closePopup2);

        window.onclick = function(event) {
            const popup1 = document.getElementById('about');
            const popup2 = document.getElementById('signup_login_popup');

            // Close About pop-up when clicking outside
            if (event.target == popup1) {
                closePopup1();
            }
            // Close signuplogin pop-up when clicking outside
            if (event.target == popup2) {
                closePopup2();
            }
        };
    </script>
</html>