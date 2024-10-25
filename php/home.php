<?php
    session_start();

    if (!(isset($_SESSION['username']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'])) {
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    }

    $user_name = $_SESSION['username'];

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

    // Update post_like_count on post table
    $update_sql = "
        UPDATE post 
        SET post_like_count = (
            SELECT COUNT(*) 
            FROM Likes 
            WHERE Likes.post_id = post.post_id
        )
    ";
    $conn->query($update_sql);


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
            .modal {
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
            .modal-content {
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
                <button><a href="/RecipeBook/Recipe-Book/php/home.php">Home</a></button>
                <button><a href="/RecipeBook/Recipe-Book/php/profile.php">Profile</a></button>
                <button><a href="/RecipeBook/Recipe-Book/php/favourite_page.php">My Favourites</a></button>
            </div>
        </header>
        <form name="search" method="post" action="/RecipeBook/Recipe-Book/php/search_functionality/search_post.php">
            <br/>
            <input type="text" id="search" name="search" placeholder="Search Recipe"/>
            <input type="submit" value="Search"/>
            <br />
        </form>
        
        <h1>Hello <?php echo "$user_name" ?>, welcome to your home feed!!</h1>
        <button><a href="/RecipeBook/Recipe-Book/php/logout.php">Log out</a></button>
        <button><a href="/RecipeBook/Recipe-Book/html/add_post.html">Add recipe</a></button>
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
                    $postId = $row['post_id'];
                    echo "<div class='post' onclick='view_post($postId)'>";
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
                    echo "<button class='comment-btn' data-post-id='" . $postId . "'>Comment</button>";
                    echo "<button class='fav-btn' data-post-id='" . $row['post_id'] . "'>Add to Favourites</button>";

                    echo "<button class='like-btn' data-post-id='" . $postId . "'>";
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
        <!-- pop up box for comments -->
        <div id="commentModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Comments</h2>
                <div id="commentList"></div>
                <textarea id="commentText" placeholder="Add your comment..."></textarea><br>
                <button id="submitComment">Submit Comment</button>
            </div>
        </div>
    </body>
    <script>
        function view_post(post_id) {
            window.location.href = "/RecipeBook/Recipe-Book/php/view_post.php?post_id=" + post_id;
        }

        //ajax for like button
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/recipebook/Recipe-Book/php/like_post.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            const likeCountSpan = document.getElementById('like-count-' + postId);
                            likeCountSpan.innerText = response.newLikeCount;
                        } else {
                            alert(response.message);
                        }
                    }
                };
                xhr.send('post_id=' + postId);
            });
        });

        //ajax for favourite button
        document.querySelectorAll('.fav-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const postId = this.getAttribute('data-post-id');
                console.log(postId);
                
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/RecipeBook/Recipe-Book/php/add_favourite.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('Post added to your favourites!');
                    }
                };
                xhr.send('post_id=' + postId);
            });
        });
        // ajax and js for comments section
        let commentPollingInterval; // Variable to hold the interval ID

        function openModal(postId) {
            // Fetch existing comments
            fetchComments(postId);
            
            // Start polling for new comments
            commentPollingInterval = setInterval(() => {
                fetchComments(postId);
            }, 3000); // Fetch new comments every 3 seconds
            
            // Display the modal
            document.getElementById('commentModal').style.display = 'block';
            
            // Set the postId in the button
            document.getElementById('submitComment').setAttribute('data-post-id', postId);
        }

        function closeModal() {
            document.getElementById('commentModal').style.display = 'none';
            
            // Stop polling when the modal is closed
            clearInterval(commentPollingInterval);
        }

        function fetchComments(postId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '/recipebook/Recipe-Book/php/comment_functionality/fetch_comments.php?post_id=' + postId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('commentList').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Submit comment
        document.getElementById('submitComment').addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const commentText = document.getElementById('commentText').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/recipebook/Recipe-Book/php/comment_functionality/add_comment.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('commentText').value = ''; // Clear the text area
                    fetchComments(postId); // Refresh comments
                }
            };
            xhr.send('post_id=' + postId + '&comment_text=' + encodeURIComponent(commentText));
        });

        // Handle comment button clicks
        document.querySelectorAll('.comment-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent post click
                const postId = this.getAttribute('data-post-id');
                openModal(postId);
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('commentModal');
            if (event.target == modal) {
                closeModal();
            }
        };
        // Close modal on 'x' click
        document.querySelector('.close').addEventListener('click', closeModal);

    </script>
</html>