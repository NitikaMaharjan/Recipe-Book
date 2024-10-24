<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipebook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sort_by = 'post.post_id DESC'; // Default sort by date
    if (isset($_GET['sort']) && $_GET['sort'] == 'likes') {
        $sort_by = 'post_like_count DESC'; // Sort by likes
    }

    $sql = "
        SELECT post.*, user.user_name 
        FROM post 
        JOIN user ON post.user_id = user.user_id 
        ORDER BY $sort_by
    ";
    $result = $conn->query($sql);
?>

<html>
    <head>
        <title>Home page</title>
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
        <header>
            <div>
                <button><a href="/RecipeBook/Recipe-Book/html/signup.html">Sign up</a></button>
                <button><a href="/RecipeBook/Recipe-Book/html/login.html">Log in</a></button>
            </div>
        </header>
        <br/>
        <input type="text" placeholder="Search Recipe"/>
        <button onclick="popup()">Search</button>
        <br />
        
        <h1>Welcome to RecipeBook!!</h1>
        <button onclick="popup()">Add recipe</button>
        <h2>All posts</h2>

        <form id="sortForm" method="GET" action="">
            <label for="sort">Sort by:</label>
            <select id="sort" name="sort" onchange="document.getElementById('sortForm').submit();">
                <option value="date" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date') ? 'selected' : ''; ?>>Date</option>
                <option value="likes" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'likes') ? 'selected' : ''; ?>>Likes</option>
            </select>
        </form>
        
        <br/>

        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='post' onclick='popup()'>";
                    echo "<h3>" . htmlspecialchars($row['post_title']) . "</h3>";

                    if ($row['post_edited_date'] != $row['post_posted_date']) {
                        echo "<p>Posted by <b>" . htmlspecialchars($row['user_name']) . " </b>and edited on<b> " . htmlspecialchars($row['post_edited_date']) . "</b></p>";
                    } else {
                        echo "<p>Posted by <b>" . htmlspecialchars($row['user_name']) . " </b>on<b> " . htmlspecialchars($row['post_edited_date']) . "</b></p>";
                    }

                    echo "<p>Category : " . htmlspecialchars($row['post_category']) . "</p>";

                    if (($row['post_image'])) {
                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' alt='Recipe Image' style='max-width: 200px; max-height: 200px;'/>";
                    } else {
                        echo "No image available";
                    }
                    echo "<p>" . htmlspecialchars($row['post_text']) . "</p>";
                    echo "<p>" . htmlspecialchars($row['post_keywords']) . "</p>";

                    echo "<button>Comment</button>";
                    echo "<button>Add to Favourites</button>";

                    $postId = $row['post_id'];
                    echo "<button>";
                    echo "Likes: <span id='like-count-" . $postId . "'>" . htmlspecialchars($row['post_like_count']) . "</span>";
                    echo "</button>";
                    
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
        }

        // Close pop-up on 'x' click
        document.querySelector('.close').addEventListener('click', closePopup);
    </script>
</html>