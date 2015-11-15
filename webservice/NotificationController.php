<?php

require_once __DIR__ . '/../class/Notifications.php';
require_once __DIR__ . '/../class/PrivacyController.php';

/**
 * Description of NotificationController
 *
 * @author ids
 */
class NotificationController {
    public function authorize()
    {
        if(isset($_SESSION['user']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Get the user notifications
     *
     * @url GET /notifications
     */
    public function getNotifications()
    {
        $obj = new Notification();
        $cursor = $obj->GetUserNotifications((string)$_SESSION['user']['_id']);
        $cursor->sort(array('date' => -1));
        $rtnNotifs = array();
        foreach ( $cursor as $currentNotif )
        {
            $rtnNotifs[] = $currentNotif;
        }
        return $rtnNotifs;
    }
    
    /**
     * Set user notification to read status
     *
     * @url POST /notifications/$id
     */
    public function setNotificationRead($id)
    {
        $notifToUpdate = new Notification();
        $notifToUpdate->SetNotificationRead($id);
    }
}
