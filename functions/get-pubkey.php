<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once '../dba/User.php.class';


$json = file_get_contents('php://input');
$obj = json_decode($json);
$keyarray = array();
foreach ($obj->userid as $value) {
  $user = new User();
  $user->setId($value);
  $apubkey = $user->getPublicKey();
  $keyarray[$value] = $apubkey;
}
echo json_encode($arr);
 ?>
