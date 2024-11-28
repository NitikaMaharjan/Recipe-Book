<?php
    session_start();

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
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['tmp_name'] != '') {
            // Get the file extension
            $imageFileType = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        
            // Check if the uploaded file is a valid image
            $check = getimagesize($_FILES['profile_pic']['tmp_name']);
        
            if ($check !== false) { // File is an image
                // Validate file type
                if (in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
                    // Validate file size (5MB limit)
                    if ($_FILES['profile_pic']['size'] <= 5000000) {
                        // Image is valid, prepare it for storage
                        $imageData = file_get_contents($_FILES['profile_pic']['tmp_name']);
                        $imageData = mysqli_real_escape_string($conn, $imageData);
        
                        // Update query to save the image in the database
                        $sql = "UPDATE user SET user_profile_picture='$imageData' WHERE user_id='$user_id'";
        
                        if ($conn->query($sql) === TRUE) {
                            echo "<script>
                                    alert('Profile picture updated successfully!');
                                    window.location.href = '/RecipeBook/Recipe-Book/php/profile.php';
                                  </script>";
                            exit();
                        } else {
                            echo "<script>
                                    alert('Error updating profile picture. Please try again!');
                                    window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/add_profile_pic.html';
                                  </script>";
                            exit();
                        }
                    } else {
                        // File size exceeds limit
                        echo "<script>
                                alert('Sorry, your file size exceeds the 5MB limit. Please try again!!');
                                window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/add_profile_pic.html';
                              </script>";
                        exit();
                    }
                } else {
                    // Invalid file type
                    echo "<script>
                            alert('Invalid image format. Only JPG, JPEG, and PNG files are allowed. Please try again!!');
                            window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/add_profile_pic.html';
                          </script>";
                    exit();
                }
            } else {
                // Not an image
                echo "<script>
                        alert('File is not a valid image. Please try again!!');
                        window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/add_profile_pic.html';
                      </script>";
                exit();
            }
        } else {
            // No file uploaded
            echo "<script>
                    alert('Profile picture cannot be empty. Please upload an image!!');
                    window.location.href = '/RecipeBook/Recipe-Book/html/manage_profile/add_profile_pic.html';
                  </script>";
            exit();
        }
    }

    $conn->close();
?>