<?php
session_start();
if(empty($_SESSION['logged_in']) || $_SESSION['logged_in'] != 'super-admin') {
    header('Location: ../layouts/login-access.php');
}
?>