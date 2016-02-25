<?php
include_once dirname(__FILE__).'/../config/config.php';
include_once '/var/www/virtualid/vendor/autoload.php';

function AddOpenfireUser($api, $username, $passhash, $displayName)
{
  // Add a new user to OpenFire and add to a group
  $result = $api->addUser($username, $passhash, $displayName, '', array());

  // Check result if command is succesful
  if($result['status']) {
      return true;
  } else {
      // Something went wrong, probably connection issues
      echo 'Error: ';
      echo $result['message'];
  }
}

function SetFriends($api, $username1, $username2)
{
  $result = $api->addToRoster($username1, $username2.$VIDdomain, $username2, 3);
  // Check result if command is succesful
  if($result['status']) {
      return true;
  } else {
      // Something went wrong, probably connection issues
      echo 'Error: ';
      echo $result['message'];
  }
}

function UpdateOpenfireUser($api, $username, $passhash, $displayName)
{
  // Add a new user to OpenFire and add to a group
  $result = $api->updateUser($username, $passhash, $displayName, '', array());

  // Check result if command is succesful
  if($result['status']) {
      return true;
  } else {
      // Something went wrong, probably connection issues
      echo 'Error: ';
      echo $result['message'];
  }
}
?>
