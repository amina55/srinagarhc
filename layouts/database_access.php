<?php

$dbuser = 'Mac';
$dbpass = 'root';
$dbhost = 'localhost';
$dbname='test';

$connection = new PDO("pgsql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, [PDO::ATTR_PERSISTENT => true]);
?>