<?php
include_once 'dba/User.php.class';

$user = new User();
$suberreur = "";

// on teste si le visiteur a soumis le formulaire de connexion
if (isset($_POST['subscribe']) && ($_POST['subscribe'] == 'subscribe')) {
  if ((isset($_POST['username']) && !empty($_POST['username'])) && (isset($_POST['password']) && !empty($_POST['password'])) && (isset($_POST['passwordcheck']) && !empty($_POST['passwordcheck']))) {
    if($_POST['password'] == $_POST['passwordcheck'])
    {
      if ($user->createNew($_POST['email'], $_POST['username'], $_POST['password'])) {
  		  fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$_POST['username']." s'est inscrit.\n");
        header("Location: stream.php");
      }
      else {
        $suberreur = 'Un compte a déjà été créé avec cette adresse email. ('.$_POST['email'].')';
  		  fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$suberreur."\n");
      }
    }
    else {
      $suberreur = 'Les champs de mot de passe ne concordent pas.';
      fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$suberreur."\n");
    }
  } else {
      $suberreur = 'Au moins un des champs est vide.';
	    fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$suberreur."\n");
  }
}
?>
