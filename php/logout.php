<html>
    <head>
        <title>Recipebook</title>
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo4.png" type="image/png">
    </head>
</html>
<?php
    session_start();

    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
        unset($_SESSION['loggedin']);
        unset($_SESSION['username']);
        unset($_SESSION['user_id']);

        if (empty($_SESSION)) {
            session_destroy();
        }

        echo"<script>
                alert('You have been logged out!!');
                window.location.href = '/RecipeBook/Recipe-Book/php/home_for_all.php';
            </script>";
        exit();
    }  
?>
