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
        <style>
            .post {
                cursor: pointer;
                padding: 10px;
                border: 1px solid #ccc;
                margin-bottom: 10px;
                transition: background-color 0.3s ease;
            }
            .popup {
                display: none; /* Hidden by default */
                position: fixed; /* Stay in place */
                z-index: 1; /* Sit on top */
                left: 0;
                top: 0;
                width: 100%; /* Full width */
                height: 100%; /* Full height */
                overflow: auto; /* Enable scroll if needed */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            }
            .popup_content {
                background-color: #fefefe;
                margin: 15% auto; /* 15% from the top and centered */
                padding: 20px;
                border: 1px solid #888;
                width: 80%; /* Could be more or less, depending on screen size */
            }
        </style>
    </head>
    <body>
        <button><a href="/RecipeBook/Recipe-Book/html/signup.html">Sign up</a></button>
        <button><a href="/RecipeBook/Recipe-Book/html/login.html">Log in</a></button>
        
        <br/><br/>
        <input type="text" placeholder="Search Recipe"/>
        <button onclick="popup()">Search</button>
        <br />
        
        <h1>Welcome to RecipeBook!!</h1>
        <button onclick="popup()">Add recipe</button>
        <h2>All posts</h2>

        
        <label>Sort by:</label>
        <select onchange="popup()">
            <option value="date">Date</option>
            <option value="likes">Likes</option>
        </select>
        
        <br/><br/>

        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
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
                    echo "<br/>";
                }
            } else {
                echo "<p>There are no recipes to show you, Sorry T_T </p>";
            }
            $conn->close();
        ?>
        <!-- pop up box for signup and login -->
        <div id="signup_login_popup" class="popup">
            <div class="popup_content">
                <span class="close" onclick="closePopup()">&times;</span>
                <button><a href="/RecipeBook/Recipe-Book/html/signup.html">Sign up</a></button>
                <button><a href="/RecipeBook/Recipe-Book/html/login.html">Log in</a></button>
            </div>
        </div>
    </body>
    <script>
        function popup() {
            //display the pop-up box
            document.getElementById('signup_login_popup').style.display = 'block';
        }

        // Close pop-up when clicking outside
        window.onclick = function(event) {
            const popup = document.getElementById('signup_login_popup');
            if (event.target == popup) {
                closePopup();
            }
        };

        function closePopup() {
            document.getElementById('signup_login_popup').style.display = 'none';
            location.reload();
        }

        // Close pop-up on 'x' click
        document.querySelector('.close').addEventListener('click', closePopup);
    </script>
</html>