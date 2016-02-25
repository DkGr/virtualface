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
    <link type="text/css" rel="stylesheet" href="css/cropper.min.css">
  </head>

  <body>
      <input id="myid" value="<?php echo (string)$_SESSION['user']['_id']; ?>" type="hidden" >
    <?php include_once 'page_includes/facebook-status.php'; ?>
    <?php include_once 'page_includes/navbar.php'; ?>
    <div class="container" style="position:relative;top:50px;">
      <div class="container-fluid">
        <!-- SIDEBAR -->
        <div style="width:30%;float:left;padding:10px;">
          <div><img id="avatarImg" src="<?php echo 'avatars/'.$_SESSION['user']['_id']; ?>" alt="no_avatar"></div>
          <br/>
          <form enctype="multipart/form-data">
              <input name="file" type="file" />
              <input type="button" value="Upload" />
          </form>
          <!--<progress></progress>-->
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Mes informations</h3>
            </div>
            <div class="panel-body">
                Nom d'utilisateur : <a id="username"></a>
              <br/>
                Nom d'affichage : <input id="displayname"></input>
              <br/>
                E-mail : <input id="email"></input>
              <br/>
            </div>
          </div>
        </div>
      	<!-- Main content -->
      	<div style="width:77%;">
          <div id="tabContent" class="tab-content">
            <h4>Qui peut ... ?</h4>
            <h5>Voir mon adresse email : </h5>
            <div id="emailPrivacySettings" class="btn-group" data-toggle="buttons"></div>
            <br/>
            <h5>Voir ma liste d'amis : </h5>
            <div id="friendsPrivacySettings" class="btn-group" data-toggle="buttons"></div>
            <br/>
            <h5>Voir mon nom d'affichage : </h5>
            <div id="displaynamePrivacySettings" class="btn-group" data-toggle="buttons"></div>
            <br/>
          </div>
            <br/>
          <div id="map" style="height: 180px;"></div>
        </div>
      </div>
    </div>
    <?php require_once 'page_includes/footer.php'; ?>
    <script type="text/javascript">
        
        /*var map = L.map('map').setView([51.505, -0.09], 13);
        var tiles = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox.streets',
            accessToken: 'pk.eyJ1IjoicGFkbWFuIiwiYSI6ImNpa3RxZnY3ZDAwMTB3OW0wenR2YmxpazUifQ.5QIDU2hWg39a8hbRLAg-5Q'
        }).addTo(map);
        var arcgisOnline = L.esri.Geocoding.arcgisOnlineProvider();
        // create the geocoding control and add it to the map
        var searchControl = L.esri.Geocoding.geosearch({
            providers: [arcgisOnline]
        }).addTo(map);
        // create an empty layer group to store the results and add it to the map
        var results = L.layerGroup().addTo(map);
        // listen for the results event and add every result to the map
        searchControl.on("results", function(data) {
            results.clearLayers();
            for (var i = data.results.length - 1; i >= 0; i--) {
                results.addLayer(L.marker(data.results[i].latlng));
            }
        });*/
        
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
            $('#avatarImg').cropper({
                aspectRatio: 1,
                crop: function(e) {
                }
            });
        }, 500);   
        loadPrivacySettings();
        updateNotifications();
        setInterval(updateNotifications, 30000);
      });
    </script>    
  </body>
  <?php include_once 'page_includes/instant-message-module.php'; ?>
</body>
