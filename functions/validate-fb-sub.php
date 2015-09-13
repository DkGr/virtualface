<?php
$fbsuberreur = "";

// on teste si le visiteur a soumis le formulaire de connexion
if (isset($_POST['validate-fb-sub']) && ($_POST['validate-fb-sub'] == 'validate-fb-sub')) {
  if ((isset($_POST['username']) && !empty($_POST['username'])) && (isset($_POST['password']) && !empty($_POST['password'])) && (isset($_POST['passwordcheck']) && !empty($_POST['passwordcheck']))) {
    if($_POST['password'] == $_POST['passwordcheck'])
    {
      $user->updateUsername($_POST['username']);
      $user->updateEmail($_POST['email']);
      $user->updatePassword($_POST['password']);
      $user->setFacebookLinked();
		  fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$_POST['username']." a validé son compte avec facebook.\n");
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
