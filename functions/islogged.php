<?php
include_once 'lib/openpgp-helper.php';
include_once 'dba/User.php.class';

$accessToken ='';
$useFacebookConnect = false;
$helper = $fb->getJavaScriptHelper();
$user = '';
try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {

} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".'Facebook SDK returned an error: ' . $e->getMessage());
}

if (!empty($accessToken)) {
  //We are logged with facebook
  $useFacebookConnect = true;
  $_SESSION['facebook_access_token'] = $accessToken;

  // Sets the default fallback access token so we don't have to pass it to each request
  $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);

  try {
    $response = $fb->get('/me?fields=id,name,email,picture');
    $userNode = $response->getGraphUser();

    $user = new User();
    $_SESSION['_id'] = $user->getIdWithFacebookEmail($userNode['email']);
    if(isset($_SESSION['_id']) && !empty($_SESSION['_id']))
    {
      $user->setId($_SESSION['_id']);
    }
    else{
      $keypair = OpenPGP_Helper::GenerateKeyPair();
      $user->createNew($userNode['email'], $userNode['name'], "fb_password", $keypair['privatekey'], $keypair['publickey']);
      $_SESSION['_id'] = $user->getIdWithFacebookEmail($userNode['email']);
      $user->setId($_SESSION['_id']);
      fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".$userNode['name']." s'est inscrit.\n");
    }
  } catch(Facebook\Exceptions\FacebookResponseException $e) {

  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    fwrite(fopen('log.txt', 'a+'), "[".date("d/m/Y H:i:s")." IP:".$_SERVER['REMOTE_ADDR']."] ".'Facebook SDK returned an error: ' . $e->getMessage());
  }
}
else{
  if(isset($_SESSION['_id']))
  {
    $user = new User();
    $user->setId($_SESSION['_id']);
  }
  else{
    header("Location: index.php");
    die();
  }
}
?>
