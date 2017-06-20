<?php
session_start();
if(!empty($_SESSION['logged_in'])) {
    echo '<script>window.location = "../admin/welcome.php";</script>';
    exit();
}
include "master.php";
?>
<script>
    function sendContact() {
        var valid;
        valid = validateContact();
        if (valid) {
            jQuery.ajax({
                url: "login-post.php",
                data: 'username=' + $("#username").val() + '&password=' + $("#password").val(),
                type: "POST",
                success: function (data) {
                    if (data == 1) {
                        window.location = "../admin/welcome.php";
                    } else {
                        $("#login-status").html(data);
                    }
                },
                error: function () {
                    $("#login-status").html("<div class='alert alert-danger'>Error in Ajax Call</div>");
                }
            });
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
                $("#login-status").html("<div class='alert alert-danger'>Required Parameter is missing.</div>");
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
            <form accept-charset="UTF-8" class="form-horizontal form-login" id="user-login">

                <br>
                <div class="form-group ">
                    <div id="login-status" class="col-sm-12"></div>
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
