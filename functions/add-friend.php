<?php
session_start();
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once '../dba/User.php.class';

if(!isset($_SESSION['_id']))
{
  echo "<script>window.location = 'https://www.octeau.fr/virtualid/'</script>";
}

if (isset($_POST['friendid']) && !empty($_POST['friendid'])) {
  $user = new User();
  $user->setId($_SESSION['_id']);
  $user->AddFriend($_POST['friendid']);
}
?>
