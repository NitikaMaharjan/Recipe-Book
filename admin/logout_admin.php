<?php
    session_start();

    if ((isset($_SESSION['adminname']) && isset($_SESSION['admin_loggedin']) && isset($_SESSION['admin_id']))) {
        session_unset();
        session_destroy();
        header("Location: /Recipebook/Recipe-Book/admin/login_admin.html");
        exit();
    } else {
        header("Location: /Recipebook/Recipe-Book/admin/login_admin.html");
        exit();
    }
?>
