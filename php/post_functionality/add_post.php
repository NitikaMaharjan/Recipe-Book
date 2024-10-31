<?php
    session_start();
    
    if (!isset($_SESSION['username']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false){
        header("Location: /RecipeBook/Recipe-Book/html/login.html");
        exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "RecipeBook";
    
    $user_id = $_SESSION['user_id'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $post_title = $_POST['post_title'];
        $post_ingredients = implode(", ", $_POST['post_ingredients']); 
        $post_instructions = implode(", ", $_POST['post_instructions']);
        $post_keywords = $_POST['post_keywords'];
        $post_category = $_POST['post_category'];
        $post_text = $_POST['post_text'];

        if (isset($_FILES['post_image']) && $_FILES['post_image']['tmp_name'] != '') {
            $imageFileType = strtolower(pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION));
            $check = getimagesize($_FILES['post_image']['tmp_name']);
    
            if ($check !== false && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                if ($_FILES['post_image']['size'] > 5000000) { // 5MB limit
                    echo "<script>
                            alert ('Sorry, your file size is too large!!');
                            window.location.href = '/RecipeBook/Recipe-Book/html/post_functionality/add_post.html';
                          </script>";
                    exit();
                }
    
                $imageData = file_get_contents($_FILES['post_image']['tmp_name']);
                $imageData = mysqli_real_escape_string($conn, $imageData);
            } else {
                echo "<script>
                        alert ('Invalid image format. Only JPG, JPEG, PNG & GIF files are allowed!!');
                        window.location.href = '/RecipeBook/Recipe-Book/html/post_functionality/add_post.html';
                      </script>";
                exit();
            }
        }

        $sql = "INSERT INTO post (post_image, post_title, post_ingredients, post_instructions, post_keywords, post_category, user_id, post_text)
            VALUES ('$imageData', '$post_title', '$post_ingredients', '$post_instructions', '$post_keywords', '$post_category', '$user_id','$post_text')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert ('Post created successfully!!');
                    window.location.href = '/RecipeBook/Recipe-Book/php/profile.php';
                  </script>";
            exit();
        } else {
            echo "Error creating post: " . $conn->error;
        }
    }

    $conn->close();
?>