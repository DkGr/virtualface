<?php
require_once '../lib/openfire.php';
include_once '../dba/User.php.class';

$user = new User();
$suberreur = "";

// on teste si le visiteur a soumis le formulaire de connexion
if (isset($_POST['subscribe']) && ($_POST['subscribe'] == 'subscribe')) {
  $pass_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
  if ($result = $user->createNew($_POST['email'], $_POST['displayname'],$_POST['username'], $pass_hash)) {
    $contentOrFalseOnFailure   = file_get_contents(__DIR__.'/../img/no_avatar.png');
    $filenameOut = __DIR__.'/../avatars/'.$result;
    $byteCountOrFalseOnFailure = file_put_contents($filenameOut, $contentOrFalseOnFailure);

    fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$_POST['username']." s'est inscrit.\n");
    // Create the Openfire Rest api object
    $OpenfireAPI = new Gidkom\OpenFireRestApi\OpenFireRestApi;

    // Set the required config parameters
    $OpenfireAPI->secret = "m8D6vTN7L0QVwUq4";
    $OpenfireAPI->host = "octeau.fr";
    $OpenfireAPI->port = "9091";  // default 9090

    // Optional parameters (showing default values)
    $OpenfireAPI->useSSL = true;
    $OpenfireAPI->plugin = "/plugins/restapi/v1";  // plugin

    if(AddOpenfireUser($OpenfireAPI, $_POST['username'], md5($pass_hash), $_POST['username']))
    {
      fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$_POST['username']." > utilisateur openfire créé.\n");
      echo '<input id="validationcode" type="hidden" value="'.$pass_hash.'"></div>';
    }
  }
  else {
    $suberreur = 'Un compte a déjà été créé avec ce nom d\'utilisateur. ('.$_POST['email'].')';
    echo $suberreur;
	  fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$suberreur."\n");
  }
}
?>
