<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once dirname(__FILE__).'/../dba/Notifications.php.class';

if (isset($_POST['userid']) && !empty($_POST['userid']))
{
  $notif = new Notification();
  $cursor = $notif->GetUserNotifications($_POST['userid']);
  $cursor->sort(array('date' => -1));
  $notifContent = '<div style="height:200px;overflow-y:scroll;border: 1px solid #DDD;">';
  $notifCount = 0;
  if($cursor->hasNext())
  {
    foreach ( $cursor as $currentNotif )
    {
      if($currentNotif['read'])
      {
        $notifContent = $notifContent.'<div class="list-group-item">'.$currentNotif['content'].'</div>';
      }
      else {
        $notifContent = $notifContent.'<div class="list-group-item">'.$currentNotif['content'].' <a style="float:right;" href="javascript:void(0)" onclick="setNotifRead(\''.$currentNotif['_id'].'\')"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></div>';
        $notifCount++;
      }
    }
    $notifContent = $notifContent.'</div>';
    if($notifCount > 0)
    {
      echo '<a tabindex="0" class="btn" role="button" data-toggle="popover" style="width: 250px;" data-content="'
      .htmlentities('<p style="text-align: center;"><a>Tout marquer comme lu</a></p>'.$notifContent).'">Notifications <span class="badge">'.$notifCount.'</span></a>';
    }
    else {
      echo '<a tabindex="0" class="btn" role="button" data-toggle="popover" style="width: 250px;" data-content="'
      .htmlentities($notifContent).'">Notifications</a>';
    }
  }
  else {
    $notifContent = 'Il n\'y a rien à afficher ici pour l\'instant';
    echo '<a tabindex="0" class="btn" role="button" data-toggle="popover" style="width: 250px;" data-content="'
    .$notifContent.'">Notifications</a>';
  }
}
?>
