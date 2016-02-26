<?php
error_reporting(E_ALL ^ E_DEPRECATED);
$connexion = new MongoClient();
$this->VirtualIDDB = $connexion->VirtualID;
?>
