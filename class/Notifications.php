<?php

/** mongodb Notification collection structure
 * {
 *     "_id" : ObjectId("xxxxxxxxxxxxxxxxxxxxxxxx"),
 *		 "recipient" : UserID,
 *		 "date" : "yyyy-MM-dd HH:mm:ss",
 *     "content" : "text",
 *     "read" : true|false
 * }
 */

class Notification {
	private $VirtualIDDB;

	public function __construct(){
            include 'DatabaseConnect.php';
        }

	public function CreateNew($userid, $date, $content)
	{
            $newnotif = array('recipient' => $userid,
                            'date' => $date,
                            'content' => $content,
                            'read' => false
            );
            $this->VirtualIDDB->Notifications->insert($newnotif);
	}

	public function GetUserNotifications($userid)
	{
		return $this->VirtualIDDB->Notifications->find(array('recipient' => $userid));
	}

	public function SetNotificationRead($notifid)
	{
		$this->VirtualIDDB->Notifications->update(
	    array( '_id' => new MongoId($notifid) ),
	    array( '$set' => array('read' => true) )
		);
	}
}
?>
