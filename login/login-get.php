<?php
session_start();
if(!empty($_SESSION['logged_in'])) {
    echo '<script>window.location = "/admin/welcome.php";</script>';
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
                    data: 'username=' + $("#username").val() + '&password=' + $("#password").val() + '&captcha=' + $("#captcha").val(),
                    type: "POST",
                    success: function (data) {
                        if (data == 1) {
                            window.location = "/admin/welcome.php";
                        } else {
                            $("#login-status").html(data);
                        }
                    },
                    error: function () {
                        $("#login-status").html('Error in Ajax Call');
                    }
                });
            }
        }

        function validateContact() {
            var valid = true;
            $(".demoInputBox").css('background-color', '');
            var inputs = ['username', 'password', 'captcha'];
            $("#login-status").html('');


            for (var i = 0; i < inputs.length ; i++) {
                if (!$("#" + inputs[i]).val()) {
                    $("#" + inputs[i]).css('background-color', '#FFFFDF');
                    valid = false;
                    $("#login-status").html('<p class="error">Required Parameter is missing.</p>');
                }
            }
            return valid;
        }
    </script>

<div class="login">
    <div class="box-header">
        <h3 class="login-heading">Log In</h3>
    </div>

    <div class="login-body">
        <form method="POST" action="login-post.php" accept-charset="UTF-8" class="form-horizontal form-login" id="user-login">
            <div class="form-group ">
                <div id="login-status" class="col-sm-12">
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="username">
                        Username
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="username" class="form-control" placeholder="Username" name="username" type="text" value="" required>
                    <span class="error-message"></span>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="password">
                        Password
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="password" class="form-control" placeholder="Password" name="password" type="password" value="" required>
                    <span class="error-message"></span>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="captcha">
                        Captcha
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="captcha" class="form-control" placeholder="Captcha" name="captcha" type="password" value="" required>
                </div>
            </div>

            <?php include "captcha.php" ?>
            <div class="form-group" style="margin-bottom: 40px;">
                <div class="col-sm-12">
                    <input class="btn btn-default submit text-uppercase" onclick="sendContact()" value="Log In">
                </div>
            </div>
        </form>
    </div>
</div>

<?php include "footer.php" ?>
