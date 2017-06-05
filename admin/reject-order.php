<?php
include "admin_access.php";
$message = '';
$_SESSION['alert_type'] = 'danger';
$orderId = trim($_POST['order_id']);
$rejectionReason = trim($_POST['rejection-reason']);
if (empty($rejectionReason)) {
    $message = "Please mention rejection reason.";
} elseif (empty($orderId)) {
    $message = "Please again select an order to reject.";
} else {
    include "../layouts/database_access.php";
    if (!$connection) {
        $message = "Connection Failed!";
    } else {
        $query = "UPDATE client_order SET order_status = 'rejected', rejection_reason = '$rejectionReason' WHERE id = $orderId";
        $result = $connection->exec($query);
        if(!empty($result)) {
             $message = "Successfully Reject Order.";
            $_SESSION['alert_type'] = "success";
        } else {
            $message = "Sorry! Oder not rejected";
        }
    }
}
$_SESSION['alert_message'] = $message;
echo '<script>window.location = "welcome.php";</script>';
?>
