<?php
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

//require_once dirname(__FILE__).'/../config/config.php';
require_once dirname(__FILE__).'/../lib/openfire.php';
require_once dirname(__FILE__).'/PrivacySettings.php';
require_once dirname(__FILE__).'/Notifications.php';
/** mongodb User collection structure
 * {
 *     "_id" : ObjectId("xxxxxxxxxxxxxxxxxxxxxxxx"),
 *     "infos" : {
 *         "username" : "joe",
 *         "displayname" : "Joe Danger",
 *         "password" : "PASSWORD_HASH",
 *         "email" : "joe@email.net"
 *     },
 *		 "fb-link" : true,
 *               "fb-id" : xxxxxxxx,
 *		 "friends" : {
 *                  User1ID => true, User2ID => false, ...
 *		 },
 *     "private_key" : "base64encoded",
 *     "public_key" : "base64encoded",
 *     "privacy_settings" : {
 *          "displayname" : 0,
 *          "email" : 0,
 *          "friends" : 0,
 *          "posts" : 0
 *     }
 * }
 */

class User {
	private $_id;
	private $VirtualIDDB;

	public function __construct(){
            include 'DatabaseConnect.php';
        }
        
        public function load($id)
        {     
            return $this->VirtualIDDB->Users->findOne(array('_id' => new MongoId((string)$id)));
        }

        public function setId($id)
	{
            $this->_id = $id;
	}

	public function getId()
	{
            return $this->_id;
	}

	public function loginUser($username, $password)
	{
            $this->_id = (string)new MongoId();
            $user = $this->VirtualIDDB->Users->findOne(array('infos.username' => $username));
            if(isset($user))
            {
                if(password_verify($password, $user['infos']['password']))
                {
                    $this->_id = (string)$user['_id'];
                    return true;
                }
                else{
                    return false;
                }
            }
            else {
                return false;
            }
	}

	public function getVirtualIdWithFacebookId($fb_id)
	{
            $user = $this->VirtualIDDB->Users->findOne(array('fb-id' => $fb_id));
            return $user;
	}

	public function checkPassword($password)
	{
            $user = $this->VirtualIDDB->Users->findOne(array('_id' => new MongoId($this->_id)));
            $dbPass = $user['infos']['password'];
            if(password_verify($password, $dbPass))
            {
                return true;
            }
            else{
                return false;
            }
	}

	public function getUsername()
	{
            $user = $this->VirtualIDDB->Users->findOne(array('_id' => new MongoId($this->_id)));
            return $user['infos']['username'];
	}

	public function getDisplayname()
	{
            $user = $this->VirtualIDDB->Users->findOne(array('_id' => new MongoId($this->_id)));
            return $user['infos']['displayname'];
	}

	public function getPasswordHash()
	{
            $user = $this->VirtualIDDB->Users->findOne(array('_id' => new MongoId($this->_id)));
            return $user['infos']['password'];
	}

	public function updateUsername($username)
	{
            $this->VirtualIDDB->Users->update(array('_id' => new MongoId($this->_id)), array('$set' => array('infos.username' => $username)));
	}

	public function updateDisplayname($displayname)
	{
            $this->VirtualIDDB->Users->update(array('_id' => new MongoId($this->_id)), array('$set' => array('infos.displayname' => $displayname)));
	}
        
        public function updateEmail($email)
	{
            $this->VirtualIDDB->Users->update(array('_id' => new MongoId($this->_id)), array('$set' => array('infos.email' => $email)));
	}
        
        public function updatePassword($newpassword)
	{
            $this->VirtualIDDB->Users->update(array('_id' => new MongoId($this->_id)), array('$set' => array('infos.password' => $newpassword)));
	}

	public function getEmail()
	{
            $user = $this->VirtualIDDB->Users->findOne(array('_id' => new MongoId($this->_id)));
            return $user['infos']['email'];
	}

	public function getPublicKey()
	{
            $user = $this->VirtualIDDB->Users->findOne(array('_id' => new MongoId($this->_id)));
            return $user['public_key'];
	}

	public function getPrivateKey()
	{
            $user = $this->VirtualIDDB->Users->findOne(array('_id' => new MongoId($this->_id)));
            return $user['private_key'];
	}

	public function saveKeys($privkey, $pubkey)
	{
            $this->VirtualIDDB->Users->update(array('_id' => new MongoId($this->_id)), array('$set' => array('private_key' => $privkey, 'public_key' => $pubkey)));
	}

	public function isFacebookLinked()
	{
            $user = $this->VirtualIDDB->Users->findOne(array('_id' => new MongoId($this->_id)));
            return $user['fb-link'];
	}

	public function setFacebookLinked()
	{
            $this->VirtualIDDB->Users->update(array('_id' => new MongoId($this->_id)), array('$set' => array('fb-link' => true)));
	}

	public function setFacebookId($fb_id)
	{
            $this->VirtualIDDB->Users->update(array('_id' => new MongoId($this->_id)), array('$set' => array('fb-id' => $fb_id)));
            $this->setFacebookLinked();
	}

	public function createNew($email, $displayname, $username, $password)
	{
            if ($this->VirtualIDDB->Users->findOne(array('infos.username' => $username))) {
                return false;
            }
            $userInfos = array('infos' => (object)array(
                                    'username' => $username,
                                    'displayname' => $displayname,
                                    'email' => $email,
                                    'password' => $password
                                ),
                                'fb-link' => false,
                                'fb-id' => 0,
                                'friends' => array(),
                                'private_key' => '',
                                'public_key' => '',
                                'privacy_settings' => PrivacySettings::getDefaultPrivacySettings()
                            );
            $this->VirtualIDDB->Users->insert($userInfos);
            return (string)$userInfos['_id'];
	}
        
        public function saveNew($newuser)
	{
            if ($this->VirtualIDDB->Users->findOne(array('infos.username' => $newuser->{'username'}))) {
                return false;
            }
            $displayname = (empty($newuser->{'displayname'}))?$newuser->{'username'}:$newuser->{'displayname'};
            $userInfos = array('infos' => array(
                                    'username' => $newuser->{'username'},
                                    'displayname' => $newuser->{'displayname'},
                                    'email' => $newuser->{'email'},
                                    'password' => password_hash($newuser->{'password'}, PASSWORD_DEFAULT)
                                ),
                                'fb-link' => false,
                                'fb-id' => 0,
                                'friends' => array(),
                                'private_key' => '',
                                'public_key' => '',
                                'privacy_settings' => PrivacySettings::getDefaultPrivacySettings()
                            );
            $this->VirtualIDDB->Users->insert($userInfos);
            
            $contentOrFalseOnFailure   = file_get_contents(__DIR__.'/../img/no_avatar.png');
            $filenameOut = __DIR__.'/../avatars/'.$userInfos['_id'];
            $byteCountOrFalseOnFailure = file_put_contents($filenameOut, $contentOrFalseOnFailure);

            // Create the Openfire Rest api object
            $OpenfireAPI = new Gidkom\OpenFireRestApi\OpenFireRestApi;

            // Set the required config parameters
            $OpenfireAPI->secret = "m8D6vTN7L0QVwUq4";
            $OpenfireAPI->host = $GLOBALS['VIDdomain'];
            $OpenfireAPI->port = "9091";  // default 9090

            // Optional parameters (showing default values)
            $OpenfireAPI->useSSL = true;
            $OpenfireAPI->plugin = "/plugins/restapi/v1";  // plugin

            AddOpenfireUser($OpenfireAPI, $userInfos['infos']['username'], md5($userInfos['infos']['password']), $userInfos['infos']['username']);
            return $userInfos;
	}

	public function deleteUser($user)
	{
            //TODO
	}

	public function AddFriend($friendid)
	{
            $friends = $this->GetFriends();
            $friend = new User();
            $friend->setId($friendid);
            if($friend->IsAskedFriend($this->_id))
            {
                $friends[$friendid] = true;
                $friendsFriends = $friend->GetFriends();
                $friendsFriends[$this->_id] = true;
                $friend->UpdateFriends($friendsFriends);
                $notif = new Notification();
                $notif->CreateNew((string)$friendid, date("Y/m/d H:i:s"), 'Vous êtes maintenant ami avec <a href="identity.php?userid='.$this->_id.'">'.$this->getUsername().'</a>');
                $notif = new Notification();
                $notif->CreateNew((string)$this->_id, date("Y/m/d H:i:s"), 'Vous êtes maintenant ami avec <a href="identity.php?userid='.$friendid.'">'.$friend->getUsername().'</a>');
            }
            else {
                $notif = new Notification();
                $notif->CreateNew((string)$friendid, date("Y/m/d H:i:s"), '<a href="identity.php?userid='.$this->_id.'">'.$this->getUsername().'</a> veut être votre ami');
                $friends[$friendid] = false;
            }
            $this->UpdateFriends($friends);
	}

	public function GetFriends()
	{
            $user = $this->VirtualIDDB->Users->findOne(array('_id' => new MongoId($this->_id)));
            return $user['friends'];
	}

	public function UpdateFriends($friends)
	{
            $this->VirtualIDDB->Users->update(array('_id' => new MongoId($this->_id)), array('$set' => array('friends' => $friends)));
	}

	public function RemoveFriend($friendid)
	{
            $friends = $this->GetFriends();
            unset($friends[$friendid]);
            $friend = new User();
            $friend->setId($friendid);
            $friend->RemoveFriend($this->_id);
            $this->UpdateFriends($friends);
	}

	public function IsMyFriend($friendid)
	{
            $friends = $this->GetFriends();
            if(array_key_exists($friendid, $friends))
            {
                return $friends[$friendid];
            }
            else {
                return false;
            }
	}

	public function IsAskedFriend($friendid)
	{
            $friends = $this->GetFriends();
            if(array_key_exists($friendid, $friends))
            {
                return true;
            }
            else {
                return false;
            }
	}

	public function GetAllUsers()
	{
            return $this->VirtualIDDB->Users->find();
	}
}
?>
