<?php

$baseUrl = "http://high-court:8888";
include "../layouts/database_access.php";
try {
    if (!$connection) {
        $message = "Connection Failed.";
    } else {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $uniqueOrderId = $_POST['order_id'];
        } else {
            $uniqueOrderId = !empty($_GET['order_id']) ? $_GET['order_id'] : '';
        }
        if($uniqueOrderId) {
            $query = "select * from client_order where order_id = '$uniqueOrderId'";
            $statement = $connection->prepare($query);
            $statement->execute();
            $orderDetail = $statement->fetch();
            if(empty($orderDetail)) {
                $message = "No record for this Order ID.";
            }
        }
    }
} catch (Exception $ex) {
    $message = "Error : ".$ex->getMessage();
}
include "../layouts/master.php";

?>

<!------------------------------ Page Header -------------------------------->
<div class="box-header">
    <h3 class="pull-left"> View Order Detail</h3>
</div>
<!------------------------------- Page Body --------------------------------->


<div class="modal fade" id="reject-order-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Reject Document</h5>
            </div>
            <form action="reject-document.php" method="POST" accept-charset="UTF-8" >
                <div class="modal-body">
                    <input type="hidden" name="order_id" value="<?php echo $uniqueOrderId?>">
                    <p>Please Mention here rejection reason.<em class="required-asterik">*</em></p> <br>
                    <textarea name="rejection_reason" placeholder="mention here rejection reason" value="" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-global-thin" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-green btn-global-thin" value="Submit Rejection ">
                </div>
            </form>
        </div>
    </div>
</div>


<div class="box-body">
    <div class="mt15">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">

            <?php
            if (!empty($_SESSION['alert_message'])) {

                echo "<div class='alert ".((!empty($_SESSION['alert_type']) && $_SESSION['alert_type'] == 'success')?'alert-success':'alert-danger')."'>".$_SESSION['alert_message']."</div>";
                $_SESSION['alert_message'] = '';
                $_SESSION['alert_type'] = '';
                unset($_SESSION['alert_message']);
                unset($_SESSION['alert_type']);
            } ?>

            <?php if (!empty($message)) { ?>
                <div class="alert alert-danger">
                    <?php echo $message?>
                </div>
            <?php } ?>

            <div class="form-group col-sm-12">
                <label class="col-sm-2 col-xs-12 control-label text-right mt10">
                    Order ID
                    <em class="required-asterik">*</em>
                </label>
                <div class=" col-sm-10 col-xs-12">
                    <input name="order_id" class="form-control" type="text" placeholder="Enter your Order ID here" value="<?php echo $uniqueOrderId ?>" required>
                </div>
            </div>
            <br>
            <div class="form-group col-sm-12">
                <div class="col-lg-offset-2 col-sm-10 col-xs-12 text-left bold">
                    <a href="send-order.php" class="btn btn-default btn-global-thick"> Cancel</a>
                    <input type="submit" class="btn btn-green btn-global-thick" value="Search">
                </div>
            </div>


            <div class="col-lg-offset-2 col-sm-10 col-xs-12 mt20">
                <?php
                if(!empty($orderDetail)) {
                    if($orderDetail['order_status']) {
                        if($orderDetail['order_status'] == 'approved') {

                            echo "<br><br>";
                            if(date('Y-m-d') >= $orderDetail['upload_date']) { ?>
                            <div class="pull-left">
                                <a href="<?php echo $baseUrl ?>/uploads/<?php echo $orderDetail['upload_document'] ?>" class="btn btn-global-thick btn-green">Click here to download Document</a>
                                <br><br><br>

                                <?php if(empty($orderDetail['applicant_doc_status'])) { ?>
                                    <a href="ack-document.php?order_id=<?php echo $uniqueOrderId;?>" class="btn btn-global-thick btn-green">Acknowledged</a>
                                    <button type="button" data-target="#reject-order-modal" data-toggle="modal" class="btn btn-global-thick btn-default"> Reject</button>
                                <?php
                                } elseif ($orderDetail['applicant_doc_status'] == 'ack') {
                                    echo "<h5> You acknowledged this document.</h5>";
                                 } else {
                                    echo "<h5> You reject this document.</h5>";
                                }
                                ?>
                            </div>

                            <?php } else { ?>
                                <h2>
                                    Yor request is approved and File will be uploaded on Date : <?php echo date('d-m-Y', strtotime($orderDetail['upload_date'])) ?>
                                </h2>
                           <?php }
                        } else { ?>

                            <h2>Sorry, You request is rejected, Here is the rejection detail.</h2><br><br>
                            <h5><span class="bold"> Rejection Reason : </span> <?php echo $orderDetail['rejection_reason']?></h5>

                        <?php  } ?>

                        <div>
                            <!--<h5>Complain Box</h5>
                            <textarea></textarea>-->
                        </div>

                    <?php } else { ?>
                        <h2>You order is still in pending state. Kindly wait for your order.</h2>
                    <?php    }
                }
                ?>

            </div>
        </form>
    </div>
</div>

<?php include "../layouts/footer.php" ?>
