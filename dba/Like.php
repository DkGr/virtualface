<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

include_once dirname(__FILE__).'/Notifications.php.class';
include_once dirname(__FILE__).'/User.php.class';
include_once dirname(__FILE__).'/Post.php.class';
include_once dirname(__FILE__).'/Comment.php.class';
/** mongodb Like collection structure
 * {
 *     "_id" : ObjectId("xxxxxxxxxxxxxxxxxxxxxxxx"),
 *     "author" : xxxxxxxxxxxxxxxxxxxxxxxx,
 *		 "targetType" : 'post'|'comment'|...,
 *		 "targetId" : xxxxxxxxxxxxxxxxxxxxxxxx,
 *		 "date" : "yyyy-MM-dd HH:mm:ss"
 * }
 */

class Like {
	private $_id;
	private $VirtualIDDB;

	public function __construct(){
		$connexion = new MongoClient();
		$this->VirtualIDDB = $connexion->VirtualID;
  }

	public function CreateNew($userid, $targetType, $targetId, $date)
	{
		$newlike = array('author' => $userid,
										 'targetType' => $targetType,
										 'targetId' => $targetId,
										 'date' => $date
								 );
		$this->VirtualIDDB->Likes->insert($newlike);

		//Notification
		$liker = new User();
		$liker->setId($userid);
		if($targetType == "post")
		{
			$target = new Post();
			$target->setId($targetId);
			$notif = new Notification();
			$notif->CreateNew($target->getAuthor(), date("Y/m/d H:i:s"), '<a href="identity.php?userid='.$liker->getId().'">'.$liker->getUsername().'</a> aime votre post');
		}
		elseif ($targetType == "comment") {
			$target = new Comment();
			$target->setId($targetId);
			$notif = new Notification();
			$notif->CreateNew($target->getAuthor(), date("Y/m/d H:i:s"), '<a href="identity.php?userid='.$liker->getId().'">'.$liker->getUsername().'</a> aime votre commentaire');
		}
	}

	public function setId($id)
	{
		$this->_id = (string)$id;
	}

	public function getAuthor()
	{
		$like = $this->VirtualIDDB->Likes->findOne(array('_id' => new MongoId($this->_id)));
		return $like['author'];
	}

	public function HasLiked($userid, $targetid, $targettype)
	{
		$cursor = $this->VirtualIDDB->Likes->find(array('author' => $userid, 'targetId' => $targetid, 'targetType' => $targettype));
		if($cursor->count() > 0)
		{
			$like = $cursor->next();
			return $like['_id'];
		}
		else
		{
			return false;
		}
	}

	public function GetAllPostLikes($postid)
	{
		return $this->VirtualIDDB->Likes->find(array('targetId' => $postid, 'targetType' => 'post'));
	}

	public function GetAllCommentLikes($commentid)
	{
		return $this->VirtualIDDB->Likes->find(array('targetId' => $commentid, 'targetType' => 'comment'));
	}

	public function DeleteLike()
	{
		$this->VirtualIDDB->Likes->remove(array('_id' => new MongoId($this->_id)));
	}

	public function GetUserLikes($userid)
	{
		return $this->VirtualIDDB->Likes->find(array('author' => $userid));
	}
}
?>
