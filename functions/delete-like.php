<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once '../dba/Like.php.class';

if(!isset($_SESSION['_id']))
{
  echo "<script>window.location = 'https://www.octeau.fr/virtualid/'</script>";
}

if ((isset($_POST['userid']) && !empty($_POST['userid'])) && (isset($_POST['likeid']) && !empty($_POST['likeid']))) {
  $likeToDelete = new Like();
  $likeToDelete->setId($_POST['likeid']);
  if($_POST['userid'] == $likeToDelete->getAuthor())
  {
    $likeToDelete->DeleteLike();
  }
}
?>
