<?php
session_start();
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once '../vendor/autoload.php';
require_once '../lib/openfire.php';
include_once '../dba/User.php';

if(!isset($_SESSION['_id']))
{
  echo "<script>window.location = 'https://www.octeau.fr/virtualid/'</script>";
}

if (isset($_POST['friendid']) && !empty($_POST['friendid'])) {
  $user = new User();
  $user->setId($_SESSION['_id']);
  $user->AddFriend($_POST['friendid']);
  $friendAdded = new User();
  $friendAdded->setId($_POST['friendid']);
  $OpenfireAPI = new Gidkom\OpenFireRestApi\OpenFireRestApi;

  // Set the required config parameters
  $OpenfireAPI->secret = "m8D6vTN7L0QVwUq4";
  $OpenfireAPI->host = "octeau.fr";
  $OpenfireAPI->port = "9091";  // default 9090

  // Optional parameters (showing default values)
  $OpenfireAPI->useSSL = true;
  $OpenfireAPI->plugin = "/plugins/restapi/v1";  // plugin

  SetFriends($OpenfireAPI, $user->getUsername(), $friendAdded->getUsername());
}
?>
