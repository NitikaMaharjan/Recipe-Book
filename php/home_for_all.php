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
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo.png" type="image/png">
    </head>

    <body>
        <nav class="navbar">
            <div class="logo">
                <img src="/RecipeBook/Recipe-Book/logo/logo.png" onclick="about()" style="width: 150px; height: 150px;"/>
                <h1 onclick="about()" class="recipebook">Recipebook</h1>
            </div>

            <div class="search-bar">
                <img src="/RecipeBook/Recipe-Book/buttons/search_icon.png" onclick="popup()" height="30px"/>
                <input type="text" placeholder="Search Recipe" onclick="popup()" style="margin-left:20px">
            </div>
            <div class="loginbar">
                <button class="login-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/html/login.html'">Log in</button>
                <button class="signup-btn" onclick="window.location.href='/RecipeBook/Recipe-Book/html/signup.html'">Sign up</button>
            </div>
        </nav>
        
        <div class="heading">
            <h1>Welcome to <span style="color:#ffbf17; cursor:pointer;" onclick="about()">Recipebook</span></h1>
            <h2>All posts</h2>

            <label>Sort by:</label>
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
                    echo "<div class='container' onclick='popup()'>";

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
                                echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
                            } else {
                                echo "No image available";
                            }
                            echo "<p>" . htmlspecialchars($row['post_text']) . "</p>";
                            echo "<p>" . htmlspecialchars($row['post_keywords']) . "</p>";

                            
                            echo "<button>Add to Favourites</button>";

                            $postId = $row['post_id'];
                            echo "<button>";
                            echo "Likes: <span id='like-count-" . $postId . "'>" . htmlspecialchars($row['post_like_count']) . "</span>";
                            echo "</button>";

                            echo "<button>Comment</button>";
                        echo "</div>";

                    echo "</div>";
                }
            } else {
                echo "<p>There are no recipes to show you, Sorry T_T </p>";
            }
            $conn->close();
        ?>

        <!-- pop up box for about -->
        <div id="about" class="popup">
            <div class="popup_content">
                <div style="text-align:right;">
                    <span class="close1" onclick="closePopup1()" style="font-size:35px; color:black;">&times;</span>
                </div><br/>
                <img src="/RecipeBook/Recipe-Book/logo/logo.png" style="width: 300px; height: 300px;"/>
                <h1 style="color:black;">About Recipebook</h1>
                <p style="font-size: 20px; text-align:left;">Recipebook is an online platform that serves as a social networking and content-sharing system which enables users to share their own recipes. It's an interactive and personalized place for food enthusiasts, with the ability to post recipes, like and comment on posts, and search for recipes by title, username, or ingredients they have on hand.</p>
            </div>
        </div>

        <!-- pop up box for signup and login -->
        <div id="signup_login_popup" class="popup">
            <div class="popup_content">
            <div style="text-align:right;">
                <span class="close2" onclick="closePopup2()" style="font-size:35px; color:black;">&times;</span><br/>
            </div><br/>
            <button><a href="/RecipeBook/Recipe-Book/html/signup.html">Sign up</a></button>
            <button><a href="/RecipeBook/Recipe-Book/html/login.html">Log in</a></button>
            </div>
        </div>

    </body>
    <script>
        //for color change of login and sign up button
        const loginBtn = document.querySelector('.login-btn');
        const signupBtn = document.querySelector('.signup-btn');

        function hoverEffect() {
            loginBtn.style.backgroundColor = '#cccc';
            loginBtn.style.color = 'black';
            signupBtn.style.backgroundColor = '#FFBF17';
            signupBtn.style.color = 'white';
        }

        function removeHoverEffect() {
            loginBtn.style.backgroundColor = '#FFBF17';
            loginBtn.style.color = 'white';
            signupBtn.style.backgroundColor = '#cccc';
            signupBtn.style.color = 'black';
        }

        loginBtn.addEventListener('mouseenter', hoverEffect);
        signupBtn.addEventListener('mouseenter', hoverEffect);

        loginBtn.addEventListener('mouseleave', removeHoverEffect);
        signupBtn.addEventListener('mouseleave', removeHoverEffect);

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