<?php
session_start();
if(!empty($_SESSION['logged_in'])) {
    echo '<script>window.location = "../admin/welcome.php";</script>';
    exit();
}
$message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
try{
    $userName = trim($_POST['username']);
    $password = trim($_POST['password']);
    if (empty($userName) || empty($password)) {
        $message =  "Required Parameter is missing";
    } else {
        include "../layouts/database_access.php";
        if (!$connection) {
            $message = "Connection Failed.";
        } else {
            $stmt = $connection->prepare('SELECT id, name, username, type FROM users WHERE username = :username AND password = :password');
            $stmt->execute(array('username' => $userName, 'password' => hash('sha512', $password)));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($user)) {
                $message = "User Name or Password is incorrect";
            } else {
                $userType = $user['type'];
                $_SESSION['logged_in'] = $userType;
                $_SESSION['logged_in_user'] = $user;
                if($userType == 'admin') {
                    echo '<script>window.location = "../admin/welcome.php";</script>';
                } else if($userType == 'super-admin') {
                    echo '<script>window.location = "../super-admin/welcome.php";</script>';
                } else if($userType == 'applicant') {
                    echo '<script>window.location = "../applicant/welcome.php";</script>';
                } else {
                    $message = "Error in Login, No required user type.";
                }
            }
        }
    }
}catch (Exception $e) {
    $message = "Error : " . $e->getMessage() . "";
}
}
include "master.php";
?>
<script>
    function sendContact() {
        var valid;
        valid = validateContact();
        if (valid) {;
            $('#user-login').submit();
        }
    }

    function validateContact() {
        var valid = true;
        var inputs = ['username', 'password'];
        $("#login-status").html('');

        for (var i = 0; i < inputs.length ; i++) {
            if (!$("#" + inputs[i]).val()) {
                $("#" + inputs[i]).css('background-color', '#ffe8ea');
                valid = false;
                $("#login-status").html("<p class='alert alert-danger'>Required Parameter is missing.</p>");
            } else {
                $("#" + inputs[i]).css('background-color', '#fff');
            }
        }
        return valid;
    }
</script>
<div class="container">
    <div class="row main">
        <div class="main-login main-center mt35">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" accept-charset="UTF-8" class="form-horizontal form-login" id="user-login">

                <br>
                <div class="form-group ">
                    <div id="login-status" class="col-sm-12">
                        <?php if($message) { ?>
                            <div class="alert alert-danger"><?php echo $message?></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="username" class="cols-sm-2 control-label">Username</label>
                    <div class="cols-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="username" id="username"  placeholder="Enter your Username" required/>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="cols-sm-2 control-label">Password</label>
                    <div class="cols-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                            <input type="password" class="form-control" name="password" id="password"  placeholder="Enter your Password" required/>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="button" onclick="sendContact()" class="btn btn-lg btn-block large-button">Login in</button>
                </div>

            </form>
        </div>
    </div>
</div>
<br><br><br><br>


<?php include "footer.php" ?>
