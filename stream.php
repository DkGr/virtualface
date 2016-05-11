<?php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

if(!isset($_SESSION['user']))
{
    header("Location: index.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php include_once "page_includes/header.php" ?>
    <link type="text/css" rel="stylesheet" media="screen" href="css/converse.min.css" />
    <title>VirtualID - Votre flux d'actualités</title>
  </head>

  <body>
    <input id="myid" value="<?php echo (string)$_SESSION['user']['_id']; ?>" type="hidden" >
    <?php include_once 'page_includes/facebook-status.php'; ?>
    <?php include_once 'page_includes/navbar.php'; ?>
    <div class="container" style="position:relative;top:50px;">
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
          <div id="stream">
            <!-- Stream post form -->
            <div id="send-newpost-form">
              <div style="box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);border:none;" class="panel panel-default" style="border-radius: 5px;">
                <textarea id="newpost-content" style="resize:vertical;border-radius: 5px 5px 0 0;" class="form-control" rows="3" placeholder="Inserez votre message, lien, photo, video, etc..."></textarea>
                <div id=postPreview></div>
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
            <div id="loadingAnimation"><canvas id="c"></canvas></div>
          </div>
        </div>
    <!-- SCRIPTS -->
<!--    <div class="modal fade" id="passcryptModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                    <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h2 class="modal-title" id="myModalLabel">Décryptage de la session</h2>
                    </div>
                    <div class="modal-body">
                            <div>
                                <p id="errormessage" style="color:red;"></p>
                                <p>Entrez votre mot de passe afin de déchiffrer les informations privées de votre session.
                                    Si vous refusez, vous ne pourrez pas utiliser la messagerie instantanée et vous ne pourrez voir que les messages publics.
                                </p>
                                <form class="form-signup" role="form" onsubmit="return false;">
                                    <input id="passcrypt" name="passcrypt" type="passcrypt" class="form-control" placeholder="Mot de passe" required>
                                    <button id="save-passcrypt" name="save-passcrypt" value="save-passcrypt" class="btn btn-lg btn-primary btn-block" style="margin-top:10px;" type="submit">Déchiffrer</button>
                                </form>
                            </div>
                    </div>
            </div>
            </div>
    </div>-->
    <?php require_once 'page_includes/footer.php'; ?>
    <script type="text/javascript">
        //now some variables for canvas and math
        var canvas = document.getElementById('c');
        var context = canvas.getContext('2d');
        var x = canvas.width / 2; //the center on X axis
        var y = canvas.height / 2; //the center on Y axis

        $(document).ready(function() {
        showLoadingAnimation( distanceArrows, arrowStrength );
        setTimeout(function(){
            var searchFriendBar = $('#searchFriendBar').magicSuggest({
                allowFreeEntries: false,
                data: 'functions/get-all-users.php',
                valueField: 'id',
                displayField: 'userresult'
            });
            $(searchFriendBar).on('selectionchange', function(e,m){
              showIdentity(this.getValue());
            });
            $('[data-toggle="tooltip"]').tooltip();
        }, 500);
        loadPosts();
        changeNewpostVisibility();
        updateNotifications();
        setInterval(updateNotifications, 30000);
        showMyFriendsPanel();
        var win = $(window);

      	// Each time the user scrolls
      	win.scroll(function() {
      		// End of the document reached?
          if(!endPostStream && !loadingPost)
          {
        		if ($(document).height() - win.height() == win.scrollTop()) {
        			$('#loadingAnimation').show();
              loadOlderPosts();
        		}
          }
      	});
      });
    </script>
  </body>
  <?php
  include_once 'page_includes/instant-message-module.php';
  ?>
</html>
