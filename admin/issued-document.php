<?php
include "admin_access.php";
include "../layouts/database_access.php";
try {
    if (!$connection) {
        $message = "Connection Failed.";
    } else {
        $orderId = $_GET['id'];
        $currentDate = date('Y-m-d');
        $query = "UPDATE client_order SET order_status = 'issued', issued_date = '$currentDate' WHERE id = $orderId";
        $result = $connection->exec($query);
        if(!empty($result)) {
            $message = "Successfully Issued Order.";
            $_SESSION['alert_type'] = "success";
        } else {
            $message = "Sorry! Oder not Issued";
        }
    }
} catch (Exception $ex) {
    $message = "Error : ".$ex->getMessage();
}
$_SESSION['alert_message'] = $message;
echo '<script>window.location = "welcome.php";</script>';
?>
