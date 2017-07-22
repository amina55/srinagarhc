<?php
session_start();
$message = '';
$alertType = 'alert-danger';
$name = trim($_POST['applicant_name']);
$caseType = trim($_POST['case_type']);
$caseNo = trim($_POST['case_no']);
$caseYear = trim($_POST['case_year']);
$docTypeArray = $_POST['document_type'];
$documentType = implode(",", $docTypeArray);
$documentDate = trim($_POST['document_date']);
$paymentType = trim($_POST['payment_type']);
$licenceNo  = '';
$orderId = '';

include '../layouts/database_access.php';
if (!$connection) {
    $message = "Connection Failed.";
} else {
    try {
        $query = "select case_type, type_name from case_type_t where case_type = :case_type";
        $statement = $connection->prepare($query);
        $statement->execute(array('case_type' => $caseType));
        $caseTypeName = $statement->fetch();
        $orderId = $caseTypeName['type_name'].'-'.$caseNo.'-'.$caseYear.'-'.str_pad(rand(0, 999), '3', '0', STR_PAD_LEFT);

        if (empty($name) || empty($caseYear) || empty($caseNo) || empty($caseType) || empty($documentType) || empty($paymentType) || empty($documentDate)) {
            $message = "Required Parameter is missing";
        } else {
            if($paymentType == 'free') {
                $licenceNo = trim($_POST['licence_no']);
                if(!$licenceNo) {
                    $message = "Officer Detail is required for free payment.";
                } elseif($licenceNo == 'Other') {
                    $otherDetail = $_POST['other_detail'];
                    if(!$otherDetail) {
                        $message = "Officer Detail is required for free payment. So please mention Other Detail";
                    } else {
                        $licenceNo = $otherDetail.' (Other)';
                    }
                }
            }
            if(!$message) {
                $id = '';
                $query = "select fil_no, fil_year, pet_name, res_name, pet_adv, res_adv from civil_t where fil_no = $caseNo and filcase_type = $caseType  and fil_year = $caseYear";
                $statement = $connection->prepare($query);
                $statement->execute();
                $detail = $statement->fetch();

                if(!empty($detail)) {
                    if(in_array('judgement', $docTypeArray)) {
                        $message = "Case in pending state so Document type should not be 'judgement'";
                        $detail = null;
                    }
                } else {
                    $query = "select fil_no, fil_year, pet_name, res_name, pet_adv, res_adv from civil_t_a where fil_no = $caseNo and filcase_type = $caseType  and fil_year = $caseYear";
                    $statement = $connection->prepare($query);
                    $statement->execute();
                    $detail = $statement->fetch();
                }
                if(empty($detail) && !$message) {
                    $message = "There is no record of this case, kindly request for a valid case.";
                }
            }
        }
    } catch (Exception $e) {
        $message = "Error : " . $e->getMessage();
    }
}
if($message) {
    $_SESSION['message'] = $message;
    $_SESSION['alert_type'] = $alertType;
    header('Location: send-order.php'); exit;
}
include "../login/master.php";
?>
<div class="row main">
    <div class="main-login">
        <div class="main-center2">
            <form method="POST" action="save-order.php" accept-charset="UTF-8" class="form-horizontal form-login">

                <input type="hidden" name="applicant_name" value="<?php echo $name; ?>">
                <input type="hidden" name="case_type" value="<?php echo $caseType; ?>">
                <input type="hidden" name="case_no" value="<?php echo $caseNo; ?>">
                <input type="hidden" name=" case_year" value="<?php echo $caseYear; ?>">
                <input type="hidden" name="document_type" value="<?php echo $documentType; ?>">
                <input type="hidden" name="document_date" value="<?php echo $documentDate; ?>">
                <input type="hidden" name="payment_type" value="<?php echo $paymentType; ?>">
                <input type="hidden" name="licence_no" value="<?php echo $licenceNo; ?>">
                <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                <input type="hidden" name="fil_no" value="<?php echo $detail['fil_no']; ?>">
                <input type="hidden" name="fil_year" value="<?php echo $detail['fil_year']; ?>">

                <br>
                <div class="form-group col-sm-12">
                    <label class="col-sm-4 control-label text-right">
                        Petitioner name
                    </label>
                    <label class="col-sm-8 left-align-label">
                        <?php echo !empty($detail['pet_name']) ? $detail['pet_name'] : '(not available)'; ?>
                    </label>
                </div>

                <div class="form-group col-sm-12">
                    <label class="col-sm-4 control-label text-right">
                        Petitioner Advocate
                    </label>
                    <label class="col-sm-8 left-align-label">
                        <?php echo !empty($detail['pet_adv']) ? $detail['pet_adv'] : '(not available)'; ?>
                    </label>
                </div>

                <div class="form-group col-sm-12">
                    <label class="col-sm-4 control-label text-right">
                        Respondent name
                    </label>
                    <label class="col-sm-8 left-align-label">
                        <?php echo !empty($detail['res_name']) ? $detail['res_name'] : '(not available)'; ?>
                    </label>
                </div>

                <div class="form-group col-sm-12">
                    <label class="col-sm-4 control-label text-right">
                        Respondent Advocate
                    </label>
                    <label class="col-sm-8 left-align-label">
                        <?php echo !empty($detail['res_adv']) ? $detail['res_adv'] : '(not available)'; ?>
                    </label>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <a class="btn btn-lg large-button" href="send-order.php">Cancel</a>
                        <input class="btn btn-lg large-button pull-right" type="submit" value="Save Order">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../login/footer.php" ?>



