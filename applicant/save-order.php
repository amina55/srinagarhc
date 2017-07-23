<?php
session_start();
$message = '';
$alertType = 'alert-danger';
include '../layouts/database_access.php';
if (!$connection) {
    $message = "Connection Failed.";
} else {
    try {
        $name = trim($_POST['applicant_name']);
        $caseType = trim($_POST['case_type']);
        $caseNo = trim($_POST['case_no']);
        $caseYear = trim($_POST['case_year']);
        $documentType = $_POST['document_type'];
        $documentDate = trim($_POST['document_date']);
        $paymentType = trim($_POST['payment_type']);
        $orderId = trim($_POST['order_id']);
        $licenceNo = trim($_POST['licence_no']);
        $filNo = trim($_POST['fil_no']);
        $filYear = trim($_POST['fil_year']);
        $cino = trim($_POST['cino']);
        $currentDate = date('m/d/Y');
        $currentYear = date('Y');

         $insertQuery = "INSERT INTO client_order (applicant_name, case_type, case_no, case_year, payment_type, document_type, document_date, order_id, licence_no, apply_date, apply_year, fil_no, fil_year, cino) " .
             "VALUES  ('$name', $caseType, $caseNo, $caseYear, '$paymentType', '$documentType', '$documentDate', '$orderId', '$licenceNo', '$currentDate', $currentYear, $filNo, $filYear, '$cino')";

         $result = $connection->exec($insertQuery);
         if (empty($result)) {
             $message = "Error in Sending Order";
         } else {
             $message = "Your record is successfully entered. Please note this Order Id to view your order data. Order Id is : " . $orderId;
             $alertType = 'alert-success';
         }

    } catch (Exception $e) {
        $message = "Error : " . $e->getMessage();
    }
}
$_SESSION['message'] = $message;
$_SESSION['alert_type'] = $alertType;
header('Location: send-order.php');
?>