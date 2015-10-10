<?php
session_start();
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once '../dba/Post.php.class';
include_once '../dba/User.php.class';

if(!isset($_SESSION['_id']))
{
  echo "<script>window.location = 'https://www.octeau.fr/virtualid/'</script>";
}

function humanTiming ($time)
{

    $time = time() - $time; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => 'an',
        2592000 => 'mois',
        604800 => 'semaine',
        86400 => 'jour',
        3600 => 'heure',
        60 => 'minute',
        1 => 'seconde'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        $finalStr = "";
        if($text == "mois")
        {
          $finalStr = $numberOfUnits.' '.$text;
        }
        else{
          $finalStr = $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
        return $finalStr;
    }

}

if (isset($_POST['userid']) && !empty($_POST['userid'])) {
  $post = new Post();
  $cursor = $post->GetUserPosts($_POST['userid']);
  $cursor->sort(array('date' => -1));
  if($cursor->hasNext())
  {
    foreach ( $cursor as $currentPost )
    {
      $author = new User();
      $author->setId(new MongoId($currentPost['author']));
      $authorActionButton = '';
      $posttime = strtotime($currentPost['date']);
      $postDateStr = humanTiming($posttime);
      if($_SESSION["_id"] == $author->getId())
      {
        $authorActionButton = '<!-- Split button -->
          <div style="display:table-cell;vertical-align: top;" class="btn-group">
            <button style="float:right;" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="caret"></span>
              <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul style="top: 0;left: -165px;" class="dropdown-menu">
              <li><a href="#">Modifier</a></li>
              <li><a href="javascript:void(0)" onclick="deletePost(\''.$currentPost['_id'].'\');">Supprimer</a></li>
            </ul>
          </div>';
      }

      echo '<div style="box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);" class="panel panel-default">
        <div class="panel-body">
          <div class="media">
            <div style="text-align:center;" class="media-left">
              <a href="javascript:void(0)" onclick="showIdentityFromPost(\''.$currentPost['_id'].'\');">
                <img class="media-object img-rounded" style="width: 64px; height: 64px;" src="avatars/'.$author->getId().'" alt="...">
              </a>
              <a href="javascript:void(0)" onclick="showIdentityFromPost(\''.$currentPost['_id'].'\');">'.$author->getDisplayname().'</a>
            </div>
            <div class="media-body">'
            .nl2br(preg_replace('$(\s|^)(https?://[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', $currentPost['content']." ")).
            '</div>'.$authorActionButton.'
            <div style="float: left;margin: 10px 0 0 75px;">0 <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span style="color:grey;">Posté il y a environ '.$postDateStr.'</span></div>
          </div>
        </div>
        <div class="panel-footer">
          <a>0 commentaire</a> - <a>J\'aime</a>
        </div>
        <!--<ul class="list-group">
          <li class="list-group-item">
            <div style="margin-left: 25px;" class="media">
              <div style="text-align:center;" class="media-left">
                <a href="#">
                  <img class="media-object img-rounded" style="width: 32px; height: 32px; margin: auto;" src="img/no_avatar.png" alt="...">
                </a>
                <a>Padman</a>
              </div>
              <div class="media-body">
                Post de test ! Woohoo !

              </div>
              <div style="float: left;margin: 10px 0 0 75px;"><a>J\'aime</a> - 0 <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span style="color:grey;">Posté il y a environ 2 minutes</span></div>
            </div>
          </li>
        </ul>-->
      </div>';
    }
  }
  else {
    echo '<p style="text-align:center;">Il n\'y a rien à afficher ici pour l\'instant</p>';
  }
}
?>
