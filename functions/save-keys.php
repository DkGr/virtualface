<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once '../dba/User.php.class';


$json = file_get_contents('php://input');
$obj = json_decode($json);
if ((isset($obj->{'username'}) && !empty($obj->{'username'})) && (isset($obj->{'code'}) && !empty($obj->{'code'})) && (isset($obj->{'privkey'}) && !empty($obj->{'privkey'})) && (isset($obj->{'pubkey'}) && !empty($obj->{'pubkey'}))) {
  $user = new User();
  $user->saveKeys($obj->{'username'}, $obj->{'code'}, $obj->{'privkey'}, $obj->{'pubkey'});
}
 ?>
