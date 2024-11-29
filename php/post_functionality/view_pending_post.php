<?php
    session_start();

    if (!isset($_SESSION['username']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false){
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    } 

    if (!isset($_GET['post_id'])) {
        echo "<script>
                alert ('No post ID provided for viewing!!');
              </script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $post_id = $_GET['post_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT pending_post.*, user.user_name, user.user_profile_picture FROM pending_post JOIN user ON pending_post.user_id = user.user_id WHERE pending_post.post_id =$post_id";
    
    $result = $conn->query($sql);

    if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
        $postId = $row['post_id'];
    } else {
        echo "<script>
                alert ('No post found!!');
              </script>";
        exit();
    }
?>

<html>
    <head>
        <title>Recipebook</title>
        <link rel="stylesheet" href="/RecipeBook/Recipe-Book/css/view_pending_post.css" type="text/css">
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>
    <body>
        <img onclick="go_back()" class="back-button" src="/RecipeBook/Recipe-Book/buttons/back_button.png" title="Go back" onmouseover="onHoverBack()" onmouseout="noHoverBack()">
        
        <div class="view-post-container">
            <?php 
                echo "<div class='heading'>";
                    echo "<div>";
                        echo "<h1 title='Recipe Title'>".htmlspecialchars($row['post_title'])."</h1>";
                    echo "</div>";
                echo "</div>";
                echo "<br/>";

                echo "<div class='post-image'>";
                    if (!empty($row['post_image'])) {
                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['post_image']) . "' 
                        title='Recipe Image' style='max-width: 350px; max-height: 350px; border-radius:8px; cursor: pointer;' 
                        onclick='inlarge_image(this)'/>";
                    } else {
                        echo "No image available";
                    }
                echo "</div>";

                echo "<h2>Ingredients:</h2><p>";
                $ingredients = explode(', ', $row['post_ingredients']);
                echo "<ul>";
                foreach ($ingredients as $ingredient) {
                    echo "<li>" . htmlspecialchars($ingredient) . "</li>";
                }
                echo "</ul>";
                echo "</p>";

                echo "<h2>Preparation Steps:</h2><p>";
                $steps = explode(', ', $row['post_instructions']);
                echo "<ol>";
                foreach ($steps as $step) {
                    echo "<li>" . htmlspecialchars($step) . "</li>";
                }
                echo "</ol>";
                echo "</p>";

                echo "<h2>Hashtags:</h2><p>" . htmlspecialchars($row['post_keywords']) . "</p>";

                echo "<h2>Category:</h2><p>" . htmlspecialchars($row['post_category']) . "</p>";

                echo "<h2>Note:</h2><p>".htmlspecialchars($row['post_text'])."</p>";

                echo "<br/>";
                echo "<div class='post-actions'>";
                    echo "<div style='display: flex; align-items: center;'>";
                        if ($row['user_profile_picture']) {
                            echo "<img src='data:image/jpeg;base64," . base64_encode($row['user_profile_picture']) . "' alt='Profile picture' style='width: 50px; height: 50px; border-radius: 50%;' />";
                        } else {
                            echo "<img src='/RecipeBook/Recipe-Book/default_profile_picture.jpg' style='width: 50px; height: 50px; border-radius: 50%;' />";
                        }
                        if ($row['post_edited_date'] != $row['post_posted_date']) {
                            // Post has been edited
                            echo "<p><b>" . htmlspecialchars($row['user_name']) . "</b> edited on <b>" . htmlspecialchars($row['post_edited_date']) . "</b></p>";
                        } else {
                            // Post has not been edited
                            echo "<p><b>" . htmlspecialchars($row['user_name']) . "</b> posted on <b>" . htmlspecialchars($row['post_posted_date']) . "</b></p>";
                        }
                    echo "</div>";     
                echo "</div>";   
            ?>
        </div>
        <br/>
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

        function inlarge_image(image){
            // Create the modal container
            const modal = document.createElement('div');
            modal.classList.add('image-modal');

            // Add the image to the modal
            const modalImage = document.createElement('img');
            modalImage.src = image.src;
            modalImage.classList.add('modal-image');
            modal.appendChild(modalImage);

            // Add the modal to the body
            document.body.appendChild(modal);
        }

        window.onclick = function (event) {
            const imageModal = document.querySelector('.image-modal');
            
            // Prevent interfering with the image modal
            if (imageModal && event.target == imageModal) {
                imageModal.remove();
            }
        };
    </script>
</html>

<?php 
    $conn->close();
?>