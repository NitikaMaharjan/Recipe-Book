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
        <link class="logo" rel="icon" href="/RecipeBook/Recipe-Book/logo/logo.jpg" type="image/x-icon">
    </head>
    <body>
        <nav class="navbar">
            <img class="logo" src="/RecipeBook/Recipe-Book/logo/logo.JPG" onclick="about()"/>
            &nbsp;&nbsp;
            <img class="logo2" src="/RecipeBook/Recipe-Book/logo/logo2.jpg" onclick="about()"/>
            &nbsp;&nbsp;
            <div class="search-bar">
                <img src="/RecipeBook/Recipe-Book/buttons/search_icon.png" onclick="popup()" height="30px"/>
                <input type="text" placeholder="Search" onclick="popup()" style="margin-left:20px">
            </div>
            <button><a href="/RecipeBook/Recipe-Book/html/signup.html">Sign up</a></button>
            <button><a href="/RecipeBook/Recipe-Book/html/login.html">Log in</a></button>
            <br/><br/><br/>
        </nav>

        <h1 style="margin-top:80px">Welcome to RecipeBook!!</h1>
        <h2>All posts</h2>
        
        <img class="add_recipe" src="/RecipeBook/Recipe-Book/buttons/add_button.png" onclick="popup()"/>

        <label>Sort by:</label>
        <select onchange="popup()">
            <option value="date">Date</option>
            <option value="likes">Likes</option>
        </select>
        
        <br/><br/>

        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='container'>";
                    echo "<div class='post' onclick='popup()'>";
                    echo "<h3>" . htmlspecialchars($row['post_title']) . "</h3>";

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

                    echo "<p>Category : " . htmlspecialchars($row['post_category']) . "</p>";

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
                    echo "<br/>";
                }
            } else {
                echo "<p>There are no recipes to show you, Sorry T_T </p>";
            }
            $conn->close();
        ?>
        <!-- pop up box for about -->
        <div id="about" class="popup">
            <div class="popup_content">
                <span class="close1" onclick="closePopup1()">&times;</span>
                <h1>About Recipebook</h1>
            </div>
        </div>
        <!-- pop up box for signup and login -->
        <div id="signup_login_popup" class="popup">
            <div class="popup_content">
                <span class="close2" onclick="closePopup2()">&times;</span>
                <button><a href="/RecipeBook/Recipe-Book/html/signup.html">Sign up</a></button>
                <button><a href="/RecipeBook/Recipe-Book/html/login.html">Log in</a></button>
            </div>
        </div>
    </body>
    <script>
        function about() {
            //display the pop-up box
            document.getElementById('about').style.display = 'block';
        }

        function closePopup1() {
            document.getElementById('about').style.display = 'none';
        }

        // Close pop-up on 'x' click
        document.querySelector('.close1').addEventListener('click', closePopup1);


        function popup() {
            //display the pop-up box
            document.getElementById('signup_login_popup').style.display = 'block';
        }

        function closePopup2() {
            document.getElementById('signup_login_popup').style.display = 'none';
            location.reload();
        }

        // Close pop-up on 'x' click
        document.querySelector('.close2').addEventListener('click', closePopup2);

        
        window.onclick = function(event) {
            const popup1 = document.getElementById('about');
            const popup2 = document.getElementById('signup_login_popup');

            // Close "About" pop-up when clicking outside
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