<?php
session_start();

try{
    $userName = trim($_POST['username']);
    $password = trim($_POST['password']);
    if (empty($userName) || empty($password)) {
        print "<div class='alert alert-danger'>Required Parameter is missing</div>";
    } else {
        include "../layouts/database_access.php";

        if (!$connection) {
            print "<div class='alert alert-danger'>Connection Failed.</div>";
        } else {
            $stmt = $connection->prepare('SELECT name, username FROM users WHERE username = :username AND password = :password');
            $stmt->execute(array('username' => $userName, 'password' => hash('sha512', $password)));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($user)) {
                echo "<div class='alert alert-danger'>User Name or Password is incorrect</div>";
            } else {
                echo 1;
                $_SESSION['logged_in'] = $userName;
            }
        }
    }

}catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error : " . $e->getMessage() . "</div>";
}

?>