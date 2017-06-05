<?php
include "admin_access.php";
$_SESSION['alert_message'] = "Upload Date not changed";
$_SESSION['alert_type'] = "failed";
$id = trim($_POST['order_id']);
$uploadDate = trim($_POST['upload_date']);
$reason = trim($_POST['change_reason']);

$query = "update client_order set upload_date_change_reason = '$reason', upload_date = '$uploadDate' where id = $id";
include "../layouts/database_access.php";
if($connection) {
    $result = $connection->exec($query);
    if($result) {
        $_SESSION['alert_message'] = "Successfully Change upload Date.";
        $_SESSION['alert_type'] = "success";
    }
}
header('Location: welcome.php');
?>