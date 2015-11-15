<?php

require_once __DIR__ . '/../class/User.php';
require_once __DIR__ . '/../class/Post.php';
require_once __DIR__ . '/../class/Comment.php';
require_once __DIR__ . '/../class/PrivacyController.php';

/**
 * Description of CommentController
 *
 * @author padman
 */
class CommentController {
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
     * Save the user comment
     *
     * @url POST /comments
     */
    public function saveComment($data)
    {
        $obj = new Comment();
        $comment = $obj->CreateNew($_SESSION['user']['_id'], $data->{'postid'}, date("Y/m/d H:i:s"), $data->{'content'});
        if($comment)
        {
            return $comment; // returning the newly created user object
        }
        else{
            return array('error' => "Erreur lors de l'enregistrement");
        }
    }
    
    /**
     * Save the user post
     *
     * @url POST /comments/delete
     */
    public function deleteUserComment($data)
    {
        $obj = new Comment();
        $obj->setId($data->{'id'});
        if((string)$_SESSION['user']['_id'] == (string)$obj->getAuthor())
        {
            $obj->DeleteComment();
            return array('success' => "Element supprimé");
        }
        else{
            return array('error' => "Vous n'avez pas l'autorisation de supprimer cet élément");
        }
    }
}
