<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

include_once 'dba/User.php.class';
require_once dirname(__FILE__).'/lib/openpgp-helper.php';

/*$key = openssl_pkey_new(array('private_key_bits' => 2048));
echo $key;

$bob_key = openssl_pkey_get_details($key);
$bob_public_key = $bob_key['key'];

echo '<br/><br/>Public key: '.$bob_public_key;

$alice_msg = "Hi Bob, im sending you a private message";
openssl_public_encrypt($alice_msg, $pvt_msg, $bob_public_key);

echo '<br/><br/>Encrypted: '.base64_encode($pvt_msg);

openssl_private_decrypt( $pvt_msg, $bob_received_msg, $key);
echo '<br/><br/>Decrypted: '.$bob_received_msg;*/

$priv_user_key = OpenPGP_Helper::GetPrivateKey("56193121766d83bc6e7b23c7");
echo '<br/><br/>Private Key: '.print_r($priv_user_key);

$pub_user_key = OpenPGP_Helper::GetPublicKey("56193121766d83bc6e7b23c7");
echo '<br/><br/>Public Key: '.print_r($pub_user_key);

$data = new OpenPGP_LiteralDataPacket('Bob send message to Alice.');
$encrypted = OpenPGP_Crypt_Symmetric::encrypt($pub_user_key, new OpenPGP_Message(array($data)));

echo '<br/><br/>Encrypted: '.base64_encode($encrypted->to_bytes());
// Now decrypt it with the same key
$decryptor = new OpenPGP_Crypt_RSA($priv_user_key);
$decrypted = $decryptor->decrypt($encrypted);
echo '<br/><br/>Decrypted: '.$decrypted->packets[0]->data;
?>
