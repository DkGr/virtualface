<?php
error_reporting(E_ALL ^ E_DEPRECATED);
$connexion = new MongoClient('mongodb://212.129.46.110');
$connexion->admin->authenticate("padman","Padman@M0ng0");
$this->VirtualIDDB = $connexion->VirtualID;
?>
