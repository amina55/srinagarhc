<?php
include "../layouts/login-access.php";
include "../layouts/database_access.php";
try {
    if (!$connection) {
        $message = "Connection Failed.";
    } else {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                $_SESSION['alert_message'] = "Successfully Uploaded and Approved Order.";
                $_SESSION['alert_type'] = "success";
                echo '<script>window.location = "welcome.php";</script>';
                exit();
            } else {
                $message = "Applicant Order not accepted. Please Try agian!";
            }

        } else {
            $orderId = $_GET['id'];
        }
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
                    if(empty($petName)) {
                        $message = "There is no record for this order.";
                    }
                } else {
                    $message = "There is no record for this order.";
                }
            }
        }
    }
    include "../layouts/master.php";
} catch (Exception $ex) {
    $message = "Error : ".$ex->getMessage();
}

?>

<!------------------------------ Page Header -------------------------------->
<div class="box-header">
    <h3 class="pull-left"> Approve Order </h3>
    <a href="reject-order.php?id=<?php echo $orderId ?>" class="btn btn-green pull-right" title="Reject client Order">
        Reject Order
    </a>
</div>
<!------------------------------- Page Body --------------------------------->
<div class="box-body">
    <div class="mt15">

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
            <?php if (!empty($message)) { ?>
                <div class="alert alert-danger">
                    <?php echo $message?>
                </div>
            <?php } ?>

            <input type="hidden" name="order_id" value="<?php echo $orderId ?>">
            <div class="form-group col-sm-12">
                <label class="col-sm-4 col-xs-12 control-label text-right">
                    Petitioner name
                </label>
                <label class="col-sm-8 col-xs-12 text-left bold">
                    <?php echo $petName ?>
                </label>
            </div>

            <div class="form-group col-sm-12">
                <label class="col-sm-4 col-xs-12 control-label text-right">
                    Petitioner Advocate
                </label>
                <label class="col-sm-8 col-xs-12 text-left bold">
                    <?php echo $petAdv ?>
                </label>
            </div>

            <div class="form-group col-sm-12">
                <label class="col-sm-4 col-xs-12 control-label text-right">
                    Respondent name
                </label>
                <label class="col-sm-8 col-xs-12 text-left bold">
                    <?php echo $resName ?>
                </label>
            </div>

            <div class="form-group col-sm-12">
                <label class="col-sm-4 col-xs-12 control-label text-right">
                    Respondent Advocate
                </label>
                <label class="col-sm-8 col-xs-12 text-left bold">
                    <?php echo $resAdv ?>
                </label>
            </div>

            <div class="form-group col-sm-12">
                <label class="col-sm-4 col-xs-12 control-label text-right">
                   Payment Type
                </label>
                <label class="col-sm-8 col-xs-12 text-left bold">
                    <?php echo $paymentType ?>
                </label>
            </div>

            <?php if($paymentType == 'free') { ?>
                <div class="form-group col-sm-12">
                    <label class="col-sm-4 col-xs-12 control-label text-right">
                        Licence Number
                    </label>
                    <label class="col-sm-8 col-xs-12 text-left bold">
                        <?php echo $licenceNo ?>
                    </label>
                </div>
            <?php } ?>


            <div class="form-group col-sm-12">
                <label class="col-sm-4 col-xs-12 control-label text-right">
                    Paid Amount
                    <em class="required-asterik">*</em>
                </label>
                <div class="col-sm-8 col-xs-12 text-left bold">
                    <input class="form-control" type="number" name="paid_amount" min="0"
                           value="<?php echo ($paymentType == 'double') ? 20 : (($paymentType == 'single') ? 10 : 0); ?>" required>
                </div>
            </div>

            <div class="form-group col-sm-12">
                <label class="col-sm-4 col-xs-12 control-label text-right">
                    Upload Document
                    <em class="required-asterik">*</em>
                </label>
                <div class="col-sm-8 col-xs-12 text-left bold">
                    <input class="form-control" type="file" name="upload_document" accept="application/pdf" required>
                </div>
            </div>

            <?php if($paymentType != 'double') { ?>
            <div class="form-group col-sm-12">
                <label class="col-sm-4 col-xs-12 control-label text-right">
                    Document show to Applicant at
                    <em class="required-asterik">*</em>
                </label>
                <div class="col-sm-8 col-xs-12 text-left bold">
                    <input class="date-format form-control" type="text" name="upload_date" required>
                </div>
            </div>
            <?php } ?>

            <div class="form-group col-sm-12">
                <div class="col-lg-offset-3 col-sm-5 col-xs-12 text-left bold">
                    <a href="welcome.php" class="btn btn-default"> Cancel</a>
                    <input type="submit" class="btn btn-green" value="Approve Order">
            </div>
        </form>
    </div>
</div>
<?php include "../layouts/footer.php" ?>