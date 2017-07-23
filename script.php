<?php

include "layouts/database_access.php";
if($connection) {
    $updateApplyDate = "Update client_order SET apply_date = '2017-05-01' where apply_date is null";
    $result = $connection->query($updateApplyDate);
    echo "Apply Date update for records ";



}
