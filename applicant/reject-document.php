<?php
$orderId = trim($_POST['order_id']);
$rejectionReason = trim($_POST['rejection_reason']);
$query = "update client_order set applicant_doc_status = 'rejected', applicant_doc_rejection_reason = '$rejectionReason' where order_id = '$orderId'";
include "../layouts/database_access.php";
if($connection) {
    $result = $connection->exec($query);
}
header('Location: view-order.php?order_id='.$orderId);
?>