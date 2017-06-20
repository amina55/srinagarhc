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
            $("#signup-status").html('');

            var password = $("#password").val();
            var confirm_password = $("#confirm_password").val();
            var password_length = password.length;
            console.log("password length "+password_length);
            var inputs = ['username', 'name', 'email', 'password', 'confirm_password', 'captcha'];
            for (var i = 0 ; i < inputs.length ; i++ ) {
                if (!$("#"+inputs[i]).val()) {
                    $("#"+inputs[i]).css('background-color', '#ffe8ea');
                    $("#signup-status").html('<div class="alert alert-danger">Required Parameter is missing.</div>');
                    valid = false;
                } else {
                    $("#" + inputs[i]).css('background-color', '#fff');
                }
            }
            if(valid) {
                if(confirm_password != password) {
                    $("#confirm_password").css('background-color', '#ffe8ea');
                    $("#password").css('background-color', '#ffe8ea');
                    $("#signup-status").html('<div class="alert alert-danger">Please verify Password and Confirm Password.</div>');
                    valid = false;
                } else if (password_length < 6 || password_length > 20) {
                    $("#password").css('background-color', '#ffe8ea');
                    $('#password-error').html('Password should be atleast 6 character');
                    $("#signup-status").html('<div class="alert alert-danger">Password length should be 6-20 characters.</div>');
                    valid = false;
                }
            }

            return valid;
        }
    </script>

    <div class="container">
        <div class="row main">
            <div class="main-login main-center">
                <form accept-charset="UTF-8" class="form-horizontal form-login" id="user-login">

                    <div class="form-group ">
                        <div id="signup-status" class="col-sm-12"></div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="cols-sm-2 control-label">Your Name</label>
                        <div class="cols-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
                                <input type="text" class="form-control" name="name" id="name"  placeholder="Enter your Name" required/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="cols-sm-2 control-label">Your Email</label>
                        <div class="cols-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
                                <input type="text" class="form-control" name="email" id="email"  placeholder="Enter your Email" required/>
                            </div>
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
                        <label for="confirm_password" class="cols-sm-2 control-label">Confirm Password</label>
                        <div class="cols-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password"  placeholder="Confirm your Password" required/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="captcha" class="cols-sm-2 control-label">Captcha</label>
                        <div class="cols-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-check fa-lg" aria-hidden="true"></i></span>
                                <input type="text" class="form-control" name="captcha" id="captcha"  placeholder="Enter Captcha" required/>
                            </div>
                        </div>
                    </div>

                    <?php include "captcha.php" ?>

                    <div class="form-group ">
                        <button type="button" onclick="sendContact()" class="btn btn-lg btn-block large-button">Register</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
<?php include "footer.php" ?>