<?php

require_once __DIR__ . '/../class/User.php';
require_once __DIR__ . '/../class/Post.php';
require_once __DIR__ . '/../class/Comment.php';
require_once __DIR__ . '/../class/Like.php';
require_once __DIR__ . '/../class/PrivacyController.php';

/**
 * Description of LikeController
 *
 * @author padman
 */
class LikeController {
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
     * Save the user like
     *
     * @url POST /likes
     */
    public function saveUserLke($data)
    {      
        $newlike = new Like();
        $newlike->CreateNew($_SESSION['user']['_id'], $data->{'targettype'}, $data->{'targetid'}, date("Y/m/d H:i:s"));
    }
    
    /**
     * Delete the user like
     *
     * @url POST /likes/delete
     */
    public function deleteUserLike($data)
    {
        $likeToDelete = new Like();
        $likeToDelete->setId($data->{'likeid'});
        $likeToDelete->DeleteLike();
    }
}
