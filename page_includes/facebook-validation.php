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
    <input name="username" type="text" pattern="[a-zA-Z0-9]+" title="Peut contenir des caractères alpha-numériques (0 à 9 et A à z), des tirets (-), des underscores (_) ou des points (.)" placeholder="Nom d'utilisateur (identifiant)" class="form-control" required autofocus>
    <input name="displayname" value="<?php echo $user->getDisplayname() ?>" type="text" class="form-control" placeholder="Nom d'affichage">
    <input value="<?php echo $user->getEmail() ?>" name="email" type="email" placeholder="E-mail" class="form-control" required>
    <br/><p>Créez un mot de passe pour votre compte VirtualID.</p>
    <input name="password" type="password" class="form-control" placeholder="Mot de passe" required>
    <input name="passwordcheck" type="password" class="form-control" placeholder="Vérification mot de passe" required>
    <p style="color:red;"> <?php if(isset($erreur))echo $erreur; ?> </p>
    <button name="validate-fb-sub" value="validate-fb-sub" class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>
  </form>
</div>
