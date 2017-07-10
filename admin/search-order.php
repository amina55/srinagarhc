<?php

include "admin_access.php";
$applyDate = trim($_POST['apply_date']);
header('Location: welcome.php?apply_date='.$applyDate);

?>