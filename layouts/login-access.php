<?php
session_start();
if(empty($_SESSION['logged_in'])) {
    echo '<script>window.location = "/login/login-get.php";</script>';
    exit();
}
?>