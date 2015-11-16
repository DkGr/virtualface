<?php
session_start();
error_reporting(E_ALL | E_STRICT);
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
    <![if gte IE 9]>
        <script src="js/converse.min.js"></script>
    <![endif]>
  </head>

  <body>
    <input id="myid" value="<?php echo (string)$_SESSION['user']['_id']; ?>" type="hidden" >
    <?php 
        include_once 'page_includes/facebook-status.php'; 
    ?>
    <?php include_once 'page_includes/navbar.php'; ?>
    <div class="container">
      <div class="container-fluid">
        <!-- SIDEBAR -->
        <div style="width:30%;float:left;padding:10px;">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img class="img-thumbnail" src="<?php echo 'avatars/'.$_SESSION['user']['_id']; ?>" alt="no_avatar" style="width:128px;height:128px;background-color:white;margin-top:-5px;margin-right:5px;"></a>
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
              Nom d'affichage :
              <br/>
              Nom d'utilisateur :
              <br/>
              E-mail :
              <br/>
            </div>
          </div>
        </div>
      	<!-- Main content -->
      	<div style="width:77%;">
          <div id="tabContent" class="tab-content">
            <h4>Qui peut ... ?</h4>
            <h5>Voir mon adresse email : </h5>
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary active">
                <input type="radio" name="options" id="option1" autocomplete="off" checked> Tout le monde
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="options" id="option2" autocomplete="off"> Mes amis
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="options" id="option3" autocomplete="off"> Moi uniquement
              </label>
            </div>
            <br/>
            <h5>Voir ma liste d'amis : </h5>
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary active">
                <input type="radio" name="options" id="option1" autocomplete="off" checked> Tout le monde
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="options" id="option2" autocomplete="off"> Mes amis
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="options" id="option3" autocomplete="off"> Moi uniquement
              </label>
            </div>
            <br/>
            <h5>Voir mes photos : </h5>
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary active">
                <input type="radio" name="options" id="option1" autocomplete="off" checked> Tout le monde
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="options" id="option2" autocomplete="off"> Mes amis
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="options" id="option3" autocomplete="off"> Moi uniquement
              </label>
            </div>
            <br/>
          </div>
    </div>
    <script type="text/javascript">
      $(document).ready(function() {
        //updateNotifications();
    	});
    </script>
	  <?php include_once 'page_includes/instant-message-module.php'; ?>
</body>
