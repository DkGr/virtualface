<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
/** mongodb Comment collection structure
 * {
 *     "_id" : ObjectId("xxxxxxxxxxxxxxxxxxxxxxxx"),
 *     "postid" : xxxxxxxxxxxxxxxxxxxxxxxx,
 *     "author" : xxxxxxxxxxxxxxxxxxxxxxxx,
 *     "date" : "yyyy-MM-dd HH:mm:ss",
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

	public function CreateNew($userid, $postid, $date, $content)
	{
            $post = new Post();
            $post->setId($postid);
            $postAuthor = new User();
            $postAuthor->setId((string)$post->getAuthor());
            $newcomment = array('postid' => $postid,
                                'author' => $userid,
                                'date' => $date,
                                'content' => $content,
                                'likes' => array()
                               );
            $this->VirtualIDDB->Comments->insert($newcomment);
            if($userid != $postAuthor->getId())
            {
                $commentAuthor = new User();
                $commentAuthor->setId($userid);
                $notif = new Notification();
                $notif->CreateNew($postAuthor->getId(), date("Y/m/d H:i:s"), '<a href="identity.php?userid='.$userid.'">'.$commentAuthor->getUsername().'</a> a commenté votre publication');
            }
            return $newcomment;
	}

	public function setId($id)
	{
		$this->_id = (string)$id;
	}

        public function getTargetId()
	{
		$comment = $this->VirtualIDDB->Comments->findOne(array('_id' => new MongoId($this->_id)));
		return $comment['postid'];
	}
        
	public function getAuthor()
	{
		$comment = $this->VirtualIDDB->Comments->findOne(array('_id' => new MongoId($this->_id)));
		return $comment['author'];
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
