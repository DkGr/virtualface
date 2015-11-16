<?php
session_start();
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php include_once "page_includes/header.php" ?>
  </head>

  <body>
      <script>
      // This is called with the results from from FB.getLoginStatus().
      function statusChangeCallback(response) {
        console.log('statusChangeCallback');
        console.log(response);
        // The response object is returned with a status field that lets the
        // app know the current login status of the person.
        // Full docs on the response object can be found in the documentation
        // for FB.getLoginStatus().
        if (response.status === 'connected') {
          // Logged into your app and Facebook.
            FB.api(
                "/me?fields=id,name,email,picture.type(large)",
                function (response) {
                    if (response && !response.error) {
                        /* handle the result */
                        var fbUserID = response.id;
                        var fbUserEmail = '';
                        if(response.hasOwnProperty('email'))
                        {
                            fbUserEmail = response.email;
                        }
                        var fbUserName = response.name;
                        var fbUserAvatarURL = response.picture.data.url;
                        
                        $("#displayname").val(fbUserName);
                        $("#email").val(fbUserEmail);
                        $("#avatar").attr("src", function() {
                            return fbUserAvatarURL;
                        });
                        
                        $("#validate-fb-sub").click(function() {
                            subscribeFromFacebook(fbUserID, $("#avatar").attr("src"));
                        });
                    }
                }
            );
        }
        else{
            window.location = "index.php";
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

      window.fbAsyncInit = function() {
      FB.init({
        appId      : '117561025264451',
        cookie     : true,  // enable cookies to allow the server to access
                            // the session
        xfbml      : true,  // parse social plugins on this page
        version    : 'v2.2' // use version 2.2
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
    <?php //include_once 'page_includes/facebook-status.php'; ?>
    <?php include_once 'page_includes/navbar.php'; ?>
      <!-- Facebook subscription validation -->
        <div id="login-form" class="container">
          <form action="stream.php" class="form-signin" role="form" method="post">
            <h3 class="form-signin-heading">Validez vos informations</h3>
            <p>Ces informations proviennent de votre compte Facebook.</p>
            <p><em>Votre adresse E-mail ne sera pas visible des autres utilisateurs. Elle sera utilisée uniquement pour :<br/>
              <ul>
                <li> Vous envoyer des notifications</li>
                <li> Réinitialiser votre mot de passe</li>
              </ul>
            </em></p>
            <hr style="border-color: inherit;">
            <p><img id="avatar" class="img-thumbnail" src="" alt="no_avatar" style="width:128px;height:128px;background-color:white;display: block;margin-left: auto;margin-right: auto;"></p>
            <input id="username" name="username" type="text" title="Peut contenir des caractères alpha-numériques en minuscule (0 à 9 et a à z), des tirets (-), des underscores (_) ou des points (.)" pattern="[a-z0-9]+[a-z0-9._-]+[a-z0-9]+" placeholder="Nom d'utilisateur (identifiant)" class="form-control" required autofocus>
            <input id="displayname" name="displayname" value="" type="text" class="form-control" placeholder="Nom d'affichage">
            <input id="email" value="" name="email" type="email" placeholder="E-mail" class="form-control" required>
            <br/><p>Créez un mot de passe pour votre compte VirtualID.</p>
            <input id="password" name="password" type="password" class="form-control" placeholder="Mot de passe" required>
            <input id="passcheck" name="passwordcheck" type="password" class="form-control" placeholder="Vérification mot de passe" required>
            <p id="errormessage" style="color:red;"></p>
            <button id="validate-fb-sub" name="validate-fb-sub" value="validate-fb-sub" class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>
          </form>
        </div>
      <div class="modal fade" id="subscribingModal" tabindex="-1" role="dialog" aria-labelledby="subscribingModalLabel" aria-hidden="true">
  		<div class="modal-dialog modal-sm">
    		<div class="modal-content">
      			<div class="modal-header">
        			<h5 class="modal-title" id="subscribingModalLabel">Chargement</h5>
      			</div>
      			<div class="modal-body">
        			<div>
                <p>Génération des clés de chiffrement...</p>
                <div class="progress">
                  <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                  </div>
                </div>
					    </div>
      			</div>
    		</div>
            </div>
	</div>
    <!-- SCRIPTS -->
    <?php include_once 'page_includes/footer.php'; ?>
  </body>
</html>
