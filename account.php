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

//include 'functions/fb-api.php';
//include 'functions/islogged.php';
//include 'functions/validate-fb-sub.php';
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
    <link rel="stylesheet" href="css/leaflet.css" />
    <script src="js/leaflet.js"></script>
    <!-- Esri Leaflet -->
    <script src="//cdn.jsdelivr.net/leaflet.esri/2.0.0-beta.7/esri-leaflet.js"></script>
    <!-- Esri Leaflet Geocoder -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/leaflet.esri.geocoder/2.0.3/esri-leaflet-geocoder.css">
    <script src="//cdn.jsdelivr.net/leaflet.esri.geocoder/2.0.3/esri-leaflet-geocoder.js"></script>
  </head>

  <body>
      <input id="myid" value="<?php echo (string)$_SESSION['user']['_id']; ?>" type="hidden" >
    <?php include_once 'page_includes/facebook-status.php'; ?>
    <?php include_once 'page_includes/navbar.php'; ?>
    <div class="container" style="position:relative;top:50px;">
      <div class="container-fluid">
      	<!-- Main content -->
      	<div style="width:77%;margin: 0 auto;">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Mes informations</h3>
            </div>
            <div class="panel-body">
              <form class="form-horizontal" style="text-align: center;">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img class="img-thumbnail" src="<?php echo 'avatars/'.$_SESSION['user']['_id']; ?>" alt="no_avatar" style="width:128px;height:128px;background-color:white;margin-top:-5px;margin-right:5px;"></a>
                <div class="form-group">
                    <label for="username" class="col-sm-3 control-label">Nom d'utilisateur :</label><div class="col-sm-9" style="text-align: left;"><p id="username" class="form-control-static"></p></div>
                </div>
                <br/>
                <div class="form-group">
                    <label for="displayname" class="col-sm-3 control-label">Nom d'affichage :</label><div class="col-sm-9"><input id="displayname" class="form-control"></input></div>
                </div>
                <br/>
                <div class="form-group">
                    <label for="email" class="col-sm-3 control-label">E-mail :</label><div class="col-sm-9"><input id="email" class="form-control"></input></div>
                </div>
                <br/>
                <button id="saveAccountInfosBtn" type="button" class="btn btn-default">Valider</button>
              </form>
            </div>
          </div>
          <br/>
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Paramètres de confidentialité</h3>
            </div>
            <div class="panel-body">
              <form class="form-horizontal">
                <div id="tabContent" class="tab-content">
                  <h4>Qui peut voir ... ?</h4>
                  <br/>
                  <div style="margin: 0 auto;">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">mon adresse email : </label>
                        <div id="emailPrivacySettings" class="btn-group" data-toggle="buttons"></div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">ma liste d'amis : </label>
                        <div id="friendsPrivacySettings" class="btn-group" data-toggle="buttons"></div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">mon nom d'affichage : </label>
                        <div id="displaynamePrivacySettings" class="btn-group" data-toggle="buttons"></div>
                    </div>
                  </div>
                  <br/>
                </div>
              </form>
            </div>
          </div>
          <br/>
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Paramètres de chiffrement</h3>
            </div>
            <div class="panel-body">
              <form class="form-horizontal">
                <div class="form-group">
                    <label for="secLevel" class="col-sm-3 control-label">Niveau de sécurité : </label>
                    <div id="secLevel" class="btn-group col-sm-9" data-toggle="buttons">
                        <label class="btn btn-danger"><input type="radio" id="secLvlLow" autocomplete="off" checked>Faible</label>
                        <label class="btn btn-warning active"><input type="radio" id="secLvlModerate" autocomplete="off" checked>Modéré</label>
                        <label class="btn btn-success"><input type="radio" id="secLvlHigh" autocomplete="off" checked>Elevé</label>
                    </div>
                    <label class="col-sm-3 control-label"></label>
                    <!--<div class="alert alert-danger col-sm-9" style="margin-top: 10px;" role="alert">
                        <p><b>Niveau actuel :</b> Faible</p>
                        <p>En sécurité faible, vos messages privés et vos photos ne seront pas chiffrées. Vos informations personnelles resteront chiffrées. Les paramètres de confidentialité ne seront pas impactés.</p>
                    </div>-->
                    <div class="alert alert-warning col-sm-9" style="margin-top: 10px;" role="alert">
                        <p><b>Niveau actuel :</b> Modéré</p>
                        <p>En sécurité modéré, vos informations personnelles et vos messages privés seront chiffrés mais leurs commentaires et vos photos ne le seront pas. Les paramètres de confidentialité ne seront pas impactés.</p>
                    </div>
                    <!--<div class="alert alert-success col-sm-9" style="margin-top: 10px;" role="alert">
                        <p><b>Niveau actuel :</b> Elevé</p>
                        <p>En sécurité élevé, la totalité de vos données seront chiffrées y compris les commentaires de vos messages. Ce qui implique que les personnes devront être amis entre-elles afin de voir tous les commentaires. Les paramètres de confidentialité ne seront pas impactés.</p>
                    </div>-->
                </div>
                <div class="form-group">
                    <label for="pubKey" class="col-sm-3 control-label" for="">Ma clé publique : <a href="javascript: void(0);" data-placement="right" data-toggle="popover" title="Qu'est-ce qu'une clé publique ?" data-content="La clé publique est la clé qui sera utilisée par vos contacts afin de chiffrer leurs messages/informations qui vous seront destinés."><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a></label><div id="pubKey" class="col-sm-9" style="text-align:left;"><textarea style="resize: none;" readonly rows="15" cols="70" id="publicKey" class="form-control-static"></textarea></div>
                </div>
              </form>
            </div>
          </div>
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
        loadPrivacySettings();
        updateNotifications();
        setInterval(updateNotifications, 30000);
      });
    </script>    
  </body>
  <?php include_once 'page_includes/instant-message-module.php'; ?>
</body>
