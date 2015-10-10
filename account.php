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
    <style type="text/css">
      /* add a little bottom space under the images */
      .thumbnail {
        margin-bottom:7px;
      }
    </style>
    <?php include_once "page_includes/header.php" ?>
    <link type="text/css" rel="stylesheet" media="screen" href="css/converse.css" />
    <![if gte IE 9]>
        <script src="js/converse.min.js"></script>
    <![endif]>
  </head>

  <body>
    <?php include_once 'page_includes/facebook-status.php'; ?>
  	<?php include_once 'page_includes/navbar.php'; ?>
  <?php if(!$useFacebookConnect || $user->isFacebookLinked()){ ?>
    <div class="container">
      <div class="container-fluid">
        <!-- SIDEBAR -->
        <div style="width:20%;float:left;padding:10px;">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img class="img-thumbnail" src="<?php echo 'avatars/'.$user->getId(); ?>" alt="no_avatar" style="width:128px;height:128px;background-color:white;margin-top:-5px;margin-right:5px;"></a>
          <br/>
          <form enctype="multipart/form-data">
              <input name="file" type="file" />
              <input type="button" value="Upload" />
          </form>
          <!--<progress></progress>-->
          <input value="<?php echo $user->getId(); ?>" id="userid" type="hidden" >
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Mon compte</h3>
            </div>
            <div class="panel-body">
            </div>
          </div>
        </div>
      	<!-- Main content -->
      	<div style="width:77%;float:left;">
          <div id="tabContent" class="tab-content">

          </div>
    </div>
    <?php } else {
      include_once 'page_includes/facebook-validation.php';
    } ?>
	  <?php include_once 'page_includes/instant-message-module.php'; ?>
</body>
