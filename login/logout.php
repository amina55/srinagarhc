<?php
session_start();
if(!empty($_SESSION['logged_in'])) {
    $_SESSION['logged_in'] = 0;
}
session_unset();
header('Location: login-get.php');
?>