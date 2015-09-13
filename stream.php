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
    <script src="js/jquery.js"></script>
    <script src="js/jquery.form.js"></script>

    <script>
        // wait for the DOM to be loaded
        $(document).ready(function() {
            // bind 'myForm' and provide a simple callback function
            $('#postInStream').ajaxForm(function() {
                alert("Thank you for your comment!");
            });
        });
    </script>
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
      } else if (response.status === 'not_authorized') {
        // The person is logged into Facebook, but not your app.
        window.location = "index.php";
      } else {
        // The person is not logged into Facebook, so we're not sure if
        // they are logged into this app or not.
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

    function logout() {
      FB.logout(function(response) {
        // Person is now logged out
        window.location = "index.php";
    });
    }
    </script>
  	<!-- MENU NAVIGATION BAR -->
  	<div class="navbar-wrapper">
      <div class="container">
        <div class="navbar navbar-inverse navbar-static-top" style="border-radius: 4px;" role="navigation">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="stream.php">VirtualID</a>
            </div>
            <div class="navbar-collapse collapse">
        <?php if(!$useFacebookConnect || $user->isFacebookLinked()){ ?>
			  <ul class="nav navbar-nav navbar-left">
  				<li class="active"><a href="#stream" data-toggle="tab">Flux</a></li>
  				<li><a href="#messages" data-toggle="tab">Messages <span class="badge">1</span></a></li>
			  </ul>
        <?php } ?>
              <ul class="nav navbar-nav navbar-right">
              	<?php if(!$useFacebookConnect || $user->isFacebookLinked()){ ?>
                  <li>
                    <form class="navbar-form navbar-right">
                      <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                        <input class="form-control" type="text" placeholder="Rechercher des amis..." aria-describedby="basic-addon1" onchange="">
                      </div>

                    </form>
                  </li>
                  <li><a tabindex="0" class="btn" role="button" data-toggle="popover" data-trigger="focus" style="width: 250px;" data-content="<div class='list-group-item'><a href='' title='test add link'>Magnum</a> à publié un <a href='' title='test add link'>message</a> dans votre flux.</div><div class='list-group-item'>Vous avez un nouveau message.</div>">Notifications <span class="badge">2</span></a></li>
				        <?php } ?>
                  <li class="dropdown active" style="margin-right:50px;">
                			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img class="media-object" src="img/no_avatar.png" alt="no_avatar" style="float:left;width:32px;height:32px;background-color:white;margin-top:-5px;margin-right:5px;"><?php echo $user->getUsername() ?> <b class="caret"></b></a>
                			<ul class="dropdown-menu">
                        <?php if(!$useFacebookConnect || $user->isFacebookLinked()){ ?>
                  			<li><a href="#">Mon compte</a></li>
                  			<li><a href="#">Paramètres</a></li>
                  			<li class="divider"></li>
                        <?php } ?>
                  			<li><a onclick="logout()" href="#">Déconnexion</a></li>
                			</ul>
              		</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php if(!$useFacebookConnect || $user->isFacebookLinked()){ ?>
    <div class="container">
      <div class="container-fluid">
        <div style="width:20%;float:left;padding:10px;">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Vos amis</h3>
            </div>
            <div class="panel-body">
              :( Vous n'avez pas encore d'amis...
            </div>
          </div>
        </div>
      	<!-- CONTENUS -->
      	<div style="width:77%;float:left;">
    		  <!-- CONTENU FLUX -->
      		<div class="tab-pane fade in active" id="stream">
            <form id="postInStream" action="functions/post_stream.php">
              <div style="box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);" class="panel panel-default">
                <textarea style="resize: vertical;margin:5px; width:99%;" class="form-control" rows="3" placeholder="Inserez votre message, lien, photo, video, etc..."></textarea>
                <div class="panel-footer">
                  <button type="submit" class="btn btn-info">Publier</button>
                </div>
              </div>
            </form>
            <div style="box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);" class="panel panel-default">
              <div class="panel-body">
                <div class="media">
                  <div style="text-align:center;" class="media-left">
                    <a href="#">
                      <img class="media-object img-rounded" style="width: 64px; height: 64px;" src="img/no_avatar.png" alt="...">
                    </a>
                    <a>Padman</a>
                  </div>
                  <div class="media-body">
                    Post de test ! Woohoo !

                  </div>
                  <div style="float: left;margin: 10px 0 0 75px;">3 <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span style="color:grey;">Posté il y a environ 2 minutes</span></div>
                </div>
              </div>
              <div class="panel-footer">
                <a>1 commentaire</a> - <a>J'aime</a>
              </div>
              <ul class="list-group">
                <li class="list-group-item">
                  <div style="margin-left: 25px;" class="media">
                    <div style="text-align:center;" class="media-left">
                      <a href="#">
                        <img class="media-object img-rounded" style="width: 32px; height: 32px; margin: auto;" src="img/no_avatar.png" alt="...">
                      </a>
                      <a>Padman</a>
                    </div>
                    <div class="media-body">
                      Post de test ! Woohoo !

                    </div>
                    <div style="float: left;margin: 10px 0 0 75px;"><a>J'aime</a> - 3 <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span style="color:grey;">Posté il y a environ 2 minutes</span></div>
                  </div>
                </li>
              </ul>
            </div>
            <div style="box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);" class="panel panel-default">
              <div class="panel-body">
                <div class="media">
                  <div style="text-align:center;" class="media-left">
                    <a href="#">
                      <img class="media-object img-rounded" style="width: 64px; height: 64px;" src="img/no_avatar.png" alt="...">
                    </a>
                    <a>Padman</a>
                  </div>
                  <div class="media-body">
                    Post de test ! Woohoo !

                  </div>
                  <div style="float: left;margin: 10px 0 0 75px;">3 <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span style="color:grey;">Posté il y a environ 2 minutes</span></div>
                </div>
              </div>
              <div class="panel-footer">
                <a>1 commentaire</a> - <a>J'aime</a>
              </div>
              <ul class="list-group">
                <li class="list-group-item">
                  <div style="margin-left: 25px;" class="media">
                    <div style="text-align:center;" class="media-left">
                      <a href="#">
                        <img class="media-object img-rounded" style="width: 32px; height: 32px; margin: auto;" src="img/no_avatar.png" alt="...">
                      </a>
                      <a>Padman</a>
                    </div>
                    <div class="media-body">
                      Post de test ! Woohoo !

                    </div>
                    <div style="float: left;margin: 10px 0 0 75px;">3 <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span style="color:grey;">Posté il y a environ 2 minutes</span></div>
                  </div>
                </li>
              </ul>
            </div>
            <div style="box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);" class="panel panel-default">
              <div class="panel-body">
                <div class="media">
                  <div style="text-align:center;" class="media-left">
                    <a href="#">
                      <img class="media-object img-rounded" style="width: 64px; height: 64px;" src="img/no_avatar.png" alt="...">
                    </a>
                    <a>Padman</a>
                  </div>
                  <div class="media-body">
                    Post de test ! Woohoo !

                  </div>
                  <div style="float: left;margin: 10px 0 0 75px;">3 <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span style="color:grey;">Posté il y a environ 2 minutes</span></div>
                </div>
              </div>
              <div class="panel-footer">
                <a>1 commentaire</a> - <a>J'aime</a>
              </div>
              <ul class="list-group">
                <li class="list-group-item">
                  <div style="margin-left: 25px;" class="media">
                    <div style="text-align:center;" class="media-left">
                      <a href="#">
                        <img class="media-object img-rounded" style="width: 32px; height: 32px; margin: auto;" src="img/no_avatar.png" alt="...">
                      </a>
                      <a>Padman</a>
                    </div>
                    <div class="media-body">
                      Post de test ! Woohoo !

                    </div>
                    <div style="float: left;margin: 10px 0 0 75px;">3 <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span style="color:grey;">Posté il y a environ 2 minutes</span></div>
                  </div>
                </li>
              </ul>
            </div>
        	</div>
        		<!-- CONTENU MESSAGES -->
        	<div class="tab-pane fade" id="messages">
        	</div>
        </div>
      </div>
    </div>
  <?php } else { ?>
    <div id="login-form" class="container">
      <form action="stream.php" class="form-signin" role="form" method="post">
        <h3 class="form-signin-heading">Validez vos informations</h3>
        <p>Ces informations proviennent de votre compte Facebook.</p>
        <p><em>Votre adresse E-mail ne sera pas visible des autres utilisateurs. Elle sera utilisée uniquement pour :<br/>
          <ul>
            <li> Vous connecter</li>
            <li> En cas de perte de votre mot de passe</li>
          </ul>
        </em></p>
        <input value="<?php echo $user->getUsername() ?>" name="username" type="text" placeholder="Nom d'utilisateur" class="form-control" required autofocus>
        <input value="<?php echo $user->getEmail() ?>" name="email" type="email" placeholder="E-mail" class="form-control" required>
        <br/><p>Créez un mot de passe pour votre compte VirtualID.</p>
        <input name="password" type="password" class="form-control" placeholder="Mot de passe" required>
        <input name="passwordcheck" type="password" class="form-control" placeholder="Vérification mot de passe" required>
        <p style="color:red;"> <?php if(isset($erreur))echo $erreur; ?> </p>
        <button name="validate-fb-sub" value="validate-fb-sub" class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>
      </form>
    </div>
  <?php } ?>
	<!-- SCRIPTS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
		$('[data-toggle="popover"]').popover({'html':'true','placement':'bottom','trigger':'focus'})
	});
    </script>
  </body>
</html>
