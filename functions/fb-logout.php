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
       window.fbAsyncInit = function() {
       FB.init({
         appId      : '117561025264451',
         cookie     : true,  // enable cookies to allow the server to access
                             // the session
         xfbml      : true,  // parse social plugins on this page
         version    : 'v2.4' // use version 2.2
       });

       // Load the SDK asynchronously
       (function(d, s, id) {
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) return;
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/fr_FR/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));

        FB.logout(function(response) {
               window.location = "index.php";
           });
        };
     </script>
   </body>
