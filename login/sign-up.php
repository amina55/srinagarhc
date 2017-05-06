<?php include "master.php" ?>

<script>
        function sendContact() {
            var valid;
            valid = validateContact();
            if (valid) {
                jQuery.ajax({
                    url: "sign-up-post.php",
                    data: 'name=' + $("#name").val() + '&username=' + $("#username").val() + '&email=' + $("#email").val()
                    + '&password=' + $("#password").val() + '&confirm_password=' + $("#confirm_password").val() + '&captcha=' + $("#captcha").val(),
                    type: "POST",
                    success: function (data) {
                        if (data == 1) {
                            window.location = "login-get.php";
                        } else {
                            $("#signup-status").html(data);
                        }
                    },
                    error: function () {
                        $("#signup-status").html('Error in Ajax Call');
                    }
                });
            }
        }

        function validateContact() {
            var valid = true;
            $(".demoInputBox").css('background-color', '');
            $("#signup-status").html('');

            var password = $("#password").val();
            var confirm_password = $("#confirm_password").val();
            var password_length = password.length;
            console.log("password length "+password_length);
            var inputs = ['username', 'name', 'email', 'password', 'confirm_password', 'captcha'];
            for (var i = 0 ; i < inputs.length ; i++ ) {
                if (!$("#"+inputs[i]).val()) {
                    $("#"+inputs[i]).css('background-color', '#FFFFDF');
                    $("#signup-status").html('<p class="error">Required Parameter is missing.</p>');
                    valid = false;
                }
            }
            if(confirm_password != password) {
                $("#confirm_password").css('background-color', '#FFFFDF');
                $("#password").css('background-color', '#FFFFDF');
                $("#signup-status").html('<p class="error">Please verify Password and Confirm Password.</p>');
                valid = false;
            } else if (password_length < 6 || password_length > 20) {
                $("#password").css('background-color', '#FFFFDF');
                $('#password-error').html('Password should be atleast 6 character');
                $("#signup-status").html('<p class="error">Password length should be 6-20 characters.</p>');
                valid = false;
            }
            return valid;
        }
    </script>

<div class="login">
    <div class="box-header">
        <h3 class="login-heading">Sign Up</h3>
    </div>

    <div class="login-body">
        <form accept-charset="UTF-8" class="form-horizontal form-login" id="user-login">
            <div class="form-group ">
                <div id="signup-status" class="col-sm-12s">
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="name">
                        Name
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="name" class="form-control" placeholder="Name" name="name" type="text" value="" required>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="username">
                        Username
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="username" class="form-control" placeholder="Username" name="username" type="text" value="" required>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="email">
                        Email
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="email" class="form-control" placeholder="Email" name="email" type="email" value="" required>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="password">
                        Password
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="password" class="form-control" placeholder="Password" name="password" type="password" value="" required>
                    <span class="error-message" id="password-error"> </span>

                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="confirm_password">
                        Confirm Password
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="confirm_password" class="form-control" placeholder="Confirm Password" name="confirm_password" type="password" value="" required>
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
                    <input class="btn btn-default submit text-uppercase" type="button" onclick="sendContact()" value="Sign Up">
                </div>
            </div>
        </form>
    </div>
</div>
    <?php include "footer.php" ?>
