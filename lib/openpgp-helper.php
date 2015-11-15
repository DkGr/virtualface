<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

require_once dirname(__FILE__).'/../dba/User.php.class';
require_once dirname(__FILE__).'/openpgp.php';
require_once dirname(__FILE__).'/openpgp_crypt_rsa.php';

/**
 *
 */
class OpenPGP_Helper
{
  public static function GenerateKeyPair($userid)
  {
    $user = new User();
    $user->setId($userid);
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
    $domain = $_SERVER['HTTP_HOST'];
    if(strpos($domain, 'www.') !== false)
    {
      $domain = substr($domain, 4, strlen($domain));
    }
    $uid = new OpenPGP_UserIDPacket($user->getUsername().' <'.$user->getUsername().'@'.$domain.'>');
    $wkey = new OpenPGP_Crypt_RSA($nkey);
    $m = $wkey->sign_key_userid(array($nkey, $uid));
    $fp = fopen($userid.'.keys', 'wb');
    fwrite($fp, $m->to_bytes());
    fclose($fp);
  }

  public static function GetPrivateKey($userid)
  {
    $user = new User();
    $user->setId($userid);
    $priv = $user->getPrivateKey();
    $rsa = new Crypt_RSA();
    $rsa->loadKey($priv);
    $nkey = new OpenPGP_SecretKeyPacket(array(
       'n' => $rsa->modulus->toBytes(),
       'e' => $rsa->publicExponent->toBytes(),
       'd' => $rsa->exponent->toBytes(),
       'p' => $rsa->primes[1]->toBytes(),
       'q' => $rsa->primes[2]->toBytes(),
       'u' => $rsa->coefficients[2]->toBytes()
    ));
    $wkey = new OpenPGP_Crypt_RSA($nkey);
    $domain = $_SERVER['HTTP_HOST'];
    if(strpos($domain, 'www.') !== false)
    {
      $domain = substr($domain, 4, strlen($domain));
    }
    $uid = new OpenPGP_UserIDPacket($user->getUsername().'@'.$domain);
    $key = $wkey->sign_key_userid(array($nkey, $uid));
    return $key;
  }

  public static function GetPublicKey($userid)
  {
    $user = new User();
    $user->setId($userid);
    $pub = $user->getPublicKey();
    $rsa = new Crypt_RSA();
    $rsa->loadKey($pub);
    $nkey = new OpenPGP_PublicKeyPacket(array(
       'n' => $rsa->modulus->toBytes(),
       'e' => $rsa->publicExponent->toBytes(),
       'd' => $rsa->exponent->toBytes(),
       'p' => $rsa->primes[1]->toBytes(),
       'q' => $rsa->primes[2]->toBytes(),
       'u' => $rsa->coefficients[2]->toBytes()
    ));
    $wkey = new OpenPGP_Crypt_RSA($nkey);
    $domain = $_SERVER['HTTP_HOST'];
    if(strpos($domain, 'www.') !== false)
    {
      $domain = substr($domain, 4, strlen($domain));
    }
    $uid = new OpenPGP_UserIDPacket($user->getUsername().'@'.$domain);
    $key = $wkey->sign_key_userid(array($nkey, $uid));
    return $key;
  }

  public static function EncryptString($plaintext, $publicKeys)
  {

  }

  public static function DecryptString($ciphertext, $privateKey)
  {

  }
}
?>
