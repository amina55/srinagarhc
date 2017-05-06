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
            print "<p class='error'>Required Parameter is missing</p>";
        } else {
            if($password != $confirmPassword) {
                print "<p class='error'>Please verify Password and Confirm Password.</p>";
            } else {
                include "../layouts/database_access.php";
                if (!$connection) {
                    print "<p class='error'><p class='error'>Connection Failed.</p>";
                } else {
                    if( !preg_match('^(?=.*\d)(?=.*?[a-zA-Z])(?=.*?[\W_]).{6,10}$^', $password) || strlen( $password) < 6) {
                        print "<p class='error'>Password length should be 6-20 characters and contain at-least one digit, upper or lowercase letter and at-least one special character.</p>";
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
                                print "<p class='error'>Error in User Sign up</p>";
                            } else {
                                echo $result;
                            }
                        } else {
                            print "<p class='error'>Username or Email should be unique. Please enter unique ".$repeat."</p>";
                        }
                    }
                }
            }
        }
    } else {
        print "<p class='error'>Enter Correct Captcha Code.</p>";
    }
}catch (Exception $e) {
    echo "<p class='error'>Error : " . $e->getMessage() . "</p>";
}
?>