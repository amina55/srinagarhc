<?php
include "../layouts/master.php";
include "../layouts/database_access.php";
$pendingOrders = $disposedOrders = array();
$tableHeading = '';
$currentYear = date('Y');
$displayTable = false;
$reasonColumnShow = false;
if (!$connection) {
    $message = "Connection Failed.";
} else {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $displayTable = true;

        $applyStartDate = trim($_POST['applied_date']);
        $applyEndDate = trim($_POST['date_to']);

        $startYear = trim($_POST['start_year']);
        $endYear = trim($_POST['end_year']);

        $filingNo = trim($_POST['filling_no']);
        $filingYear = trim($_POST['filling_year']);

        $caseType = trim($_POST['case_type']);
        $caseYear = trim($_POST['case_year']);
        $caseNo = trim($_POST['case_no']);

        $paymentType = trim($_POST['payment_type']);
        $caseStatus = trim($_POST['case_status']);
        $cino = trim($_POST['cino']);

        $appliedBy = trim($_POST['applied_by']);

        $whereQuery = $finalQuery = $applyDateQuery = $yearQuery = '';
        if($applyStartDate || $applyEndDate) {
            if($applyStartDate && $applyEndDate) {
                if ($applyStartDate > $applyEndDate) {
                    $message = " 'Applied to' Date should be less than 'Applied Date'.";
                    $displayTable = false;
                } else {
                    $applyDateQuery = " apply_date between '$applyStartDate' and '$applyEndDate' ";
                    $tableHeading = 'Apply Date ('.$applyStartDate.'-'.$applyEndDate.') ,';
                }
            } else {
                $date = ($applyStartDate) ? $applyStartDate : $applyEndDate;
                $applyDateQuery = "apply_date = '$date'";
                $tableHeading = 'Apply Date ('.$date.'),';
            }
            if($applyDateQuery) {
                $whereQuery .= $applyDateQuery." and";
            }
        }

        if($startYear || $endYear) {
            if($startYear && $endYear) {
                if ($startYear > $endYear) {
                    $message = "Start Year should be less than End Year.";
                    $displayTable = false;
                } else {
                    $yearQuery = " apply_year between $startYear and $endYear ";
                    $tableHeading .= 'Year ('.$startYear.'-'.$endYear.') ,';
                }
            } else {
                $year = ($startYear) ? $startYear : $endYear;
                $yearQuery = "apply_year = $year";
                $tableHeading .= 'Year ('.$year.'),';
            }
            if($yearQuery) {
                $whereQuery .= $yearQuery." and";
            }
        }

        if($displayTable) {
            $finalQuery = 'Select * from client_order';

            $whereQuery .= ($filingNo) ? " fil_no = $filingNo  and " : '';
            $whereQuery .= ($filingYear) ? " fil_year = $filingYear and " : '';
            $whereQuery .= ($caseType) ? " case_type = $caseType  and " : '';
            $whereQuery .= ($caseNo) ? " case_no = $caseNo  and " : '';
            $whereQuery .= ($caseYear) ? " case_year = $caseYear  and " : '';
            $whereQuery .= ($appliedBy) ? " applicant_name like  '%$appliedBy%'  and " : '';
            $whereQuery .= ($paymentType) ? " payment_type = '$paymentType'  and " : '';
            $whereQuery .= ($caseStatus) ? (($caseStatus == 'pending') ? " order_status is null and " : " order_status = '$caseStatus'  and ") : '';
            $whereQuery .= ($cino) ? " cino = '$cino' and " : '';

            $reasonColumnShow = ($caseStatus == 'rejected' || $caseStatus == 'lapsed' ) ? true : false;

            if($whereQuery) {
                $finalQuery .= ' where '. rtrim($whereQuery, 'and ');
            }
            $statement = $connection->prepare($finalQuery);
            $statement->execute();
            $searchOrders = $statement->fetchAll(PDO::FETCH_ASSOC);
        }

    }

    $caseTypeQuery = "select case_type, type_name from case_type_t";
    $statement = $connection->prepare($caseTypeQuery);
    $statement->execute();
    $caseTypes = $statement->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="box">
    <!------------------------------ Page Header -------------------------------->
    <div class="box-header">
        <h3>Search Order</h3>
            <!--<a href="welcome.php" class="btn btn-global btn-global-thick pull-right" style="margin-top: -43px">View Order</a>-->
    </div>
    <!------------------------------- Page Body --------------------------------->
    <div class="box-body">
        <?php
        if (!empty($message) ) { ?>
        <div class="alert alert-danger"> <?php echo $message; ?> </div>

        <?php } ?>

        <div class="mt15">
            <form action="search.php" method="post" class="form-horizontal">
                <div class="form-group">
                    <div class="mt20 col-sm-12">
                        <label class="col-sm-1 mt10"> Case Type </label>

                        <div class="col-sm-3">
                            <select  name="case_type" class="form-control">
                                <option value="">All</option>
                                <?php foreach ($caseTypes as $case) {
                                    echo "<option value='".$case['case_type']."'>".$case['type_name']."</option>";
                                } ?>
                            </select>
                        </div>

                        <label class="col-sm-1"> Case No : </label>
                        <div class="col-sm-3">
                            <input placeholder="Case No" class="form-control" type="number" name="case_no" min="1">
                        </div>

                        <label class="col-sm-1"> Case Year </label>
                        <div class="col-sm-3">
                            <input placeholder="Case Year" class="form-control" type="number" name="case_year" min="1800" max="<?php echo $currentYear; ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="mt20 col-sm-12">
                        <label class="col-sm-2 mt10"> Payment </label>

                        <div class="col-sm-2">
                            <select  name="payment_type" class="form-control">
                                <option value="">All</option>
                                <option value="single">Single</option>
                                <option value="double">Double</option>
                                <option value="free">Free</option>
                            </select>
                        </div>

                        <label class="col-sm-2"> Case Status </label>
                        <div class="col-sm-2">
                            <select  name="case_status" class="form-control">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="lapsed">Lapsed</option>
                                <option value="rejected">Rejected</option>
                                <option value="issued">Issued</option>
                            </select>
                        </div>

                        <label class="col-sm-1"> CNR No. </label>
                        <div class="col-sm-3">
                            <input placeholder="CNR No." class="form-control" type="text" name="cino">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="mt20 col-sm-12">
                        <label class="col-sm-2"> Applied Date : </label>
                        <div class="col-sm-4">
                            <input placeholder="Applied Date" class="apply_date_format form-control" type="text" name="applied_date">
                        </div>

                        <label class="col-sm-2"> Date to : </label>
                        <div class="col-sm-4">
                            <input placeholder="Date to" class="apply_date_format form-control" type="text" name="date_to">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="mt20 col-sm-12">
                        <label class="col-sm-2"> Start Year </label>
                        <div class="col-sm-4">
                            <input placeholder="Start Year" class="form-control" type="number" name="start_year" min="1700" max="<?php echo $currentYear; ?>">
                        </div>

                        <label class="col-sm-2"> End Year </label>
                        <div class="col-sm-4">
                            <input placeholder="End Year" class="form-control" type="number" name="end_year" min="1700" max="<?php echo $currentYear; ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="mt20 col-sm-12">
                        <label class="col-sm-2"> Filling No : </label>
                        <div class="col-sm-4">
                            <input placeholder="Filling No" class="form-control" type="number" name="filling_no" min="1">
                        </div>

                        <label class="col-sm-2"> Filling Year : </label>
                        <div class="col-sm-4">
                            <input placeholder="Filling Year" class="form-control" type="number" name="filling_year" min="1800" max="<?php echo $currentYear; ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="mt10 col-sm-12">
                        <label class="col-sm-2"> Applied By : </label>
                        <div class="col-sm-4">
                            <input placeholder="Applied By" class="form-control" type="text" name="applied_by">
                        </div>

                        <div class="col-sm-2">
                            <input type="submit" class="btn btn-global btn-global-thick" value="Search">
                        </div>
                    </div>
                </div>
            </form>


            <?php if($displayTable) { ?>

                <br><br>

                <button class="btn btn-global btn-global-thin ml10 pull-right" onclick="exportPdf()"> Export Pdf</button>
                <button class="btn btn-global btn-global-thin ml10 pull-right" onclick="exportExcel()"> Export Excel</button>

                <br><br><br><br>
                <div class="visible-block sorted-records-wrapper sorted-records">
                    <div class="table-responsive">
                        <table id="search_order_table" class="table data-tables">
                            <thead>
                            <tr>
                                <th>Order Id</th>
                                <th>Case No.</th>
                                <th>Case Year</th>
                                <th>Document type</th>
                                <th>Document Date</th>
                                <th>Apply Date</th>
                                <th>Status</th>
                                <?php if($reasonColumnShow) { ?>
                                    <th>Reason</th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($searchOrders as $searchOrder) { ?>
                                <tr>
                                    <td><?php echo $searchOrder['order_id'] ?></td>
                                    <td><?php /*echo $searchOrder['case_no'] */?>

                                        <a href="" data-toggle="modal" data-target="#view-detail-modal" data-id="<?php echo $searchOrder['id']; ?>" class="view-detail no-text-decoration" title="View Detail of Order">
                                            <?php echo $searchOrder['case_no'] ?>
                                        </a>
                                    </td>
                                    <td><?php echo $searchOrder['case_year'] ?></td>
                                    <td><?php echo $searchOrder['document_type'] ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($searchOrder['document_date'])) ?></td>
                                    <td><?php echo ($searchOrder['apply_date']) ? date('d-m-Y', strtotime($searchOrder['apply_date'])) : '---'?></td>
                                    <td><?php echo (!$searchOrder['order_status']) ? 'pending' : $searchOrder['order_status']; ?></td>

                                    <?php if($reasonColumnShow) { ?>
                                        <td>
                                            <?php echo ($searchOrder['order_status'] == 'rejected' || $searchOrder['order_status'] == 'lapsed' )
                                                ? (($searchOrder['order_status'] == 'rejected') ? $searchOrder['rejection_reason'] : $searchOrder['lapsed_reason']) : '---'; ?>
                                        </td>
                                    <?php } ?>


                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>

        </div>
        <br clear="all" />
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

<script src="../js/jspdf.debug.js"></script>
<script src="../js/jspdf.plugin.autotable.js"></script>
<script src="../js/faker.min.js"></script>
<script src="../js/tableExport.js"></script>
<script src="../js/jquery.base64.js"></script>


<script>
    $('.apply_date_format').datepicker({
        maxDate: new Date()
    });

    $('.view-detail').click(function () {
        var id = $(this).data('id');
        jQuery.ajax({
            url: '../admin/get-detail.php?id='+id,
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

    function exportExcel() {

        $('#search_order_table').tableExport({type:'excel',escape:'false',title:'<?php echo $tableHeading?>'});
    }

    function exportPdf() {
        var doc = new jsPDF('l');
        var title = '<?php echo $tableHeading; ?>';
        doc.text(title, 14, 16);
        var elem = document.getElementById("search_order_table");
        var res = doc.autoTableHtmlToJson(elem);
        //res.columns.splice(-1,1);
        doc.autoTable(res.columns, res.data, {
            startY: 20,
            margin: {horizontal: 7},
            bodyStyles: {valign: 'top'},
            styles: {overflow: 'linebreak', columnWidth: 'wrap'},
            columnStyles: {text: {columnWidth: 'auto'}}
        });
        doc.output('dataurlnewwindow');
    }
    
</script>
<?php include '../layouts/footer.php' ?>
