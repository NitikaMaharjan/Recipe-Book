<html>
    <head>
        <title>Recipebook</title>
        <link rel="icon" href="/RecipeBook/Recipe-Book/logo/logo.png" type="image/png">
    </head>
</html>
<?php
    session_start();

    if ((isset($_SESSION['adminname']) && isset($_SESSION['admin_loggedin']) && isset($_SESSION['admin_id']))) {

        unset($_SESSION['admin_loggedin']);
        unset($_SESSION['adminname']);
        unset($_SESSION['admin_id']);

        if (empty($_SESSION)) {
            session_destroy();
        }
        echo"<script>
                alert('You have been logged out!!');
                window.location.href = '/Recipebook/Recipe-Book/admin/login_admin.html';
            </script>";
        exit();
    } else {
        header("Location: /Recipebook/Recipe-Book/admin/login_admin.html");
        exit();
    }
?>
