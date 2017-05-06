<?php
include "../layouts/login-access.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = '';
    $orderId = trim($_POST['order_id']);
    $rejectionReason = trim($_POST['rejection-reason']);
    if (empty($rejectionReason)) {
        $message = "Please mention rejection reason.";
    } elseif (empty($orderId)) {
        $message = "Please again select an order to reject.";
    } else {
        include "../layouts/database_access.php";
        if (!$connection) {
            $message = "Connection Failed!";
        } else {
            $query = "UPDATE client_order SET order_status = 'rejected', rejection_reason = '$rejectionReason' WHERE id = $orderId";
            $result = $connection->exec($query);
            if(!empty($result)) {
                $_SESSION['alert_message'] = "Successfully Reject Order.";
                $_SESSION['alert_type'] = "success";
                echo '<script>window.location = "welcome.php";</script>';
                exit();
            } else {
                $message = "Sorry! Oder not rejected";
                print_r($connection->errorInfo());
            }
        }
    }
} else {
    $orderId = !empty($_GET['id']) ? $_GET['id'] : '';
}
include "../layouts/master.php";
?>

<!------------------------------ Page Header -------------------------------->
<div class="box-header">
    <h3 class="pull-left"> Reject Order </h3>
</div>
<!------------------------------- Page Body --------------------------------->
<div class="box-body">
    <div class="mt15">
     <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" accept-charset="UTF-8" class="form-horizontal form-login">
         <?php if (!empty($message)) { ?>
             <div class="alert alert-danger">
                 <?php echo $message?>
             </div>
         <?php } ?>
        <h1> Please mention here rejection reason<em class="required-asterik">*</em></h1><br>
        <textarea class="form-control" name="rejection-reason" cols="6" rows="8" value="" required></textarea>

        <br><br>
         <input type="hidden" value="<?php echo $orderId; ?>" name="order_id">
        <a href="welcome.php" class="btn btn-default"> Cancel</a>
        <button type="submit" class="btn btn-global-thin btn-green"> Submit</button>
     </form>
    </div>
</div>

<?php include "../layouts/footer.php" ?>

