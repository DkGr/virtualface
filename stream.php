<?php
session_start();
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

include 'functions/fb-api.php';
include 'functions/islogged.php';
include 'functions/validate-fb-sub.php';
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php include_once "page_includes/header.php" ?>
    <link type="text/css" rel="stylesheet" media="screen" href="css/converse.css" />
    <![if gte IE 9]>
        <script src="js/converse.min.js"></script>
    <![endif]>
  </head>

  <body>
    <?php include_once 'page_includes/facebook-status.php'; ?>
    <?php include_once 'page_includes/navbar.php'; ?>
  <?php if(!$useFacebookConnect || $user->isFacebookLinked()){ ?>
    <div class="container">
      <div class="container-fluid">
        <!-- SIDEBAR -->
        <div style="width:30%;float:left;padding:10px;">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Vos amis</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <?php
                $hisFriends = $user->GetFriends();
                $friendCount = 0;
                foreach ($hisFriends as $keyid => $isFriend) {
                    if($isFriend)
                    {
                      $tmpFriend = new User();
                      $tmpFriend->setId($keyid); ?>
                      <div style="padding-right: 5px;padding-left: 5px;" class="col-lg-3 col-sm-4 col-xs-5">
                        <a href="identity.php?userid=<?php echo $tmpFriend->getId(); ?>">
                          <img data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $tmpFriend->getDisplayname().' ('.$tmpFriend->getUsername().')'; ?>" style="margin-bottom: 0px;" src="avatars/<?php echo $tmpFriend->getId(); ?>" class="thumbnail img-responsive">
                        </a>
                      </div>
                      <?php
                      $friendCount++;
                    }
                }
                if($friendCount == 0){
                  echo '<p style="margin: 15px 15px 10px;">:( Vous n\'avez pas encore d\'amis...</p>';
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      	<!-- Main content -->
      	<div style="width:67%;float:left;">
    		  <!-- stream content -->
      		<div class="tab-pane fade in active" id="stream">
            <!-- Stream post form -->
            <div id="send-newpost-form">
              <input value="<?php echo $user->getId() ?>" id="newpost-userid" type="hidden" >
              <div style="box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);" class="panel panel-default">
                <textarea id="newpost-content" style="resize:vertical;margin:5px; width:99%;" class="form-control" rows="3" placeholder="Inserez votre message, lien, photo, video, etc..."></textarea>
                <div class="panel-footer">
                  <button id="button-send-newpost" class="btn btn-info">Publier</button>
                </div>
              </div>
            </div>

            <!-- Stream posts list -->
            <div id="posts-stream">
            </div>
          </div>
          <!-- Messages content -->
        	<div style="position:absolute;top:120px;width:53%;text-align:center;" class="tab-pane fade" id="messages">
            <p>Bientôt disponible</p>
        	</div>
    </div>
  <?php } else {
    include_once 'page_includes/facebook-validation.php';
  } ?>
	<!-- SCRIPTS -->
    <script type="text/javascript">
      $(document).ready(function() {
        loadPosts();
        updateNotifications();
    	});
    </script>
    <?php include_once 'page_includes/instant-message-module.php'; ?>
  </body>
</html>
