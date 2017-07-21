<?php
session_start();
$message = !empty($_SESSION['message']) ? $_SESSION['message'] : '';
$alertType = !empty($_SESSION['alert_type']) ? $_SESSION['alert_type'] : '';
$_SESSION['message'] = '';
$_SESSION['alert_type'] = '';
include '../layouts/database_access.php';
if (!$connection) {
    $message = "Connection Failed.";
} else {
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
        <br>
        <div class="main-center">

            <form method="POST" action="order-detail.php" accept-charset="UTF-8" class="form-horizontal form-login">
                <?php if ($message) { ?>
                    <div class="alert <?php echo $alertType ?>">
                        <?php echo $message?>
                    </div>
                <?php } ?>

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
                        <label class="control-label mb10" for="applicant_name">
                            Applicant Name
                            <em class="required-asterik">*</em>
                        </label>
                        <input id="applicant_name" class="form-control" placeholder="Applicant Name" name="applicant_name" type="text" value="" required>
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
