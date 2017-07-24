<?php

include "layouts/database_access.php";
if($connection) {
    $updateApplyDate = "Update client_order SET apply_date = '2017-05-01' where apply_date is null";
    $result = $connection->query($updateApplyDate);
    echo "Apply Date update for records <br>";

    $getOrdersQuery = "select id, case_no, case_year, case_type from client_order";
    $statement = $connection->prepare($getOrdersQuery);
    $statement->execute();
    $allOrders = $statement->fetchAll();

    foreach ($allOrders as $order) {
        $query = "select cino, reg_no, reg_year, regcase_type from civil_t where fil_no = ".$order['case_no']." and fil_year = ".$order['case_year'].
            " and filcase_type = ".$order['case_type'];

        $statement = $connection->prepare($query);
        $statement->execute();
        $result = $statement->fetch();

        if(empty($result)) {
            $query = "select cino, reg_no, reg_year, regcase_type from civil_t_a where fil_no = ".$order['case_no']." and fil_year = ".$order['case_year'].
                " and filcase_type = ".$order['case_type'];

            $statement = $connection->prepare($query);
            $statement->execute();
            $result = $statement->fetch();
        }

        if($result && $result['reg_no'] && $result['reg_year'] && $result['regcase_type'] ) {

            $query = "update client_order set case_no = ".$result['reg_no'].", case_year = ".$result['reg_year'].
                " , case_type = ".$result['regcase_type']. " , cino = '".$result['cino']."' where id = ".$order['id'];

            $statement = $connection->prepare($query);
            $statement->execute();
            $result = $statement->fetch();
        }
    }

    echo "CINO update for old records <br>";
    echo "FIL_NO-> reg_no , fil_year -> reg_year and case also change for old records <br><br><br> Note : This is one time script, dont run again";

}
