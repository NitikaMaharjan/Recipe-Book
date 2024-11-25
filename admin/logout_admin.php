<?php
    session_start();

    if ((isset($_SESSION['adminname']) && isset($_SESSION['admin_loggedin']) && isset($_SESSION['admin_id']))) {

        unset($_SESSION['admin_loggedin']);
        unset($_SESSION['adminname']);
        unset($_SESSION['admin_id']);

        if (empty($_SESSION)) {
            session_destroy();
        }
        header("Location: /Recipebook/Recipe-Book/admin/login_admin.html");
        exit();
    } else {
        header("Location: /Recipebook/Recipe-Book/admin/login_admin.html");
        exit();
    }
?>
