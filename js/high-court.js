/**
 * Created by Mac on 03/05/2017.
 */

$(document).ready(function() {
    $('.data-tables').DataTable({
        'aoColumnDefs': [{
        }]
    });

    $('.date-format').datepicker({
        minDate: new Date()
    });
});
