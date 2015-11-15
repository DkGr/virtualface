<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
/** mongodb Post collection structure
 * {
 *     "_id" : ObjectId("xxxxxxxxxxxxxxxxxxxxxxxx"),
 *		 "author" : UserID,
 *		 "date" : "yyyy-MM-dd HH:mm:ss",
 *     "visibility" : array { Public, VirtualID, Friends, UserID, ... },
 *     "content" : "text",
 *     "comments" : array{ CommentID, ... },
 *     "likes" : array{ LikeID, ... }
 * }
 */

class Post {
	private $_id;
	private $VirtualIDDB;

	public function __construct(){
		$connexion = new MongoClient();
		$this->VirtualIDDB = $connexion->VirtualID;
  }

	public function CreateNew($userid, $date, $visibility, $content)
	{
		$newpost = array('author' => $userid,
										 'date' => $date,
										 'visibility' => $visibility,
										 'content' => $content,
										 'comments' => array(),
										 'likes' => array()
								 );
		$this->VirtualIDDB->Posts->insert($newpost);
	}

	public function setId($id)
	{
		$this->_id = (string)$id;
	}

	public function getAuthor()
	{
		$post = $this->VirtualIDDB->Posts->findOne(array('_id' => new MongoId($this->_id)));
		return $post['author'];
	}

	public function GetAllPosts()
	{
		return $this->VirtualIDDB->Posts->find();
	}

	public function DeletePost()
	{
		$this->VirtualIDDB->Posts->remove(array('_id' => new MongoId($this->_id)));
	}

	public function GetUserPosts($userid)
	{
		return $this->VirtualIDDB->Posts->find(array('author' => $userid));
	}
}
?>
