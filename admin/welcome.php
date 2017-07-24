<?php
include "admin_access.php";
include "../layouts/master.php";
include "../layouts/database_access.php";
$pendingOrders = $inTransitOrders = array();
if (!$connection) {
    $message = "Connection Failed.";
} else {
    $query = "select * from client_order where order_status is NULL ";
    $statement = $connection->prepare($query);
    $statement->execute();
    $pendingOrders = $statement->fetchAll(PDO::FETCH_ASSOC);

    $query = "select * from client_order where order_status in ('approved', 'lapsed')";
    $statement = $connection->prepare($query);
    $statement->execute();
    $inTransitOrders = $statement->fetchAll(PDO::FETCH_ASSOC);


    $query = "select * from client_order where order_status = 'rejected'";
    $statement = $connection->prepare($query);
    $statement->execute();
    $rejectedOrders = $statement->fetchAll(PDO::FETCH_ASSOC);

    $query = "select * from client_order where order_status = 'issued'";
    $statement = $connection->prepare($query);
    $statement->execute();
    $issuedOrders = $statement->fetchAll(PDO::FETCH_ASSOC);

}
?>
<div class="box">
    <!------------------------------ Page Header -------------------------------->
    <div class="box-header">
        <ul class="nav nav-tabs">
            <li class="active" ><a data-toggle="tab" href="#pending_table">Pending Order</a></li>
            <li><a data-toggle="tab" href="#in_transit_table">In Transit Order</a></li>
            <li><a data-toggle="tab" href="#rejected_table">Rejected Order</a></li>
            <li><a data-toggle="tab" href="#issued_table">Issued Order</a></li>

        </ul>
        <a href="../search.php" class="btn btn-global btn-global-thick pull-right" style="margin-top: -43px">Search Order</a>
    </div>
    <!------------------------------- Page Body --------------------------------->
    <div class="box-body">
        <?php
        if (!empty($_SESSION['alert_message'])) {

            echo "<div class='alert ".((!empty($_SESSION['alert_type']) && $_SESSION['alert_type'] == 'success')?'alert-success':'alert-danger')."'>".$_SESSION['alert_message']."</div>";
            $_SESSION['alert_message'] = '';
            $_SESSION['alert_type'] = '';
            unset($_SESSION['alert_message']);
            unset($_SESSION['alert_type']);
         } ?>

        <div class="mt15">
            <div class="tab-content">
                <div id="pending_table" class="tab-pane fade in active">
                    <div class="visible-block sorted-records-wrapper sorted-records">
                        <div class="table-responsive">
                            <table class="table data-tables">
                                <thead>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Order Id</th>
                                    <th>CNR No.</th>
                                    <th>Case No.</th>
                                    <th>Case Year</th>
                                    <th>Payment type</th>
                                    <th>Apply Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($pendingOrders as $pendingOrder) { ?>
                                    <tr>
                                        <td><?php echo $pendingOrder['applicant_name'] ?></td>
                                        <td><?php echo $pendingOrder['order_id'] ?></td>
                                        <td><?php echo $pendingOrder['cino'] ?></td>
                                        <td><?php /*echo $pendingOrder['case_no'] */?>
                                            <a href="" data-toggle="modal" data-target="#view-detail-modal" data-id="<?php echo $pendingOrder['id']; ?>" class="view-detail no-text-decoration" title="View Detail of Order">
                                                <?php echo $pendingOrder['case_no'] ?>
                                            </a>
                                        </td>
                                        <td><?php echo $pendingOrder['case_year'] ?></td>
                                        <td><?php echo $pendingOrder['payment_type'] ?></td>
                                        <td><?php echo ($pendingOrder['apply_date']) ? date('d-m-Y', strtotime($pendingOrder['apply_date'])) : '---'?></td>
                                        <td>
                                            <?php if(empty($pendingOrder['order_status'])) { ?>
                                                <a href="" data-toggle="modal" data-target="#approve-order-modal" data-id="<?php echo $pendingOrder['id']; ?>" data-payment="<?php echo $pendingOrder['payment_type']; ?>" class="approve-order no-text-decoration" title="Approve Order">
                                                    <i class="fa fa-2x fa-check"></i>
                                                </a>
                                                <a href="" data-toggle="modal" data-target="#reject-order-modal" data-id="<?php echo $pendingOrder['id']; ?>" class="reject-order no-text-decoration" title="Reject Order">
                                                    <i class="fa fa-2x fa-times"></i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="in_transit_table" class="tab-pane fade in">
                    <div class="visible-block sorted-records-wrapper sorted-records">
                        <div class="table-responsive">
                            <table class="table data-tables">
                                <thead>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Order Id</th>
                                    <th>CNR No.</th>
                                    <th>Case No.</th>
                                    <th>Case Year</th>
                                    <th>Case Status</th>
                                    <th>Apply Date</th>
                                    <th>Doc. Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($inTransitOrders as $inTransit) { ?>
                                    <tr>
                                        <td><?php echo $inTransit['applicant_name'] ?></td>
                                        <td><?php echo $inTransit['order_id'] ?></td>
                                        <td><?php echo $inTransit['cino'] ?></td>
                                        <td><?php /*echo $inTransit['case_no'] */?>
                                            <a href="" data-toggle="modal" data-target="#view-detail-modal" data-id="<?php echo $inTransit['id']; ?>" class="view-detail no-text-decoration" title="View Detail of Order">
                                                <?php echo $inTransit['case_no'] ?>
                                            </a>
                                        </td>
                                        <td><?php echo $inTransit['case_year'] ?></td>
                                        <td><?php echo ($inTransit['order_status']) ? $inTransit['order_status'] : '---' ?></td>
                                        <td><?php echo ($inTransit['apply_date']) ? date('d-m-Y', strtotime($inTransit['apply_date'])) : '---'?></td>
                                        <td><?php echo ($inTransit['upload_date']) ? date('d-m-Y', strtotime($inTransit['upload_date'])) : '---' ?></td>
                                        <td>
                                            <?php if($inTransit['upload_date'] > date('Y-m-d')) {
                                                echo "<a class='change-upload-date-button' href='' data-id = ".$inTransit['id']." data-upload=".strtotime($inTransit['upload_date'])." data-toggle='modal' data-target='#change-upload-date-modal'><i class='fa fa-2x fa-calendar'></i></a>";
                                            } else { ?>
                                                <a href="issued-document.php?id=<?php echo $inTransit['id']?>"> Issued </a>
                                                <a href="" class="lapsed-document-button" data-toggle="modal" data-target="#lapsed-document-modal" data-id="<?php echo $inTransit['id'] ?>"> Lapsed </a>
                                            <?php }?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="rejected_table" class="tab-pane fade in">
                    <div class="visible-block sorted-records-wrapper sorted-records">
                        <div class="table-responsive">
                            <table class="table data-tables">
                                <thead>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Order Id</th>
                                    <th>CNR No.</th>
                                    <th>Case No.</th>
                                    <th>Case Year</th>
                                    <th>Case Status</th>
                                    <th>Apply Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($rejectedOrders as $rejectedOrder) { ?>
                                    <tr>
                                        <td><?php echo $rejectedOrder['applicant_name'] ?></td>
                                        <td><?php echo $rejectedOrder['order_id'] ?></td>
                                        <td><?php echo $rejectedOrder['cino'] ?></td>
                                        <td><?php /*echo $rejectedOrder['case_no'] */?>
                                            <a href="" data-toggle="modal" data-target="#view-detail-modal" data-id="<?php echo $rejectedOrder['id']; ?>" class="view-detail no-text-decoration" title="View Detail of Order">
                                                <?php echo $rejectedOrder['case_no'] ?>
                                            </a>
                                        </td>
                                        <td><?php echo $rejectedOrder['case_year'] ?></td>
                                        <td><?php echo ($rejectedOrder['order_status']) ? $rejectedOrder['order_status'] : '---' ?></td>
                                        <td><?php echo ($rejectedOrder['apply_date']) ? date('d-m-Y', strtotime($rejectedOrder['apply_date'])) : '---'?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="issued_table" class="tab-pane fade in">
                    <div class="visible-block sorted-records-wrapper sorted-records">
                        <div class="table-responsive">
                            <table class="table data-tables">
                                <thead>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Order Id</th>
                                    <th>CNR No.</th>
                                    <th>Case No.</th>
                                    <th>Case Year</th>
                                    <th>Apply Date</th>
                                    <th>Issued Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($issuedOrders as $issuedOrder) { ?>
                                    <tr>
                                        <td><?php echo $issuedOrder['applicant_name'] ?></td>
                                        <td><?php echo $issuedOrder['order_id'] ?></td>
                                        <td><?php echo $issuedOrder['cino'] ?></td>
                                        <td><?php /*echo $issuedOrder['case_no'] */?>
                                            <a href="" data-toggle="modal" data-target="#view-detail-modal" data-id="<?php echo $issuedOrder['id']; ?>" class="view-detail no-text-decoration" title="View Detail of Order">
                                                <?php echo $issuedOrder['case_no'] ?>
                                            </a>
                                        </td>
                                        <td><?php echo $issuedOrder['case_year'] ?></td>
                                        <td><?php echo ($issuedOrder['apply_date']) ? date('d-m-Y', strtotime($issuedOrder['apply_date'])) : '---'?></td>
                                        <td><?php echo ($issuedOrder['issued_date']) ? date('d-m-Y', strtotime($issuedOrder['issued_date'])) : '---' ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br clear="all" />
    </div>
</div>

<div class="modal fade" id="view-rejection-reason-modal" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Reject Reason</h5>
            </div>
            <div class="modal-body">
                <p id="rejection-reason-p"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-global btn-global-thin" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="change-upload-date-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Change Upload Date</h5>
            </div>
            <form action="change-upload-date.php" method="POST" accept-charset="UTF-8" >
                <div class="modal-body">
                    <input type="hidden" id="upload_date_id" name="order_id" value="">
                    <div class="col-sm-12 mb20">
                        <label class="col-sm-3 mt20">Upload Date<em class="required-asterik">*</em></label>
                        <div class="col-sm-9">
                            <input class="date-format form-control" id="upload_date" name="upload_date" type="text" value="" required>
                        </div>
                    </div>


                    <p>Why are you changing upload date? Please mention here reason.<em class="required-asterik">*</em></p><br>
                    <textarea class="form-control" name="change_reason" value="" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-global-thin" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-green btn-global-thin" value="Change">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="lapsed-document-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Lapsed Document</h5>
            </div>
            <form action="lapsed-document.php" method="POST" accept-charset="UTF-8" >
                <div class="modal-body">
                    <input type="hidden" id="lapsed_order_id" name="order_id" value="">
                    <div class="col-sm-12 mb20">
                        <label class="col-sm-3 mt20">New Date<em class="required-asterik">*</em></label>
                        <div class="col-sm-9">
                            <input class="date-format form-control" id="lapsed_new_date" name="lapsed_new_date" type="text" value="" required>
                        </div>
                    </div>
                    <p>Why document not uploaded on time? Please mention lapsed reason.<em class="required-asterik">*</em></p><br>
                    <textarea class="form-control" name="lapsed_reason" value="" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-global-thin" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-green btn-global-thin" value="Done">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="view-detail-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Detail of Order</h5>
            </div>
            <div class="modal-body">
                <div id="view-detail-body" class="inline-block">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-global btn-global-thin" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approve-order-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Approve Order</h5>
            </div>
            <form action="approve-order.php" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
                <div class="modal-body inline-block">
                    <input type="hidden" id="approve_order_id" name="order_id" value="">

                    <div class="form-group col-sm-12">
                        <label class="col-sm-4 control-label text-right">
                            Paid Amount
                            <em class="required-asterik">*</em>
                        </label>
                        <div class="col-sm-8 text-left bold">
                            <input class="form-control" type="number" name="paid_amount" id="paid_amount" min="0" required>
                        </div>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="col-sm-4 control-label text-right">
                            Document show to Applicant at
                            <em class="required-asterik">*</em>
                        </label>
                        <div class="col-sm-8 text-left bold">
                            <input class="date-format form-control" type="text" name="upload_date" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-global-thin" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-green btn-global-thin" value="Approve">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="reject-order-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Reject Order</h5>
            </div>
            <form action="reject-order.php" method="POST" accept-charset="UTF-8" >
                <div class="modal-body">
                    <input type="hidden" id="reject_order_id" name="order_id" value="">
                    <div class="col-sm-12 mb20">

                    </div>
                    <h1> Please mention here rejection reason<em class="required-asterik">*</em></h1><br>
                    <textarea class="form-control" name="rejection-reason" cols="6" rows="8" value="" required></textarea>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-global-thin" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-green btn-global-thin" value="Reject">
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../js/jspdf.debug.js"></script>
<script src="../js/jspdf.plugin.autotable.js"></script>
<script src="../js/faker.min.js"></script>
<script src="../js/tableExport.js"></script>
<script src="../js/jquery.base64.js"></script>


<script>

    $('.apply_date_format').datepicker({
        maxDate: new Date()
    });

    $('.rejection-reason').click(function () {

        var reason = $(this).data('reason');
        $('#rejection-reason-p').html(reason);
    });

    $('.change-upload-date-button').click(function () {

        var id = $(this).data('id');
        var upload_date = $(this).data('upload');
        var newDate = new Date(upload_date * 1000);
        $('#upload_date_id').val(id);
        $( "#upload_date" ).datepicker( "option", "maxDate", newDate );
    });

    $('.lapsed-document-button').click(function () {

        var id = $(this).data('id');
        $('#lapsed_order_id').val(id);
    });


    $('.view-detail').click(function () {
        var id = $(this).data('id');
        jQuery.ajax({
            url: 'get-detail.php?id='+id,
            type: "GET",
            success: function (data) {
                $('#view-detail-body').html(data);
            },
            error: function(xhr,status,error){
                $('#view-detail-body').html('<p>Error in View Detail</p>');
                console.log("An error "+error+" occured while ajax call " + xhr.status + " " + xhr.statusText);
            }
        });
    });

    $('.reject-order').click(function () {
        var id = $(this).data('id');
        $('#reject_order_id').val(id);
    });

    $('.approve-order').click(function () {
        var id = $(this).data('id');
        var payment_type = $(this).data('payment');

        $('#approve_order_id').val(id);
        if(payment_type == 'free') {
            $('#paid_amount').val(0);
        } else if (payment_type == 'single') {
            $('#paid_amount').val(20);
        } else if (payment_type == 'double') {
            $('#paid_amount').val(40);
        }
    });
</script>
<?php include '../layouts/footer.php' ?>
