<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

include_once 'dba/User.php.class';
?>
<script src="js/jquery-2.1.4.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/openpgp.min.js"></script>
<script src="js/magicsuggest-min.js"></script>
<script src="js/virtualid.js"></script>
<script>
  generateUserKeyPair('padman', 'pass');
</script>
