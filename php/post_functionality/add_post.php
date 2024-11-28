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
            // Get the file extension
            $imageFileType = strtolower(pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION));
        
            // Check if the uploaded file is an image
            $check = getimagesize($_FILES['post_image']['tmp_name']);
        
            if ($check !== false) { // File is an image
                // Validate file type
                if (in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
                    // Validate file size (5MB limit)
                    if ($_FILES['post_image']['size'] <= 5000000) {
                        // Image is valid, prepare it for storage
                        $imageData = file_get_contents($_FILES['post_image']['tmp_name']);
                        $imageData = mysqli_real_escape_string($conn, $imageData);
                    } else {
                        // File size exceeds limit
                        echo "<script>
                                alert('Sorry, your file size exceeds the 5MB limit. Please try again!!');
                                window.location.href = '/RecipeBook/Recipe-Book/html/post_functionality/add_post.html';
                              </script>";
                        exit();
                    }
                } else {
                    // Invalid file type
                    echo "<script>
                            alert('Invalid image format. Only JPG, JPEG, and PNG files are allowed. Please try again!!');
                            window.location.href = '/RecipeBook/Recipe-Book/html/post_functionality/add_post.html';
                          </script>";
                    exit();
                }
            } else {
                // Not an image
                echo "<script>
                        alert('File is not a valid image. Please try again!!');
                        window.location.href = '/RecipeBook/Recipe-Book/html/post_functionality/add_post.html';
                      </script>";
                exit();
            }
        } else {
            // No file uploaded
            echo "<script>
                    alert('Image cannot be empty. Please upload an image. Please try again!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/post_functionality/add_post.html';
                  </script>";
            exit();
        }
        
        $sql = "INSERT INTO approve_post (post_image, post_title, post_ingredients, post_instructions, post_keywords, post_category, user_id, post_text)
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