<?php
include "admin_access.php";
include "../layouts/database_access.php";
try {
    $_SESSION['alert_type'] = "danger";
    if (!$connection) {
        $message = "Connection Failed.";
    } else {
        $amount = $_POST['paid_amount'];
        $orderId = $_POST['order_id'];
        $uploadDate = !empty($_POST['upload_date']) ? $_POST['upload_date'] : date('Y-m-d', strtotime('+1 day'));

        if ($_FILES['upload_document']['error'] !== UPLOAD_ERR_OK) {
            $message = "Upload failed with error " . $_FILES['upload_document']['error'];
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['upload_document']['tmp_name']);
            if ($mime == 'application/pdf') {
                $uploadDocument = $orderId.'-'.time().'.pdf';

                move_uploaded_file($_FILES["upload_document"]["tmp_name"], "../uploads/".$uploadDocument);
            } else {
                $message = "Only pdf file is allowed.";
            }
        }
        $query = "update client_order set order_status = 'approved', paid_amount = $amount, upload_date = '$uploadDate', ".
            "upload_document = '$uploadDocument' where id = $orderId";

        $result = $connection->exec($query);
        if($result) {
            $message = "Successfully Uploaded and Approved Order.";
            $_SESSION['alert_type'] = "success";
        } else {
            $message = "Applicant Order not accepted. Please Try agian!";
        }
    }
} catch (Exception $ex) {
    $message = "Error : ".$ex->getMessage();
}
$_SESSION['alert_message'] = $message;
echo '<script>window.location = "welcome.php";</script>';
?>
