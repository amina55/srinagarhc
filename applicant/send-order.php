<?php
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
            $documentType = implode(",", $_POST['document_type']);
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
                    $id = '';
                    $query = "select fil_no from civil_t where fil_no = $caseNo and filcase_type = $caseType  and fil_year = $caseYear";
                    $statement = $connection->prepare($query);
                    $statement->execute();
                    $detail = $statement->fetch();

                    if(!empty($detail['fil_no'])) {
                        $id = $detail['fil_no'];
                    } else {
                        $query = "select fil_no from civil_t_a where fil_no = $caseNo and filcase_type = $caseType  and fil_year = $caseYear";
                        $statement = $connection->prepare($query);
                        $statement->execute();
                        $detail = $statement->fetch();
                        if(!empty($detail['fil_no'])) {
                            $id = $detail['fil_no'];
                        }
                    }

                    if($id) {
                        $currentDate = date('m/d/Y');
                        $insertQuery = "INSERT INTO client_order (applicant_name, case_type, case_no, case_year, payment_type, document_type, document_date, order_id, licence_no, apply_date) " .
                            "VALUES  ('$name', $caseType, $caseNo, $caseYear, '$paymentType', '$documentType', '$documentDate', '$orderId', '$licenceNo', '$currentDate')";

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
include "../login/master.php";
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
        <a href="view-order.php" class="btn btn-lg large-button"> View Order Detail</a>
      <!--  <a href="welcome.php" class="btn btn-lg large-button"> Back to Detail</a>-->

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
                        <input class="form-control" type="number" placeholder="Case Year" name="case_year" min="1700" max="<?php echo date('Y') ?>">
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

                        <br>
                        <input type="checkbox" id="all_doc_type">All<br>
                        <input type="checkbox" name="document_type[]" value="petition_copy">Petition copy<br>
                            <input type="checkbox" name="document_type[]" value="writ">Writ<br>
                            <input type="checkbox" name="document_type[]" value="objection">Objection<br>
                            <input type="checkbox" name="document_type[]" value="vakaltnama">Vakaltnama<br>
                            <input type="checkbox" name="document_type[]" value="order">Order<br>
                            <input type="checkbox" name="document_type[]" value="judgement">Judgement<br>
                            <input type="checkbox" name="document_type[]" value="CMP">CMP<br>
                            <input type="checkbox" name="document_type[]" value="reply">Reply<br>
                            <input type="checkbox" name="document_type[]" value="rejoinder">Rejoinder<br>
                            <input type="checkbox" name="document_type[]" value="affidavit">Affidavit<br>
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
                        <input class="btn btn-lg btn-block large-button" type="submit" value="Save">
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

        $('#all_doc_type').click(function () {
            var value = this.checked;
            $('[name^=document_type]').attr('checked', value);
        });
    </script>
    <?php include "../login/footer.php" ?>
