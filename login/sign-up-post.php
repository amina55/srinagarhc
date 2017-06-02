<?php
session_start();
try{
    if ($_POST["captcha"] == $_SESSION["captcha_code"]) {
        $name = trim($_REQUEST['name']);
        $username = trim($_REQUEST['username']);
        $email = trim($_REQUEST['email']);
        $password = trim($_REQUEST['password']);
        $confirmPassword = trim($_REQUEST['confirm_password']);

        if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            print "<div class='alert alert-danger'>Required Parameter is missing</div>";
        } else {
            if($password != $confirmPassword) {
                print "<div class='alert alert-danger'>Please verify Password and Confirm Password.</div>";
            } else {
                include "../layouts/database_access.php";
                if (!$connection) {
                    print "<div class='alert alert-danger'>Connection Failed.</div>";
                } else {
                    if( !preg_match('^(?=.*\d)(?=.*?[a-zA-Z])(?=.*?[\W_]).{6,10}$^', $password) || strlen( $password) < 6) {
                        print "<div class='alert alert-danger'>Password length should be 6-20 characters and contain at-least one digit, upper or lowercase letter and at-least one special character.</div>";
                    } else {
                        $repeat = '';
                        $query = "select * from users where username = '$username' or email = '$email'";
                        $users = $connection->query($query);
                        foreach ($users as $user) {
                            if($user['email'] == $email) {
                                $repeat = 'email';
                            } else if ($user['username'] == $username) {
                                $repeat = 'username';
                            }
                        }

                        if(!$repeat) {
                            $password = hash('sha512', $password);
                            $insertQuery = "INSERT INTO users (name, username, email, password) VALUES  ('$name', '$username', '$email', '$password')";
                            $result = $connection->exec($insertQuery);
                            if (!$result) {
                                print "<div class='alert alert-danger'>Error in User Sign up</div>";
                            } else {
                                echo $result;
                            }
                        } else {
                            print "<div class='alert alert-danger'>Username or Email should be unique. Please enter unique ".$repeat."</div>";
                        }
                    }
                }
            }
        }
    } else {
        print "<div class='alert alert-danger'>Enter Correct Captcha Code.</div>";
    }
}catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error : " . $e->getMessage() . "</div>";
}
?>