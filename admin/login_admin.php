<?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipebook";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $admin_name = $_POST['username'];
        $admin_password = $_POST['password'];

        if (empty($admin_name) || empty($admin_password)) {
            echo "<script>
                    alert ('All fields are required!!');
                    window.location.href = '/Recipebook/Recipe-Book/admin/login_admin.html';
                  </script>";
            exit();
        } else {
            $sql = "SELECT admin_name, admin_password, admin_id FROM admin WHERE admin_name='$admin_name' AND admin_password='$admin_password'";
            $result = $conn->query($sql);

            if ($result->num_rows==1) {
                $row = $result->fetch_assoc();

                $_SESSION['admin_loggedin'] = true;
                $_SESSION['adminname'] = $row['admin_name'];
                $_SESSION['admin_id'] = $row['admin_id'];
                header("Location: /Recipebook/Recipe-Book/admin/dashboard.php");
                exit();
            }else{
                echo "<script>
                        alert ('Incorrect username and password, Please try again!!');
                        window.location.href = '/Recipebook/Recipe-Book/admin/login_admin.html';
                      </script>";
                exit();
            }
        }
    }

    $conn->close();
?>