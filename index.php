<?php
session_start();
if(!empty($_SESSION['logged_in'])) {
    header('Location: admin/welcome.php');
} else {
    header('Location: login/login-get.php');
}
?>