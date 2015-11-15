<?php
session_start();
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

if(!isset($_SESSION['user']))
{
    header("Location: index.php");
    die();
}

//include 'functions/fb-api.php';
//include 'functions/islogged.php';
//include 'functions/validate-fb-sub.php';
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
    <?php //include_once 'page_includes/facebook-status.php'; ?>
    <?php include_once 'page_includes/navbar.php'; ?>
  <?php //if($_SESSION['user']){ ?>
    <div class="container">
      <div class="container-fluid">
        <!-- SIDEBAR -->
        <div style="width:30%;float:left;padding:10px;">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Vos abonnements</h3>
            </div>
            <div class="panel-body">
                <p>Abonnez-vous :</p>
            </div>
          </div>
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Vos amis</h3>
            </div>
            <div class="panel-body">
              <div id="my-user-panel" class="row">
              </div>
            </div>
          </div>
        </div>
      	<!-- Main content -->
      	<div style="width:67%;float:left;">
          <!-- stream content -->
          <div style="" class="tab-pane fade in active" id="stream">
            <!-- Stream post form -->
            <div id="send-newpost-form">
              <div style="box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);border:none;" class="panel panel-default" style="border-radius: 5px;">
                <textarea id="newpost-content" style="resize:vertical;border-radius: 5px 5px 0 0;" class="form-control" rows="3" placeholder="Inserez votre message, lien, photo, video, etc..."></textarea>
                <div style="border: 1px solid rgb(221, 221, 221);" class="panel-footer" style="border-bottom-right-radius: 5px;border-bottom-left-radius: 5px;">
                  
                  <div class="btn-group" role="group" aria-label="...">
                      <button id="button-send-newpost" style="font-weight: bold;font-size: 12px;" class="btn btn-info">Publier <span class="glyphicon glyphicon-send"></span></button>
                    <div class="btn-group" role="group">
                      <button id="newpost-visibility-btn" type="button" style="font-size: 12px;" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Amis <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a id="newpost-visibility-public-btn" data-toggle="tooltip" data-delay='{ "show": 500, "hide": 0 }' data-placement="right" data-original-title="Tous les internautes du monde pourront voir cette publication." href="#"><span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Tout le monde</a></li>
                        <li><a id="newpost-visibility-friend-btn" data-toggle="tooltip" data-delay='{ "show": 500, "hide": 0 }' data-placement="right" data-original-title="Seulement vos amis VirtualID pourront voir cette publication." href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Mes amis</a></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Stream posts list -->
            <div id="posts-stream">
            </div>
          </div>
          <!-- Messages content -->
          <div style="visibility: hidden;position:absolute;top:120px;width:53%;text-align:center;" class="tab-pane fade" id="emails">
            <p>Bientôt disponible</p>
          </div>
        </div>
  <?php 
    // } else {
    //include_once 'page_includes/facebook-validation.php';
    //} 
  ?>
    <!-- SCRIPTS -->
    <?php include_once 'page_includes/footer.php'; ?>
    <script type="text/javascript">
      $(document).ready(function() {
        loadPosts();
        changeNewpostVisibility();
        updateNotifications();
        //setInterval(updateNotifications, 60000);
        showMyUserPanel();
        var searchFriendBar = $('#searchFriendBar').magicSuggest({
            allowFreeEntries: false,
            data: 'functions/get-all-users.php',
            valueField: 'id',
            displayField: 'userresult'
        });
        $(searchFriendBar).on('selectionchange', function(e,m){
          showIdentity(this.getValue());
        });
      });
    </script>
    <?php include_once 'page_includes/instant-message-module.php'; ?>
  </body>
</html>
