<?php
include "applicant_access.php";
include "../layouts/master.php";
include "../layouts/database_access.php";
$pendingOrders = $disposedOrders = [];
if (!$connection) {
    $message = "Connection Failed.";
} else {
    $userId = $_SESSION['logged_in_user']['id'];
    $query = "select * from client_order where user_id = $userId AND order_status is NULL ";
    $statement = $connection->prepare($query);
    $statement->execute();
    $pendingOrders = $statement->fetchAll(PDO::FETCH_ASSOC);

    $query = "select * from client_order where user_id = $userId AND order_status is NOT NULL ";
    $statement = $connection->prepare($query);
    $statement->execute();
    $pendingOrder = $statement->fetchAll(PDO::FETCH_ASSOC);
    $disposedOrders = $connection->query($query);
}
?>
<div class="box">
    <a href="send-order.php" class="btn btn-global btn-global-thick pull-right"> Apply for Certified Copy</a>

    <!------------------------------ Page Header -------------------------------->
    <div class="box-header">
        <ul class="nav nav-tabs">
            <li><a data-toggle="tab" href="#pending_table">Pending Order</a></li>
            <li><a data-toggle="tab" href="#disposed_table">Processed Order</a></li>
        </ul>

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
                                    <th>Order Id</th>
                                    <th>Case No.</th>
                                    <th>Document type</th>
                                    <th>Document Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($pendingOrders as $pendingOrder) { $reason = $pendingOrder['applicant_doc_rejection_reason'] ?>
                                    <tr>
                                        <td><?php echo $pendingOrder['order_id'] ?></td>
                                        <td><?php echo $pendingOrder['case_no'] ?>
                                            <!--<a href="" data-toggle="modal" data-target="#view-detail-modal" data-id="<?php /*echo $pendingOrder['id']; */?>" class="view-detail no-text-decoration" title="View Detail of Order">
                                                <?php /*echo $pendingOrder['case_no'] */?>
                                            </a>-->
                                        </td>
                                        <td><?php echo $pendingOrder['document_type'] ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($pendingOrder['document_date'])) ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="disposed_table" class="tab-pane fade in">
                    <div class="visible-block sorted-records-wrapper sorted-records">
                        <div class="table-responsive">
                            <table class="table data-tables">
                                <thead>
                                <tr>
                                    <th>Order Id</th>
                                    <th>Case No.</th>
                                    <th>Case Status</th>
                                    <th>Document type</th>
                                    <th>Document Date</th>
                                    <th>Document status</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($disposedOrders as $disposedOrder) { $reason = $disposedOrder['applicant_doc_rejection_reason'] ?>
                                    <tr>
                                        <td><?php echo $disposedOrder['order_id'] ?></td>
                                        <td><?php echo $disposedOrder['case_no'] ?>
                                            <!--<a href="" data-toggle="modal" data-target="#view-detail-modal" data-id="<?php /*echo $disposedOrder['id']; */?>" class="view-detail no-text-decoration" title="View Detail of Order">
                                                <?php /*echo $disposedOrder['case_no'] */?>
                                            </a>-->
                                        </td>
                                        <td><?php echo ($disposedOrder['order_status']) ? $disposedOrder['order_status'] : '---' ?></td>
                                        <td><?php echo $disposedOrder['document_type'] ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($disposedOrder['document_date'])) ?></td>
                                        <td><?php echo (!$disposedOrder['applicant_doc_status']) ? '---' : (($disposedOrder['applicant_doc_status'] == 'rejected') ?
                                                "<a class='rejection-reason' data-reason='".$reason."' href='' data-toggle='modal' data-target='#view-rejection-reason-modal'> rejected </a>"
                                                : $disposedOrder['applicant_doc_status']) ?>
                                        </td>
                                        <td><?php echo ($disposedOrder['upload_date']) ? date('d-m-Y', strtotime($disposedOrder['upload_date'])) : 'not uploaded' ?></td>

                                        <td>
                                            <?php
                                                if($disposedOrder['order_status'] == 'approved' && !$disposedOrder['applicant_doc_status'] && $disposedOrder['upload_date'] <= date('Y-m-d')) {
                                                    echo "<a class='' href='../uploads/".$disposedOrder['upload_document']."'  download='".$disposedOrder['order_id']."'><i class='fa fa-download'></i></a>";
                                                }
                                            ?>
                                        </td>
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
                            <input class="form-control" type="number" name="paid_amount" min="0" required>
                        </div>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="col-sm-4 control-label text-right">
                            Upload Document
                            <em class="required-asterik">*</em>
                        </label>
                        <div class="col-sm-8 text-left bold">
                            <input class="form-control" type="file" name="upload_document" accept="application/pdf" required>
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
<script>
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
        $('#approve_order_id').val(id);
    });

</script>
<?php include '../layouts/footer.php' ?>
