<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once '../dba/Notifications.php.class';

if(!isset($_SESSION['_id']))
{
  echo "<script>window.location = 'https://www.octeau.fr/virtualid/'</script>";
}

if (isset($_GET['notifid']) && !empty($_GET['notifid'])) {
  $notifToUpdate = new Notification();
  $notifToUpdate->SetNotificationRead($_GET['notifid']);
}
?>
