<?php

require_once __DIR__ . '/../class/User.php';
require_once __DIR__ . '/../class/Post.php';
require_once __DIR__ . '/../class/PrivacyController.php';

/**
 * Description of PostController
 *
 * @author padman
 */
class PostController {
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
     * Save the user post
     *
     * @url POST /posts
     */
    public function saveUserPost($data)
    {
        $obj = new Post();
        $post = $obj->CreateNew($_SESSION['user']['_id'], date("Y/m/d H:i:s"), $data->{'visibility'}, $data->{'content'});
        if($post)
        {
            return $post; // returning the newly created user object
        }
        else{
            return array('error' => "Erreur lors de l'enregistrement");
        }
    }
    
    /**
     * Delete the user post
     *
     * @url POST /posts/delete
     */
    public function deleteUserPost($data)
    {
        $obj = new Post();
        $obj->setId($data->{'id'});
        if((string)$_SESSION['user']['_id'] == (string)$obj->getAuthor())
        {
            $obj->DeletePost();
            return array('success' => "Element supprimé");
        }
        else{
            return array('error' => "Vous n'avez pas l'autorisation de supprimer cet élément");
        }
    }
    
    /**
     * Gets the user stream posts
     *
     * @url GET /posts
     */
    public function getStreamPosts()
    {
        $posts = PrivacyController::pleaseShowMyStreamPosts((string)$_SESSION['user']['_id']);
        return $posts;
    }
    
    /**
     * Gets the user stream posts
     *
     * @url GET /posts/$id/
     */
    public function getPost($id)
    {
        $post = PrivacyController::pleaseShowMePost((string)$_SESSION['user']['_id'], $id);
        return $post;
    }
    
    /**
     * Gets the posts comment by id
     *
     * @url GET /posts/$id/comments
     */
    public function getPostComments($id)
    {
        $comments = PrivacyController::pleaseShowMePostComments((string)$_SESSION['user']['_id'], $id);
        return $comments;
    }
    
    /**
     * Gets the posts likes by id
     *
     * @url GET /posts/$id/likes
     */
    public function getPostLikes($id)
    {
        $likes = PrivacyController::pleaseShowMePostLikes((string)$_SESSION['user']['_id'], $id);
        return $likes;
    }
}
