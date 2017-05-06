<?php


try{
    session_start();
    if ($_POST["captcha"] == $_SESSION["captcha_code"]) {
        $userName = trim($_POST['username']);
        $password = trim($_POST['password']);
        if (empty($userName) || empty($password)) {
            print "<p class='error'>Required Parameter is missing</p>";
        } else {
            include "../layouts/database_access.php";

            if (!$connection) {
                print "<p class='error'>Connection Failed.</p>";
            } else {
                $stmt = $connection->prepare('SELECT name, username FROM users WHERE username = :username AND password = :password');
                $stmt->execute(['username' => $userName, 'password' => hash('sha512', $password)]);
                $user = $stmt->fetch();
                if (empty($user)) {
                    echo "<p class='error'>User Name or Password is incorrect</p>";
                } else {
                    echo 1;
                    $_SESSION['logged_in'] = $userName;
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