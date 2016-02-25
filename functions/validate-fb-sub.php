<?php
include_once 'lib/openfire.php';
$fbsuberreur = "";

// on teste si le visiteur a soumis le formulaire de connexion
if (isset($_POST['validate-fb-sub']) && ($_POST['validate-fb-sub'] == 'validate-fb-sub')) {
  if ((isset($_POST['displayname']) && !empty($_POST['displayname'])) && (isset($_POST['username']) && !empty($_POST['username'])) && (isset($_POST['password']) && !empty($_POST['password'])) && (isset($_POST['passwordcheck']) && !empty($_POST['passwordcheck']))) {
    if($_POST['password'] == $_POST['passwordcheck'])
    {
      $pass_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $user->updateUsername($_POST['username']);
      $user->updateDisplayname($_POST['displayname']);
      $user->updateEmail($_POST['email']);
      $user->updatePassword($pass_hash);
      $user->setFacebookLinked();
		  fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$_POST['username']." a validé son compte avec facebook.\n");
      // Create the Openfire Rest api object
      $OpenfireAPI = new Gidkom\OpenFireRestApi\OpenFireRestApi;

      // Set the required config parameters
      $OpenfireAPI->secret = "m8D6vTN7L0QVwUq4";
      $OpenfireAPI->host = "www.octeau.fr";
      $OpenfireAPI->port = "9091";  // default 9090

      // Optional parameters (showing default values)
      $OpenfireAPI->useSSL = false;
      $OpenfireAPI->plugin = "/plugins/restapi/v1";  // plugin

      if(AddOpenfireUser($OpenfireAPI, $_POST['username'], md5($pass_hash), ''))
      {
        fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$_POST['username']." > utilisateur openfire créé.\n");
      }
    }
    else {
      $fbsuberreur = 'Les champs de mot de passe ne concordent pas.';
      fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$fbsuberreur."\n");
    }
  } else {
      $fbsuberreur = 'Au moins un des champs est vide.';
	    fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$fbsuberreur."\n");
  }
}
?>
