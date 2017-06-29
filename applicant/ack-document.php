<?php
$order_id = $_GET['order_id'];
$query = "update client_order set applicant_doc_status = 'ack' where order_id = '$order_id'";
include "../layouts/database_access.php";
if($connection) {
    $result = $connection->exec($query);
}
header('Location: view-order.php?order_id='.$order_id);
?>