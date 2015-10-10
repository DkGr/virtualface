<?php
session_start();
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
include_once '../dba/Post.php.class';
include_once '../dba/Comment.php.class';
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
      $commentCount = 0;
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

      $comment = new Comment();
      $commentcursor = $comment->GetAllPostComments((string)$currentPost['_id']);
      $commentcursor->sort(array('date' => 1));
      $commentlist = '';
      $commentpair = false;
      if($commentcursor->hasNext())
      {
        $commentlist = '<ul class="list-group">';
        foreach ( $commentcursor as $currentComment )
        {
          $commentpair = !$commentpair;
          $commentpaircolor = 'style="background-color: rgba(53, 53, 53, 0.03);"';
          if($commentpair)
          {
            $commentpaircolor = 'style="background-color: rgba(53, 53, 53, 0.01);"';
          }
          $commentauthor = new User();
          $commentauthor->setId($currentComment['author']);
          $commentAuthorActionButton = '';
          $commentPosttime = strtotime($currentComment['date']);
          $commentPostDateStr = humanTiming($commentPosttime);
          if($_SESSION["_id"] == $commentauthor->getId())
          {
            $commentAuthorActionButton = '<!-- Split button -->
              <div style="display:table-cell;vertical-align: top;" class="btn-group">
                <button style="float:right;" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="caret"></span>
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul style="top: 0;left: -165px;" class="dropdown-menu">
                  <li><a href="#">Modifier</a></li>
                  <li><a href="javascript:void(0)" onclick="deleteComment(\''.$currentComment['_id'].'\');">Supprimer</a></li>
                </ul>
              </div>';
          }
          $commentlist = $commentlist.'<li '.$commentpaircolor.' class="list-group-item">
                                        <div style="margin-left: 25px;" class="media">
                                          <div style="text-align:center;" class="media-left">
                                            <a href="identity.php?userid='.$commentauthor->getId().'">
                                              <img class="media-object img-rounded" style="width: 32px; height: 32px; margin: auto;" src="avatars/'.$commentauthor->getId().'" alt="...">
                                            </a>
                                            <a href="identity.php?userid='.$commentauthor->getId().'">'.$commentauthor->getDisplayname().'</a>
                                          </div>
                                          <div class="media-body">'
                                          .nl2br(preg_replace('$(\s|^)(https?://[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', $currentComment['content']." ")).
                                          '</div>'.$commentAuthorActionButton.'
                                          <div style="float: left;margin: 10px 0 0 75px;"><a>J\'aime</a> - 0 <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span style="color:grey;">Posté il y a environ '.$commentPostDateStr.'</span></div>
                                        </div>
                                      </li>';
          $commentCount++;
        }

        $commentlist = $commentlist.'</ul>';
      }

      $commentLinkText = 'Aucuns commentaires';
      if($commentCount == 1)
      {
        $commentLinkText = '1 commentaire';
      }
      elseif ($commentCount > 1) {
        $commentLinkText = $commentCount.' commentaires';
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
          <a role="button" data-toggle="collapse" href="#comments-scroll-'.$currentPost['_id'].'" aria-expanded="false" aria-controls="comments-scroll-'.$currentPost['_id'].'">'.$commentLinkText.'</a> - <a>J\'aime</a>
        </div>
        <div class="collapse" id="comments-scroll-'.$currentPost['_id'].'">'
          .$commentlist.
          '<div style="border: none; margin-bottom: 0px;box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);" class="panel panel-default">
            <textarea id="newcomment-content-'.$currentPost['_id'].'" style="resize:vertical;margin:5px; width:99%;" class="form-control" rows="3" placeholder="Inserez votre commentaire..."></textarea>
            <div class="panel-footer">
              <button class="button-send-newcomment" value="'.$currentPost['_id'].'" class="btn btn-info">Poster</button>
            </div>
          </div>
        </div>
      </div>';
    }
  }
  else {
    echo '<p style="text-align:center;">Il n\'y a rien à afficher ici pour l\'instant</p>';
  }
}
?>
