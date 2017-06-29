<?php
session_start();
if(!empty($_SESSION['logged_in'])) {
    if($_SESSION['logged_in'] == 'applicant') {
        header('Location: ../applicant/welcome.php');
    } else if($_SESSION['logged_in'] == 'admin') {
        header('Location: ../admin/welcome.php');
    } else if($_SESSION['logged_in'] == 'super-admin') {
        header('Location: ../super-admin/welcome.php');
    }
} else {
    header('Location: ../login/login-get.php');
}
?>