<?php
session_start();
/*include_once '../dba/User.php.class';

$user = new User();
$erreur = "";*/

unset($_SESSION["user"]);
session_unset();
session_destroy();
?>
