<?php
include "applicant_access.php";
include "../layouts/database_access.php";
try {
    if (!$connection) {
        $message = "Connection Failed.";
    } else {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $uniqueOrderId = trim($_POST['order_id']);
        } else {
            $uniqueOrderId = !empty($_GET['order_id']) ? trim($_GET['order_id']) : '';
        }
        if($uniqueOrderId) {
            $query = "select * from client_order where order_id = '$uniqueOrderId'";
            $statement = $connection->prepare($query);
            $statement->execute();
            $orderDetail = $statement->fetch(PDO::FETCH_ASSOC);
            if(empty($orderDetail)) {
                $message = "No record for this Order ID.";
            }
        }
    }
} catch (Exception $ex) {
    $message = "Error : ".$ex->getMessage();
}
include "../login/master.php";

?>
<div class="container">
    <div class="row main">
        <div class="main-login main-center">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">

            <?php
/*            if (!empty($_SESSION['alert_message'])) {

                echo "<div class='alert ".((!empty($_SESSION['alert_type']) && $_SESSION['alert_type'] == 'success')?'alert-success':'alert-danger')."'>".$_SESSION['alert_message']."</div>";
                $_SESSION['alert_message'] = '';
                $_SESSION['alert_type'] = '';
                unset($_SESSION['alert_message']);
                unset($_SESSION['alert_type']);
            } */?>

            <?php if (!empty($message)) { ?>
                <div class="alert alert-danger">
                    <?php echo $message?>
                </div>
            <?php } ?>
                <div class="form-group">
                    <label for="order_id" class="cols-sm-2 control-label">Order Id <em class="required-asterik">*</em></label>
                    <div class="cols-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-id-badge fa" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="order_id" id="order_id"  placeholder="Enter your Order Id" required/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                        <input type="submit" class="btn large-button btn-block btn-lg" value="Search">
                </div>
            </form>
        </div>


            <div class="col-sm-12 mt20 text-center">
                <?php
                if(!empty($orderDetail)) {
                    if($orderDetail['order_status']) {
                        if($orderDetail['order_status'] == 'approved') {
                            if(date('Y-m-d') >= $orderDetail['upload_date']) { ?>
                                <?php if(empty($orderDetail['applicant_doc_status'])) { ?>
                                    <h2>After read Document, Kindly give your feedback.</h2>
                                    <br>
                                    <a href="../uploads/<?php echo $orderDetail['upload_document'] ?>" class="btn no-text-decoration" download="<?php echo $uniqueOrderId?>.pdf">Click here to download Document</a>
                                    <br><br>
                                    <a href="ack-document.php?order_id=<?php echo $uniqueOrderId;?>" class="btn btn-global-thick btn-global">Acknowledged</a>
                                    <button type="button" data-target="#reject-order-modal" data-toggle="modal" class="btn btn-global-thick btn-default"> Reject</button>
                                <?php
                                } elseif ($orderDetail['applicant_doc_status'] == 'ack') {
                                    echo "<h2> You acknowledged this document.</h2>";
                                 } else {
                                    echo "<h2> You reject this document.</h2>";
                                }
                                ?>
                            <?php } else { ?>
                                <h2>
                                    Yor request is approved and File will be uploaded on Date : <?php echo date('d-m-Y', strtotime($orderDetail['upload_date'])) ?>
                                </h2>
                           <?php }
                        } else { ?>

                            <h2>Sorry, You request is rejected. For Detail Discuss to Admin.</h2><br>
                            <h5><span class="bold"> Rejection Reason : </span> <?php echo $orderDetail['rejection_reason']?></h5>

                        <?php  } ?>

                        <div>
                            <!--<h5>Complain Box</h5>
                            <textarea></textarea>-->
                        </div>

                    <?php } else { ?>
                        <h2>You order is in queue state. Kindly wait for your order.</h2>
                    <?php    }
                }
                ?>

            </div>
        </div>
    </div>
</div>
</div>



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
                        <textarea class="form-control" name="rejection_reason" placeholder="mention here rejection reason" value="" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-global-thin" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-global btn-global-thin" value="Submit Rejection ">
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include "../login/footer.php" ?>
