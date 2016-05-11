<?php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

if(isset($_SESSION['user']))
{
    header("Location: stream.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php include_once "page_includes/header.php" ?>
    <title>VirtualID - Le réseau social libre qui respecte votre vie privée</title>
  </head>

  <body>
    <script>
      // This is called with the results from from FB.getLoginStatus().
      function statusChangeCallback(response) {
        // The response object is returned with a status field that lets the
        // app know the current login status of the person.
        // Full docs on the response object can be found in the documentation
        // for FB.getLoginStatus().
        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            loginfb(response.authResponse.userID);
        } else if (response.status === 'not_authorized') {
            // the user is logged in to Facebook,
            // but has not authenticated your app
            //window.location = "facebook-validation.php";
        } else {
            // the user isn't logged in to Facebook.
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
        appId      : '<?php echo $GLOBALS['facebook_app_id']; ?>',
        cookie     : true,  // enable cookies to allow the server to access
                            // the session
        xfbml      : true,  // parse social plugins on this page
        version    : 'v2.6' // use version 2.4
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
    <!-- MENU NAVIGATION BAR -->
    <div class="navbar-wrapper">
      <div class="container">
        <div class="navbar navbar-inverse navbar-static-top" style="border-radius: 4px;" role="navigation">
          <div class="container">
            <div class="navbar-header">
              <a class="navbar-brand" style="color: #fff;font-size: 20px;padding: initial;" href="#"><img style="height: inherit;" alt="VirtualID" src="img/virtualid-white.png"></img></a>
            </div>
            <div class="navbar-collapse collapse">
              <ul class="nav navbar-nav navbar-right">
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- CONNEXION -->
    <div id="login-form" class="container">
      <div class="jumbotron" style="max-width:800px;margin:auto;padding:0px;">
          <img style="margin-left: -20px;" alt="VirtualID" src="img/virtualid-black.png"></img>
          <h1 style="visibility: hidden;">VirtualID</h1>
          <p class="lead">Le réseau social <b>libre</b> et <b>décentralisé</b> qui respecte votre vie privée !
          <ul>
            <li><b>Pas de revente</b> de vos informations</li>
            <li><b>Pas de traçage</b> publicitaire</li>
            <li>Toutes vos <b>données privées</b> sont <b>cryptées</b> (Informations personnelles, Messages privés, Photos,...)</li>
            <li>Chacun peut <b>héberger son compte</b> (et celui d'autres personnes) chez lui</li>
          </ul>
          </p>
      </div>
      <form class="form-signin" role="form" onsubmit="return false;">
        <h2 class="form-signin-heading">Connectez vous !</h2>
        <input id="username" name="username" type="username" placeholder="Nom d'utilisateur" title="Peut contenir des caractères alpha-numériques en minuscule (0 à 9 et a à z), des tirets (-), des underscores (_) ou des points (.)" pattern="[a-z0-9]+[a-z0-9._-]+[a-z0-9]+" class="form-control" required autofocus>
        <input id="password" name="password" type="password" class="form-control" placeholder="Mot de passe" required>
        <p id="login-error" style="color:red;"></p>

        <button id="login-btn" name="login" value="login" class="btn btn-lg btn-primary btn-block" type="submit">Connexion</button>
        <a data-toggle="modal" data-target="#myModal" href="#" style="display:block;text-align:center;margin-top:10px;margin-bottom:10px;">S'inscrire sans facebook</a>
        <br/>
        <h4>Créez votre compte avec Facebook</h4>
        <fb:login-button data-max-rows="1" data-size="xlarge" data-show-faces="true" data-auto-logout-link="true" scope="public_profile,email" onlogin="checkLoginState();">
        </fb:login-button>
      </form>
    </div>
    <!-- INSCRIPTION -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h2 class="modal-title" id="myModalLabel">Inscription</h2>
                </div>
                <div class="modal-body">
                    <div>
                        <p id="errormessage" style="color:red;"></p>
                        <form class="form-signup" role="form" onsubmit="return false;">
                            <input id="sub-username" name="username" type="text" class="form-control" title="Peut contenir des caractères alpha-numériques en minuscule (0 à 9 et a à z), des tirets (-), des underscores (_) ou des points (.)" pattern="[a-z0-9]+[a-z0-9._-]+[a-z0-9]+" placeholder="Nom d'utilisateur (identifiant unique)" required autofocus>
                            <input id="sub-displayname" name="displayname" type="text" class="form-control" placeholder="Nom d'affichage">
                            <input id="sub-email" name="email" type="email" class="form-control" placeholder="Email">
                            <input id="sub-password" name="password" type="password" class="form-control" placeholder="Mot de passe" required>
                            <input id="sub-passwordcheck" name="passwordcheck" type="password" class="form-control" placeholder="Vérification du mot de passe" required>
                            <button id="validate-sub" name="subscribe" value="subscribe" class="btn btn-lg btn-primary btn-block" style="margin-top:10px;" type="submit">Valider</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
    <hr style="border-top: 1px solid #DADADA;"/>
    <footer class="footer">
      <div style="float: left;width: 20%;margin: 25px;">
        Ce projet est distribué sous licence GNU GPL v2.
        <br/>
        Le code source est disponible sur <a href="https://github.com/DkGr/virtualid">Github</a>
        <a href="https://github.com/DkGr/virtualid"><img style="width: 32px;" alt="github-icon" src="img/mark-github-512.png"/></a>
      </div>
    </footer>
    <!-- SCRIPTS -->
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/openpgp.min.js"></script>
    <script type="text/javascript" src="js/virtualid.js"></script>
  </body>
</html>
