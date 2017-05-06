<?php
include "../layouts/login-access.php";
include "../layouts/master.php";
include "../layouts/database_access.php";
$orders = [];
if (!$connection) {
    $message = "Connection Failed.";
} else {
    $query = "select * from client_order";
    $orders = $connection->query($query);
}
?>
    <!------------------------------ Page Header -------------------------------->
    <div class="box-header">
        <h3 class="pull-left"> Order Listing </h3>
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
            <div class="list-shops">
                <div class="visible-block sorted-records-wrapper sorted-records">
                    <table class="table data-tables">
                        <thead>
                        <tr>
                            <th>Applicant Name</th>
                            <th>Case type</th>
                            <th>Case No.</th>
                            <th>Case Year</th>
                            <th>Case Status</th>
                            <th>Payment type</th>
                            <th>Document type</th>
                            <th>Document Date</th>
                            <th>Document status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($orders as $order) { $reason = $order['applicant_doc_rejection_reason'] ?>
                        <tr>
                            <td><?php echo $order['applicant_name'] ?></td>
                            <td><?php echo $order['case_type'] ?></td>
                            <td><?php echo $order['case_no'] ?></td>
                            <td><?php echo $order['case_year'] ?></td>
                            <td><?php echo ($order['order_status']) ? $order['order_status'] : '---' ?></td>
                            <td><?php echo $order['payment_type'] ?></td>
                            <td><?php echo $order['document_type'] ?></td>
                            <td><?php echo date('d-m-Y', strtotime($order['document_date'])) ?></td>
                            <td><?php echo (!$order['applicant_doc_status']) ? '---' : (($order['applicant_doc_status'] == 'rejected') ?
                                    "<a class='rejection-reason' data-reason='".$reason."' href='' data-toggle='modal' data-target='#view-rejection-reason-modal'> rejected </a>"
                                    : $order['applicant_doc_status']) ?>
                            </td>
                            <td>
                                <?php if(empty($order['order_status'])) { ?>
                                    <a href="approve-order.php?id=<?php echo $order['id'] ?>" class="no-text-decoration" title="View Detail of Order">
                                        View Detail
                                    </a>
                                <?php } else {
                                    if($order['order_status'] == 'approved' && !$order['applicant_doc_status'] && $order['upload_date'] > date('Y-m-d')) {
                                        echo "<a class='change-upload-date-button' href='' data-id = ".$order['id']." data-upload=".strtotime($order['upload_date'])." data-toggle='modal' data-target='#change-upload-date-modal'>Upload Date</a>";
                                    }
                                } ?>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <br clear="all" />
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
                <button type="button" class="btn btn-default btn-global-thin" data-dismiss="modal"></button>
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
                            <input class="date-format form-control" id="upload_date" name="upload_date" type="text" value="">
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


<script>
    $('.rejection-reason').click(function () {

        var reason = $(this).data('reason');
        console.log(reason);
        $('#rejection-reason-p').html(reason);
    });


    $('.change-upload-date-button').click(function () {

        var id = $(this).data('id');
        var upload_date = $(this).data('upload');
        var newDate = new Date(upload_date * 1000);
        $('#upload_date_id').val(id);
        $( "#upload_date" ).datepicker( "option", "maxDate", newDate );
    });

</script>
<?php include '../layouts/footer.php' ?>
