<?php
    session_start();

    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
        session_unset();
        session_destroy();
        echo"<script>
                alert('You have been logged out!!');
                window.location.href = '/RecipeBook/Recipe-Book/php/home_for_all.php';
            </script>";
        exit();
    }  
?>
