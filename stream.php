<?php
session_start();
include_once 'fb-api.php';

$helper = $fb->getJavaScriptHelper();
try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (isset($accessToken)) {
  $_SESSION['facebook_access_token'] = $accessToken;

  // Sets the default fallback access token so we don't have to pass it to each request
  $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);

  try {
    $response = $fb->get('/me');
    $userNode = $response->getGraphUser();
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }
}
else{
  header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>VirtualID</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/home.css" rel="stylesheet">
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
      version    : 'v2.4' // use version 2.2
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
              <a class="navbar-brand" href="#">VirtualID</a>
            </div>
            <div class="navbar-collapse collapse">
			  <ul class="nav navbar-nav navbar-left">
  				<li class="active"><a href="#stream" data-toggle="tab">Flux</a></li>
  				<li><a href="#profile" data-toggle="tab">Profil</a></li>
  				<li><a href="#messages" data-toggle="tab">Messages</a></li>
			  </ul>
              <ul class="nav navbar-nav navbar-right">
              	<li><a href="#" style="width: 250px;" class="btn popovers" data-toggle="popover"
              	data-content="<div class='list-group-item'><a href='' title='test add link'>Magnum</a> à publié un <a href='' title='test add link'>message</a> dans votre flux.</div><div class='list-group-item'>Vous avez un nouveau message.</div>">Notifications <span class="badge">2</span></a></li>
				<li class="dropdown active" style="margin-right:50px;">
          			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img class="media-object" src="img/no_avatar.png" alt="no_avatar" style="float:left;width:32px;height:32px;background-color:white;margin-top:-5px;margin-right:5px;"><?php echo $userNode->getName() ?> <b class="caret"></b></a>
          			<ul class="dropdown-menu">
            			<li><a href="#">Mon compte</a></li>
            			<li><a href="#">Paramètres</a></li>
            			<li class="divider"></li>
            			<li><a onclick="logout()" href="#">Déconnexion</a></li>
          			</ul>
        		</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

	<!-- CONTENUS -->
	<div class="tab-content container">
		<!-- CONTENU GARAGE -->
  		<div class="tab-pane fade in active" id="stream">
  		</div>
  		<!-- CONTENU PROFIL -->
  		<div class="tab-pane fade" id="profile">
  		</div>
  		<!-- CONTENU MESSAGES -->
  		<div class="tab-pane fade" id="messages">
  		</div>
	</div>

	<!-- SCRIPTS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
		$('[data-toggle="popover"]').popover({'html':'true','placement':'bottom','trigger':'click'})
	});
    </script>
  </body>
</html>
