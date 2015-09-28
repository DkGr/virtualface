<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
require dirname(__FILE__).'/lib/openpgp.php';
require dirname(__FILE__).'/lib/openpgp_crypt_rsa.php';

$rsa = new Crypt_RSA();
$k = $rsa->createKey(512);
$rsa->loadKey($k['privatekey']);

$nkey = new OpenPGP_SecretKeyPacket(array(
   'n' => $rsa->modulus->toBytes(),
   'e' => $rsa->publicExponent->toBytes(),
   'd' => $rsa->exponent->toBytes(),
   'p' => $rsa->primes[1]->toBytes(),
   'q' => $rsa->primes[2]->toBytes(),
   'u' => $rsa->coefficients[2]->toBytes()
));

$uid = new OpenPGP_UserIDPacket('Test <test@example.com>');

$wkey = new OpenPGP_Crypt_RSA($nkey);
$m = $wkey->sign_key_userid(array($nkey, $uid));

echo 'Private key: '.$wkey->private_key());
echo '<br/><br/>Public key: '.$k['publickey'];

$data = new OpenPGP_LiteralDataPacket('Hello !');
$encrypted = OpenPGP_Crypt_Symmetric::encrypt($wkey, new OpenPGP_Message(array($data)));

echo '<br/><br/>Encrypted: '.$encrypted->to_bytes();


$decryptor = new OpenPGP_Crypt_RSA(array($wkey));
$decrypted = $decryptor->decrypt($encrypted);
echo '<br/><br/>Decrypted: '.print($decrypted);

?>
