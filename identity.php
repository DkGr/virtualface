<?php
session_start();
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

include 'functions/fb-api.php';
include 'functions/islogged.php';
include 'functions/validate-fb-sub.php';
include 'functions/get-identity.php';
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <style type="text/css">
      /* add a little bottom space under the images */
      .thumbnail {
        margin-bottom:7px;
      }
    </style>
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
        <div style="width:20%;float:left;padding:10px;">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img class="img-thumbnail" src="<?php echo 'avatars/'.$identity->getId(); ?>" alt="no_avatar" style="width:128px;height:128px;background-color:white;margin-top:-5px;margin-right:5px;"></a>
          <br/><h3><?php echo $user->getDisplayname().' ('.$user->getUsername().')'; ?></h3>
          <?php
          if($user->getId() != $identity->getId())
          {
            if(!$user->IsMyFriend($identity->getId())){
              if($user->IsAskedFriend($identity->getId())){
                echo '<span class="label label-info">Demande d\'ami envoyée</span><br/>';
              }
              elseif ($identity->IsAskedFriend($user->getId())) {
                echo '<span class="label label-warning">Attends votre acceptation</span><br/>';
              }
            } else {
              echo '<span class="label label-success">Ami</span><br/>';
            } ?>
          <div style="margin-top:15px;margin-bottom:15px;" class="btn-group" role="group" aria-label="Identity action group">
            <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></button>
            <?php if(!$user->IsMyFriend($identity->getId())){
                    if($identity->IsAskedFriend($user->getId())){?>
                      <button id="btnAcceptFriend" class="btn btn-default" type="button"><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span> Accepter</button>
                    <?php } elseif (!$user->IsAskedFriend($identity->getId())) { ?>
                      <button id="btnAddFriend" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Ajouter</button>
              <?php }
                  } ?>
          </div>
          <?php } ?>
          <input value="<?php echo $identity->getId(); ?>" id="userid" type="hidden" >
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Ses amis</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <?php
                $hisFriends = $identity->GetFriends();
                foreach ($hisFriends as $keyid => $isFriend) {
                    if($isFriend)
                    {
                      $tmpFriend = new User();
                      $tmpFriend->setId($keyid); ?>
                    <div style="padding-right: 5px;padding-left: 5px;" class="col-lg-4 col-sm-5 col-xs-6">
                      <a href="identity.php?userid=<?php echo $tmpFriend->getId(); ?>">
                        <img data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $tmpFriend->getUsername(); ?>" style="margin-bottom: 0px;" src="<?php echo 'avatars/'.$tmpFriend->getId(); ?>" class="thumbnail img-responsive">
                      </a>
                    </div>
              <?php }
                } ?>
              </div>
              <!--:( <?php echo $identity->getUsername(); ?> n'a pas encore d'amis...-->
            </div>
          </div>
        </div>
      	<!-- Main content -->
      	<div style="width:77%;float:left;">
          <!-- Identity tabs -->
          <ul style="margin-bottom: 15px;margin-left: 5px;" role="tablist" class="nav nav-pills">
            <li role="presentation" class="active"><a id="hisStream" aria-controls="stream" role="tab" data-toggle="tab" href="#stream">Son flux</a></li>
            <li role="presentation"><a id="hisPhotos" aria-controls="photos" role="tab" data-toggle="tab" href="#photos">Ses photos</a></li>
            <li role="presentation"><a id="hisFriends" aria-controls="friends" role="tab" data-toggle="tab" href="#friends">Ses amis</a></li>
          </ul>
          <div id="tabContent" class="tab-content">
      		  <!-- stream content -->
        		<div class="tab-pane fade in active" role="tabpanel" id="stream">
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
          	<div class="tab-pane fade" role="tabpanel" id="messages">
          	</div>
            <!-- Photos gallery -->
            <div style="position:absolute;top:200px;width:53%;" role="tabpanel" class="tab-pane fade" id="photos">
              <div class="row">
                <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                 <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                 <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                 <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                 <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                 <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                 <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                 <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
                <div class="col-lg-3 col-sm-5 col-xs-6">
                    <a href="#">
                         <img src="http://placehold.it/200x200" class="thumbnail img-responsive">
                    </a>
                </div>
              </div>
              <hr>
              <br><br>
            </div>
            <!-- Photos gallery -->
            <div style="position:absolute;top:200px;width:53%;" role="tabpanel" class="tab-pane fade" id="friends">
            </div>
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
