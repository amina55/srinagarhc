<?php
include "applicant_access.php";
$message = '';
$alertType = 'alert-danger';
include '../layouts/database_access.php';
if (!$connection) {
    $message = "Connection Failed.";
} else {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $name = trim($_POST['applicant_name']);
            $caseType = trim($_POST['case_type']);
            $caseNo = trim($_POST['case_no']);
            $caseYear = trim($_POST['case_year']);
            $documentType = trim($_POST['document_type']);
            $documentDate = trim($_POST['document_date']);
            $paymentType = trim($_POST['payment_type']);
            $licenceNo  = '';

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
                        $message = "Licence Number is required for free payment.";
                    }
                }
                if(!$message) {
                    $query = "select fil_no from civil_t where fil_no = $caseNo and filcase_type = $caseType  and fil_year = $caseYear";
                    $id = '';
                    $details = $connection->query($query);
                    foreach ($details as $detail) {
                        $id = $detail['fil_no'];
                    }

                    if($id) {
                        $userId = $_SESSION['logged_in_user']['id'];
                        $insertQuery = "INSERT INTO client_order (applicant_name, case_type, case_no, case_year, payment_type, document_type, document_date, order_id, licence_no, user_id) " .
                            "VALUES  ('$name', $caseType, $caseNo, $caseYear, '$paymentType', '$documentType', '$documentDate', '$orderId', '$licenceNo', $userId)";

                        $result = $connection->exec($insertQuery);
                        if (empty($result)) {
                            $message = "Error in Sending Order";
                        } else {
                            $message = "Your record is successfully entered. Please note this Order Id to view your order data. Order Id is : " . $orderId;
                            $alertType = 'alert-success';
                        }
                    } else {
                        $message = "There is no record of this case, kindly request for a valid case.";
                    }
                }
            }
        } catch (Exception $e) {
            $message = "Error : " . $e->getMessage();
        }
    }
    $query = "select case_type, type_name from case_type_t";
    $caseTypes = $connection->query($query);
}
include "../layouts/mystyle-master.php";
?>
<script>
    $( function() {
        $( ".date-format" ).datepicker({
            maxDate: new Date()
        });
    });
</script>
<div class="row main">

    <div class="main-login">
        <!--<a href="view-order.php" class="btn btn-lg large-button"> View Order Detail</a>-->
        <a href="welcome.php" class="btn btn-lg large-button"> Back to Detail</a>

        <br>
        <div class="main-center">

            <form method="POST" action="" accept-charset="UTF-8" class="form-horizontal form-login">
                <?php if ($message) { ?>
                    <div class="alert <?php echo $alertType ?>">
                        <?php echo $message?>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label mb10" for="applicant_name">
                            Applicant Name
                            <em class="required-asterik">*</em>
                        </label>
                        <input id="applicant_name" class="form-control" placeholder="Applicant Name" name="applicant_name" type="text" value="" required>
                        <span class="error-message"></span>
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-sm-12">
                        <label class="control-label mb10" for="case_type">
                            Case type
                            <em class="required-asterik">*</em>
                        </label>
                        <select class="form-control" name="case_type">

                            <?php foreach ($caseTypes as $caseType) { ?>
                                <option value="<?php echo $caseType['case_type'];?>"> <?php echo $caseType['type_name']/*.'-'.$caseType['case_type']*/;?></option>
                            <?php } ?>
                        </select>
                        <span class="error-message"></span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label mb10" for="case_no">
                            Case No.
                            <em class="required-asterik">*</em>
                        </label>
                        <input type="number" id="case_no" class="form-control" placeholder="Case No." name="case_no" value="" required>
                        <span class="error-message"></span>
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-sm-12">
                        <label class="control-label mb10" for="case_year">
                            Case Year
                            <em class="required-asterik">*</em>
                        </label>
                        <input class="form-control" type="number" placeholder="Case Year" name="case_year" min="1950" max="<?php echo date('Y') ?>">
                        <span class="error-message"></span>
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-sm-12">
                        <label class="control-label mb10" for="document_date">
                            Order Date
                            <em class="required-asterik">*</em>
                        </label>
                        <input class="date-format form-control" placeholder="Order Date" type="text" name="document_date">
                        <span class="error-message"></span>
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-sm-12">
                        <label class="control-label mb10" for="document_type">
                            Document type
                            <em class="required-asterik">*</em>
                        </label>
                        <select class="form-control" name="document_type">
                            <option value="petition_copy">Petition copy</option>
                            <option value="writ">Writ</option>
                            <option value="objection">Objection</option>
                            <option value="vakaltnama">Vakaltnama</option>
                            <option value="order">Order</option>
                            <option value="judgement">Judgement</option>
                            <option value="CMP">CMP</option>
                            <option value="reply">Reply</option>
                            <option value="rejoinder">Rejoinder</option>
                            <option value="affidavit">Affidavit</option>
                        </select>
                        <span class="error-message"></span>
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-sm-12">
                        <label class="control-label mb10" for="payment_type">
                            Payment type
                            <em class="required-asterik">*</em>
                        </label>
                        <select class="form-control" id="payment_type" name="payment_type">
                            <option value="single">Single (20₹)</option>
                            <option value="double">Double (40₹)</option>
                            <option value="free">Free</option>
                        </select>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div id="licence_no_div" class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label mb10" for="licence_no">
                            Licence No.
                            <em class="required-asterik">*</em>
                        </label>
                        <input id="licence_no" class="form-control" placeholder="Mention Lawyer Licence No. here" name="licence_no" type="text" value="">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <input class="btn btn-lg btn-block large-button text-uppercase" type="submit" value="Send Order">
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script>
        $('#licence_no_div').hide();

        $('#payment_type').change(function () {
            var payment_type = $(this).val();
            if(payment_type == 'free') {
                $('#licence_no_div').show();
            } else {
                $('#licence_no_div').hide();
            }
        });
    </script>
    <?php include "../login/footer.php" ?>
