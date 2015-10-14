<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
/** mongodb Comment collection structure
 * {
 *     "_id" : ObjectId("xxxxxxxxxxxxxxxxxxxxxxxx"),
 *		 "postid" : xxxxxxxxxxxxxxxxxxxxxxxx,
 *     "author" : xxxxxxxxxxxxxxxxxxxxxxxx,
 *		 "date" : "yyyy-MM-dd HH:mm:ss",
 *     "content" : "text",
 *     "likes" : array{ LikeID, ... }
 * }
 */

class Comment {
	private $_id;
	private $VirtualIDDB;

	public function __construct(){
		$connexion = new MongoClient();
		$this->VirtualIDDB = $connexion->VirtualID;
  }

	public function CreateNew($postid, $userid, $date, $content)
	{
		$newpost = array('postid' => $postid,
										 'author' => $userid,
										 'date' => $date,
										 'content' => $content,
										 'likes' => array()
								 );
		$this->VirtualIDDB->Comments->insert($newpost);
	}

	public function setId($id)
	{
		$this->_id = (string)$id;
	}

	public function getAuthor()
	{
		$post = $this->VirtualIDDB->Comments->findOne(array('_id' => new MongoId($this->_id)));
		return $post['author'];
	}

	public function GetAllPostComments($postid)
	{
		return $this->VirtualIDDB->Comments->find(array('postid' => $postid));
	}

	public function DeleteComment()
	{
		$this->VirtualIDDB->Comments->remove(array('_id' => new MongoId($this->_id)));
	}

	public function GetUserComments($userid)
	{
		return $this->VirtualIDDB->Comments->find(array('author' => $userid));
	}
}
?>
