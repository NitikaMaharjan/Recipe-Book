<?php
    session_start();
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']==true){
        echo "Welcome ".$_SESSION['username']."<br/>";
        echo "Your session ID is: " . session_id();
    }else{
        echo "You are not logged in";
    }
?>