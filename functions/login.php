<?php
include_once 'dba/User.php.class';

$user = new User();
$erreur = "";
$_SESSION['_id'] = '';
// on teste si le visiteur a soumis le formulaire de connexion
if (isset($_POST['login']) && ($_POST['login'] == 'login')) {
  if ((isset($_POST['email']) && !empty($_POST['email'])) && (isset($_POST['password']) && !empty($_POST['password']))) {
    if ($user->loginUser($_POST['email'], $_POST['password'])) {
      $_SESSION['_id'] = $user->id;
		  fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$_POST['email']." s'est connecté.\n");
      header("Location: stream.php");
    }
    else {
      $erreur = 'Compte ou mot de passe non reconnu. <br/><a href="send_reinit.php">Mot de passe oublié ?</a>';
		  fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$erreur."\n");
    }
  } else {
      $erreur = 'Au moins un des champs est vide.';
	    fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$erreur."\n");
  }
}
?>
