<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once '../dba/Comment.php.class';

if(!isset($_SESSION['_id']))
{
  echo "<script>window.location = 'https://www.octeau.fr/virtualid/'</script>";
}

if ((isset($_POST['postid']) && !empty($_POST['postid'])) && (isset($_POST['userid']) && !empty($_POST['userid'])) && (isset($_POST['content']) && !empty($_POST['content']))) {
  $newcomment = new Comment();
  $newcomment->CreateNew($_POST['postid'], $_POST['userid'], date("Y/m/d H:i:s"), $_POST['content']);
}
?>
