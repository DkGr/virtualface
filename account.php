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
    <style type="text/css">
      /* add a little bottom space under the images */
      .thumbnail {
        margin-bottom:7px;
      }
    </style>
    <?php include_once "page_includes/header.php" ?>
    <link type="text/css" rel="stylesheet" media="screen" href="css/converse.css" />
    <link href="css/cropper.min.css" rel="stylesheet">
    <link href="css/uploadfile.css" rel="stylesheet">
  </head>

  <body>
      <input id="myid" value="<?php echo (string)$_SESSION['user']['_id']; ?>" type="hidden" >
      <script>
      var linkedToFacebook = true;
      var connectedWithFacebook = false;
      var accessToken;
      var fbUID;
      // This is called with the results from from FB.getLoginStatus().
      function statusChangeCallback(response) {
        // The response object is returned with a status field that lets the
        // app know the current login status of the person.
        // Full docs on the response object can be found in the documentation
        // for FB.getLoginStatus().
        if (response.status === 'connected') {
          // Logged into your app and Facebook.
          connectedWithFacebook = true;
          fbUID = response.authResponse.userID;
          accessToken = response.authResponse.accessToken;
          if(!linkedToFacebook)
          {
            linktofb(fbUID);
          }
          console.log("Connecté avec Facebook");
        } else if (response.status === 'not_authorized') {
          // The person is logged into Facebook, but not your app.
          connectedWithFacebook = false;
          console.log("Connecté à Facebook sans liaison activée");
          <?php if(!isset($_SESSION['user'])){ ?>
              window.location = "index.php";
          <?php } ?>
        } else {
          // The person is not logged into Facebook, so we're not sure if
          // they are logged into this app or not.
          connectedWithFacebook = false;
          console.log("Déconnecté de Facebook");
        }
      }

      // This function is called when someone finishes with the Login
      // Button.  See the onlogin handler attached to it in the sample
      // code below.
      function checkLoginState() {
        FB.getLoginStatus(function(response) {
          statusChangeCallback(response);
        });
      }

      function revocateFacebookLink(){
        FB.api('/'+fbUID+'/permissions', 'delete', { access_token : accessToken }, function(response) {
          if (!response || response.error) {
            alert('Impossible de supprimer le lien');
          } else {
            unlinktofb();
          }
        });
      }

      window.fbAsyncInit = function() {
      FB.init({
        appId      : '<?php echo $GLOBALS['facebook_app_id']; ?>',
        cookie     : true,  // enable cookies to allow the server to access
                            // the session
        xfbml      : true,  // parse social plugins on this page
        version    : 'v2.6'
      });

      // Now that we've initialized the JavaScript SDK, we call
      // FB.getLoginStatus().  This function gets the state of the
      // person visiting this page and can return one of three states to
      // the callback you provide.  They can be:
      //
      // 1. Logged into your app ('connected')
      // 2. Logged into Facebook, but not your app ('not_authorized')
      // 3. Not logged into Facebook and can't tell if they are logged into
      //    your app or not.
      //
      // These three cases are handled in the callback function.

      FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
      });

      };

      // Load the SDK asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/fr_FR/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
      </script>
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
                  <a role="button" data-toggle="collapse" href="#singleupload" aria-expanded="false" aria-controls="singleupload"><img id="current-avatar" class="img-thumbnail" src="<?php echo 'avatars/'.$_SESSION['user']['_id']."?".date("YmdHis"); ?>" alt="no_avatar" style="width:128px;height:128px;background-color:white;margin-top:-5px;margin-right:5px;"></a>
                <div class="collapse" id="singleupload">Upload</div>
                <div id="username-form" class="form-group">
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
              <h3 class="panel-title">Paramètres Facebook</h3>
            </div>
            <div class="panel-body">
              <form class="form-horizontal">
                <div class="form-group">
                    <label for="fblinked" class="col-sm-3 control-label">Compte lié à Facebook :</label><div class="col-sm-9" style="text-align: left;"><p id="fblinked" class="form-control-static"></p></div>
                </div>
                <br/>
                <div class="form-group">
                    <label for="fbconnexion" class="col-sm-3 control-label">Connexion à mon compte Facebook :</label><div class="col-sm-9"><fb:login-button id="fbconnexion" data-max-rows="1" data-size="xlarge" data-show-faces="true" data-auto-logout-link="true" scope="public_profile,email" onlogin="checkLoginState();"></fb:login-button></div>
                </div>
                <br/>
                <div id="revocateFBDiv" style="text-align: center;"></div>
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
            $("#singleupload").uploadFile({
                url:"functions/upload-avatar.php",
                multiple:false,
                dragDrop:false,
                acceptFiles:"image/*",
                maxFileCount:1,
                fileName:"avatar",
                dragDropStr: "<span><b>Faites glisser et déposez votre image</b></span>",
                abortStr:"Abandonner",
                cancelStr:"Annuler",
                doneStr:"Terminé !",
                multiDragErrorStr: "Un fichier à la fois !",
                extErrorStr:"n'est pas autorisé. Extensions autorisées:",
                sizeErrorStr:"n'est pas autorisé. Admis taille max:",
                uploadErrorStr:"Upload n'est pas autorisé",
                uploadStr:"Modifier",
                onLoad:function(obj)
                {
                    $("#eventsmessage").html($("#eventsmessage").html()+"<br/>Widget Loaded:");
                },
                onSubmit:function(files)
                {
                    $("#eventsmessage").html($("#eventsmessage").html()+"<br/>Submitting:"+JSON.stringify(files));
                    //return false;
                },
                onSuccess:function(files,data,xhr,pd)
                {
                    var d = new Date();
                    $(".ajax-file-upload-container").remove();
                    $("#singleupload").remove();
                    $("#username-form").before('<div><img id="avatarImg" style="max-height:250px;" src="<?php echo 'avatars/'.$_SESSION['user']['_id']."?" ?>'+d.getTime()+'" alt="no_avatar"></div>');
                    $("#current-avatar").replaceWith('<div id="current-avatar" style="display: inline-block;width:128px;height:128px;background-color:white;margin-top:-5px;margin-right:5px;overflow:hidden;"></div>'+
                        '<div class="form-group">'+
                            '<button id="saveNewAvatar" type="button" class="btn btn-success">'+
                              '<span data-original-title="Enregistrer mon avatar" class="glyphicon glyphicon-ok" data-toggle="tooltip"></span>'+
                            '</button>'+
                            '<button type="button" class="btn btn-danger">'+
                              '<span data-original-title="Enregistrer mon avatar" class="glyphicon glyphicon-remove" data-toggle="tooltip"></span>'+
                            '</button>'+
                        '</div>');
                    $('#avatarImg').cropper({
                        viewMode: 0,
                        aspectRatio: 1,
                        movable: false,
                        rotatable: false,
                        scalable: false,
                        zoomable: false,
                        preview: '#current-avatar',
                        crop: function(e) {
                          console.log('crop');
                        }
                    });
                    $("#saveNewAvatar").click(function(){
                        // get cropped image data
                        var blob = $('#avatarImg').cropper('getCroppedCanvas').toDataURL();
                        // transform it to Blob object
                        var newFile = dataURItoBlob(blob);
                        // set 'cropped to true' (so that we don't get to that listener again)
                        newFile.cropped = true;

                        // Upload cropped image to server if the browser supports `canvas.toBlob`
                        var formData = new FormData();
                        formData.append('croppedImage', newFile);

                        $.ajax('webservice/users/avatar', {
                          method: "POST",
                          data: formData,
                          processData: false,
                          contentType: false,
                          success: function () {
                            console.log('Upload success');
                            location.reload();
                          },
                          error: function () {
                            console.log('Upload error');
                          }
                        });
                    });
                }
            });

            $("#revocateFB").click(function() {
              revocateFacebookLink();
            });
        }, 500);
        loadAccountSettings();
        updateNotifications();
        setInterval(updateNotifications, 30000);
      });

      // transform cropper dataURI output to a Blob which Dropzone accepts
      function dataURItoBlob(dataURI) {
          var byteString = atob(dataURI.split(',')[1]);
          var ab = new ArrayBuffer(byteString.length);
          var ia = new Uint8Array(ab);
          for (var i = 0; i < byteString.length; i++) {
              ia[i] = byteString.charCodeAt(i);
          }
          return new Blob([ab], { type: 'image/jpeg' });
      }
    </script>
  </body>
  <?php
  include_once 'page_includes/instant-message-module.php';
  ?>
</body>
