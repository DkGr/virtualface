<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once dirname(__FILE__).'/../config/config.php';
$connexion = new MongoClient($GLOBALS['database_url']);
if($GLOBALS['database_username'] != '')
{
    $connexion->admin->authenticate($GLOBALS['database_username'],$GLOBALS['database_password']);
}
$this->VirtualIDDB = $connexion->VirtualID;
?>
