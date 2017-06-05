<?php
include "admin_access.php";
include "../layouts/database_access.php";
try {
    if (!$connection) {
        $message = "Connection Failed.";
    } else {
        $orderId = $_GET['id'];

        $query = "select * from client_order where id = " . $orderId;
        $orders = $connection->query($query);
        $caseNo = $caseType = $caseYear = $paymentType = $licenceNo = '';
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $caseNo = $order['case_no'];
                $caseType = $order['case_type'];
                $caseYear = $order['case_year'];
                $paymentType = $order['payment_type'];
                $licenceNo = $order['licence_no'];
                $orderId = $order['id'];

                $query = "select pet_name, res_name, pet_adv, res_adv from civil_t where fil_no = $caseNo and " .
                    " filcase_type = $caseType  and fil_year = $caseYear";

                $petName = $resAdv = $resName = $petAdv = '';
                $details = $connection->query($query);
                if($details) {
                    foreach ($details as $detail) {
                        $petName = $detail['pet_name'];
                        $resName = $detail['res_name'];
                        $petAdv = $detail['pet_adv'];
                        $resAdv = $detail['res_adv'];
                    }
                } else {
                    $message = "There is no record for this order.";
                }
            }
        }
    }
} catch (Exception $ex) {
    $message = "Error : ".$ex->getMessage();
}

?>

    <?php if (!empty($message)) { ?>
        <div class="alert alert-danger">
            <?php echo $message?>
        </div>
    <?php } ?>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Petitioner name
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo $petName ?>
        </label>
    </div>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Petitioner Advocate
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo $petAdv ?>
        </label>
    </div>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Respondent name
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo $resName ?>
        </label>
    </div>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Respondent Advocate
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo $resAdv ?>
        </label>
    </div>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Payment Type
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo $paymentType ?>
        </label>
    </div>

    <?php if($paymentType == 'free') { ?>
        <div class="form-group col-sm-12">
            <label class="col-sm-4 control-label text-right">
                Licence Number
            </label>
            <label class="col-sm-8 text-left bold">
                <?php echo $licenceNo ?>
            </label>
        </div>
    <?php } ?>


