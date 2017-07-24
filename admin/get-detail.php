<?php
include "../layouts/database_access.php";
try {
    if (!$connection) {
        $message = "Connection Failed.";
    } else {
        $orderId = $_GET['id'];

        $query = "select * from client_order where id = " . $orderId;
        $statement = $connection->prepare($query);
        $statement->execute();
        $order = $statement->fetch(PDO::FETCH_ASSOC);

        $caseNo = $caseType = $caseYear = $paymentType = $licenceNo = '';
        if (!empty($order)) {
            $caseNo = $order['case_no'];
            $caseType = $order['case_type'];
            $caseYear = $order['case_year'];
            $paymentType = $order['payment_type'];
            $licenceNo = $order['licence_no'];
            $orderId = $order['id'];

            $query = "select fil_no, pet_name, res_name, pet_adv, res_adv from civil_t where reg_no = $caseNo and regcase_type = $caseType  and reg_year = $caseYear";
            $statement = $connection->prepare($query);
            $statement->execute();
            $detail = $statement->fetch(PDO::FETCH_ASSOC);

            if(empty($detail)) {
                $query = "select fil_no, pet_name, res_name, pet_adv, res_adv from civil_t_a where reg_no = $caseNo and regcase_type = $caseType  and reg_year = $caseYear";
                $statement = $connection->prepare($query);
                $statement->execute();
                $detail = $statement->fetch(PDO::FETCH_ASSOC);
            }

            if(empty($detail)) {
                $message = "There is no record for this order.";
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
            <?php echo !empty($detail['pet_name']) ? $detail['pet_name'] : '(not available)'; ?>
        </label>
    </div>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Petitioner Advocate
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo !empty($detail['pet_adv']) ? $detail['pet_adv'] : '(not available)'; ?>
        </label>
    </div>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Respondent name
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo !empty($detail['res_name']) ? $detail['res_name'] : '(not available)'; ?>
        </label>
    </div>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Respondent Advocate
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo !empty($detail['res_adv']) ? $detail['res_adv'] : '(not available)'; ?>
        </label>
    </div>

    <div class="form-group col-sm-12 text-center">
        <br>
        <h3> Applicant's Applied Document Detail  </h3>
        <br>
    </div>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
             Order Id
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo $order['order_id'] ?>
        </label>
    </div>


    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Case Detail
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo $order['case_no'].' / '.$order['case_year'] ?>
        </label>
    </div>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Document Type
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo str_replace(',', ' ,', $order['document_type']) ?>
        </label>
    </div>

    <div class="form-group col-sm-12">
        <label class="col-sm-4 control-label text-right">
            Document Date
        </label>
        <label class="col-sm-8 text-left bold">
            <?php echo date('d-m-Y', strtotime($order['document_date'])); ?>
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
                Free Payment By
            </label>
            <label class="col-sm-8 text-left bold">
                <?php echo $licenceNo ?>
            </label>
        </div>
    <?php } ?>


