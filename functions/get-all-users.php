<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once '../dba/User.php.class';

/*if(!isset($_SESSION['_id']))
{
  echo "<script>window.location = 'https://www.octeau.fr/virtualid/'</script>";
}*/

$tmp = new User();
$cursor = $tmp->GetAllUsers();
$cursor->sort(array('infos.username' => 1));
$userArray = array();
if($cursor->hasNext())
{
  foreach ( $cursor as $currentUser )
  {
    $userArray[] = array("id" => (string)$currentUser['_id'], "userresult" => $currentUser['infos']['displayname'].' ('.$currentUser['infos']['username'].')');
  }
}
echo json_encode($userArray);
?>
