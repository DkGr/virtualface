<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once 'dba/User.php.class';
include_once 'dba/Post.php.class';

if(!isset($_SESSION['_id']))
{
  echo "<script>window.location = 'https://www.octeau.fr/virtualid/'</script>";
}
$identity = new User();
if (isset($_POST['postid']) && !empty($_POST['postid'])) {
  $userpost = new Post();
  $userpost->setId($_POST['postid']);
  $identity->setId($userpost->getAuthor());
}
elseif (isset($_POST['userid']) && !empty($_POST['userid'])) {
  $identity->setId($_POST['userid']);
}
else {
  header('Location: index.php');
}
?>
