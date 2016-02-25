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
    <link type="text/css" rel="stylesheet" media="screen" href="css/converse.css" />
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
          <!-- Messages content -->
          <div id="emails">
              <p style="text-align: center">Bientôt disponible</p>
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
        $(document).ready(function() {
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
      });
    </script>    
  </body>
  <?php include_once 'page_includes/instant-message-module.php'; ?>
</html>
