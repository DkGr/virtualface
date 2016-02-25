<!-- MENU NAVIGATION BAR -->
<div class="navbar-wrapper">
  <div class="container">
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
            <a class="navbar-brand" style="color: #fff;font-size: 20px;" href="stream.php">VirtualID</a>
        </div>
        <div class="navbar-collapse collapse">
    <?php if(isset($_SESSION['user'])){ ?>
    <ul class="nav navbar-nav navbar-left">
      <?php  if(basename($_SERVER['PHP_SELF']) == 'stream.php'){ ?>
      <li class="active"><a href="javascript: void(0);" onclick="loadPosts();"><span class="glyphicon glyphicon-flash" aria-hidden="true"></span> Mon flux</a></li>
      <li><a href="emails.php"><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span> Mon courrier</a></li>
      <?php } elseif ((basename($_SERVER['PHP_SELF']) == 'identity.php') || (basename($_SERVER['PHP_SELF']) == 'account.php')) { ?>
      <li><a href="stream.php"><span class="glyphicon glyphicon-flash" aria-hidden="true"></span> Mon flux</a></li>
      <li><a href="emails.php"><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span> Mon courrier</a></li>
      <?php } elseif (basename($_SERVER['PHP_SELF']) == 'emails.php') { ?>
      <li><a href="stream.php"><span class="glyphicon glyphicon-flash" aria-hidden="true"></span> Mon flux</a></li>
      <li class="active"><a href="emails.php"><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span> Mon courrier</a></li>
      <?php } else { ?>
      <li><a href="stream.php"><span class="glyphicon glyphicon-flash" aria-hidden="true"></span> Mon flux</a></li>
      <li><a href="emails.php"><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span> Mon courrier</a></li>
      <?php } ?>
    </ul>
    <?php } ?>
          <ul class="nav navbar-nav navbar-right">
            <?php if(isset($_SESSION['user'])){ ?>
              <li>
                <form class="navbar-form navbar-right">
                  <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                    <div id="searchFriendBar" style="width:280px;" class="form-control" type="text" placeholder="Rechercher des amis..." aria-describedby="basic-addon1">
                    </div>
                  </div>
                </form>
              </li>
              <li id="notifPanel"></li>
              <li class="dropdown active" style="margin-right:50px;">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img class="media-object" src="<?php echo 'avatars/'.$_SESSION['user']['_id']; ?>" alt="no_avatar" style="float:left;width:32px;height:32px;background-color:white;margin-top:-5px;margin-right:5px;"><?php echo $_SESSION['user']['infos']['username']; ?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <?php if(isset($_SESSION['user'])){ ?>
                    <li><a href="<?php echo 'identity.php?userid='.$_SESSION['user']['_id']; ?>">Voir ma page</a></li>
                    <li><a href="account.php">Paramètres</a></li>
                    <li class="divider"></li>
                    <?php } ?>
                    <li><a onclick="logout()" href="#">Déconnexion</a></li>
                  </ul>
              </li>
              <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
