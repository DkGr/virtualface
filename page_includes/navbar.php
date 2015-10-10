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
      <li class="active"><a onclick="loadPosts();" href="#stream" data-toggle="tab">Mon flux</a></li>
      <li><a href="#messages" data-toggle="tab">Messages privés<span class="badge">1</span></a></li>
    </ul>
    <?php } ?>
          <ul class="nav navbar-nav navbar-right">
            <?php if(!$useFacebookConnect || $user->isFacebookLinked()){ ?>
              <li>
                <form class="navbar-form navbar-right">
                  <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                    <input id="searchFriendBar" style="width:200px;" class="form-control" type="text" placeholder="Rechercher des amis..." aria-describedby="basic-addon1">
                  </div>
                </form>
              </li>
              <li id="notifPanel"></li>
            <?php } ?>
              <li class="dropdown active" style="margin-right:50px;">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img class="media-object" src="<?php echo 'avatars/'.$user->getId(); ?>" alt="no_avatar" style="float:left;width:32px;height:32px;background-color:white;margin-top:-5px;margin-right:5px;"><?php echo $user->getUsername(); ?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <?php if(!$useFacebookConnect || $user->isFacebookLinked()){ ?>
                    <li><a href="account.php">Mon compte</a></li>
                    <li><a href="account.php#settings">Paramètres</a></li>
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
