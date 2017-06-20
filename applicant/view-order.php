<?php
session_start();
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
                                <h2> Your document is ready for Collection. </h2>
                            <?php } else { ?>
                                <h2>
                                    Yor request is approved and File will be uploaded on Date : <?php echo date('d-m-Y', strtotime($orderDetail['upload_date'])) ?>
                                </h2>
                           <?php }
                        } else { ?>
                            <h2>Sorry, You request is rejected. For Detail Discuss to Admin.</h2><br>
                            <h5><span class="bold"> Rejection Reason : </span> <?php echo $orderDetail['rejection_reason']?></h5>

                        <?php  } ?>

                    <?php } else { ?>
                        <h2>You order is in queue state. Kindly wait for your order.</h2>
                    <?php    }
                }
                ?>

            </div>

        </div>
    </div>

<?php include "../login/footer.php" ?>
