<?php
include "admin_access.php";
include "../layouts/master.php";
include "../layouts/database_access.php";
$users = array();
if (!$connection) {
    $message = "Connection Failed.";
} else {
    $userId = $_SESSION['logged_in_user']['id'];
    $query = "select * from users where type = 'admin'";
    $statement = $connection->prepare($query);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="box">
    <a href="../login/sign-up.php?type=admin" class="btn btn-global btn-global-thick pull-right"> Create New Admin </a>

    <!------------------------------ Page Header -------------------------------->
    <div class="box-header">
       <h2>List of Admin</h2>
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
            <div class="visible-block sorted-records-wrapper sorted-records">
                <div class="table-responsive">
                    <table class="table data-tables">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Useranme</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($users as $user) { ?>
                            <tr>
                                <td><?php echo $user['name'] ?></td>
                                <td><?php echo $user['username'] ?></td>
                                <td><?php echo $user['email'] ?></td>
                                <td>
                                    <a title="Reset Admin Password" class="no-text-decoration"><fa class="fa fa-2x fa-lock"></fa></a>
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
</div>

<?php include '../layouts/footer.php' ?>
