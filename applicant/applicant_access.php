<?php
session_start();
if(empty($_SESSION['logged_in']) || $_SESSION['logged_in'] != 'applicant') {
    header('Location: ../layouts/login-access.php');
}
?>