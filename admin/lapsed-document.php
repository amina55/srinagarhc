<?php
include "admin_access.php";
$_SESSION['alert_message'] = "Lapsed Date not changed";
$_SESSION['alert_type'] = "failed";
$id = trim($_POST['order_id']);
$uploadDate = trim($_POST['lapsed_new_date']);
$reason = trim($_POST['lapsed_reason']);

if(empty($id) || empty($uploadDate) || empty($reason)) {
    $_SESSION['alert_message'] = 'Required Parameter is missing for Lapsed.';
} else {
    $query = "update client_order set lapsed_reason = '$reason', upload_date = '$uploadDate', order_status = 'lapsed' where id = $id";
    include "../layouts/database_access.php";
    if($connection) {
        $result = $connection->exec($query);
        if($result) {
            $_SESSION['alert_message'] = "Successfully Update Lapsed Date.";
            $_SESSION['alert_type'] = "success";
        }
    }
}
header('Location: welcome.php');
?>