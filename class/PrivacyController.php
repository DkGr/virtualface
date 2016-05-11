<?php

require_once __DIR__ . '/../class/User.php';
require_once __DIR__ . '/../class/Post.php';
require_once __DIR__ . '/../class/PrivacySettings.php';

/**
 * Description of PrivacyController
 *
 * PrivacyController filters user information gathering
 *
 * Example :
 * User1 want a User2 information.
 * So, User1 give his id to PrivacyController to access User2 informations
 * PrivacyController check User2 permission settings and return filtred informations to User1
 *
 * @author padman
 */
class PrivacyController {
    private static $maxPostLoad = 3;

    public static function pleaseShowMeUserInformation($myUserid, $userid)
    {
        switch(PrivacyController::getUserVisibilityTypeBetween($myUserid, $userid))
        {
            case VisibilityType::Me:
                $obj = new User();
                return $obj->load($userid);
            case VisibilityType::Friends:
                $obj = new User();
                $user = $obj->load($userid);
                $rtnUser = array();
                $rtnUser['_id'] = $user['_id'];
                $rtnUser['infos'] = array();
                $rtnUser['infos']['username'] = $user['infos']['username'];
                $rtnUser['public_key'] = $user['public_key'];

                foreach ($user['privacy_settings'] as $key => $value) {
                    if(($key == 'displayname') && (($value == VisibilityType::Friends)||($value == VisibilityType::Everybody)))
                    {
                        $rtnUser['infos']['displayname'] = $user['infos']['displayname'];
                    }
                    if(($key == 'email') && (($value == VisibilityType::Friends)||($value == VisibilityType::Everybody)))
                    {
                        $rtnUser['infos']['email'] = $user['infos']['email'];
                    }
                    if(($key == 'friends') && (($value == VisibilityType::Friends)||($value == VisibilityType::Everybody)))
                    {
                        $rtnUser['friends'] = array();
                        $rtnUser['friends'] = $user['friends'];
                    }
                }
                return $rtnUser;
            case VisibilityType::Everybody:
                $obj = new User();
                $user = $obj->load($userid);
                $rtnUser = array();
                $rtnUser['_id'] = $user['_id'];
                $rtnUser['infos'] = array();
                $rtnUser['infos']['username'] = $user['infos']['username'];

                foreach ($user['privacy_settings'] as $key => $value) {
                    if(($key == 'displayname') && ($value == VisibilityType::Everybody))
                    {
                        $rtnUser['infos']['displayname'] = $user['infos']['displayname'];
                    }
                    if(($key == 'email') && ($value == VisibilityType::Everybody))
                    {
                        $rtnUser['infos']['email'] = $user['infos']['email'];
                    }
                    if(($key == 'friends') && ($value == VisibilityType::Everybody))
                    {
                        $rtnUser['friends'] = array();
                        $rtnUser['friends'] = $user['friends'];
                    }
                }
                return $rtnUser;
        }
    }

    public static function pleaseShowMeUserPosts($myUserid, $userid)
    {
        switch(PrivacyController::getUserVisibilityTypeBetween($myUserid, $userid))
        {
            case VisibilityType::Me:
                $obj = new Post();
                $posts = $obj->GetUserPosts($userid);
                $rtnPosts = array();
                foreach ( $posts as $currentPost )
                {
                    $rtnPosts[] = $currentPost;
                }
                return $rtnPosts;
            case VisibilityType::Friends:
                $obj = new Post();
                $posts = $obj->GetUserPosts($userid);
                $rtnPosts = array();
                $posts->sort(array('date' => -1));
                if($posts->hasNext())
                {
                  foreach ( $posts as $currentPost )
                  {
                    if(($currentPost['visibility'] == VisibilityType::Friends)||($currentPost['visibility'] == VisibilityType::Everybody))
                    {
                        $rtnPosts[] = $currentPost;
                    }
                  }
                }
                return $rtnPosts;
            case VisibilityType::Everybody:
                $obj = new Post();
                $posts = $obj->GetUserPosts($userid);
                $rtnPosts = array();
                $posts->sort(array('date' => -1));
                if($posts->hasNext())
                {
                  foreach ( $posts as $currentPost )
                  {
                    if($currentPost['visibility'] == VisibilityType::Everybody)
                    {
                        $rtnPosts[] = $currentPost;
                    }
                  }
                }
                return $rtnPosts;
        }
    }

    public static function pleaseShowMyStreamPosts($myUserid)
    {
        $rtnStream = array();
        $obj = new User();
        $obj->setId($myUserid);
        $friends = $obj->GetFriends();
        foreach ($friends as $frienid => $isFriend) {
            if($isFriend)
            {
              $rtnStream = array_merge($rtnStream, PrivacyController::pleaseShowMeUserPosts($myUserid, $frienid));
            }
        }
        $rtnPosts = array_merge($rtnStream, PrivacyController::pleaseShowMeUserPosts($myUserid, $myUserid));
        $rtnPosts = PrivacyController::posts_sort($rtnPosts, 'date', SORT_DESC);
        return $rtnPosts;
    }

    public static function pleaseShowMyStreamPostsAfter($myUserid, $lastPostid)
    {
        $rtnStream = array();
        $obj = new User();
        $obj->setId($myUserid);
        $friends = $obj->GetFriends();
        foreach ($friends as $frienid => $isFriend) {
            if($isFriend)
            {
              $rtnStream = array_merge($rtnStream, PrivacyController::pleaseShowMeUserPosts($myUserid, $frienid));
            }
        }
        $rtnPosts = array_merge($rtnStream, PrivacyController::pleaseShowMeUserPosts($myUserid, $myUserid));
        $rtnPosts = PrivacyController::posts_sort($rtnPosts, 'date', SORT_DESC, $lastPostid);
        return $rtnPosts;
    }

    public static function pleaseShowMePost($myUserid, $postid)
    {
        $post = new Post();
        $post->setId($postid);
        $rtnPost = null;
        if($post->GetPost() != null)
        {
            if(PrivacyController::getUserVisibilityTypeBetween($myUserid, (string)$post->getAuthor()) <= $post->GetPostVisibility())
            {
                $rtnPost = $post->GetPost();
            }
        }
        return $rtnPost;
    }

    public static function pleaseShowMePostComments($myUserid, $postid)
    {
        $obj = new Post();
        $obj->setId($postid);
        if(PrivacyController::getUserVisibilityTypeBetween($myUserid, (string)$obj->getAuthor()) <= $obj->GetPostVisibility())
        {
            $comObj = new Comment();
            $comments = $comObj->GetAllPostComments($postid);
            $rtnComments = array();
            $comments->sort(array('date' => 1));
            if($comments->hasNext())
            {
              foreach ( $comments as $currentComment )
              {
                $rtnComments[] = $currentComment;
              }
            }
            return $rtnComments;
        }
        else{
            return array('error' => 'Acces forbidden');
        }
    }

    public static function pleaseShowMePostLikes($myUserid, $postid)
    {
        $obj = new Post();
        $obj->setId($postid);
        if(PrivacyController::getUserVisibilityTypeBetween($myUserid, (string)$obj->getAuthor()) <= $obj->GetPostVisibility())
        {
            $likeCollection = new Like();
            $postLikesCursor = $likeCollection->GetAllPostLikes((string)$postid);
            $rtnLikes = array();
            $postLikesCursor->sort(array('date' => 1));
            if($postLikesCursor->hasNext())
            {
              foreach ( $postLikesCursor as $currentLike )
              {
                $rtnLikes[] = $currentLike;
              }
            }
            return $rtnLikes;
        }
        else{
            return array('error' => 'Acces forbidden');
        }
    }

    public static function pleaseShowMeCommentLikes($myUserid, $commentid)
    {
        $obj = new Comment();
        $obj->setId($commentid);
        $obj2 = new Post();
        $obj2->setId($obj->getTargetId());
        if(PrivacyController::getUserVisibilityTypeBetween($myUserid, (string)$obj2->getAuthor()) <= $obj2->GetPostVisibility())
        {
            $likeCollection = new Like();
            $postLikesCursor = $likeCollection->GetAllCommentLikes((string)$commentid);
            $rtnLikes = array();
            $postLikesCursor->sort(array('date' => 1));
            if($postLikesCursor->hasNext())
            {
              foreach ( $postLikesCursor as $currentLike )
              {
                $rtnLikes[] = $currentLike;
              }
            }
            return $rtnLikes;
        }
        else{
            return array('error' => 'Acces forbidden');
        }
    }

    private static function getUserVisibilityTypeBetween($userid1, $userid2)
    {
        $obj = new User();
        $obj->setId($userid2);
        //test if they are friends
        if($obj->IsMyFriend((string)$userid1))
        {
            return VisibilityType::Friends;
        }
        elseif ($userid1 == $userid2) {
            return VisibilityType::Me;
        }
        else{
            return VisibilityType::Everybody;
        }
    }

    private static function posts_sort($array, $on, $order=SORT_ASC, $afterid=0)
    {
        $new_array = array();
        $sortable_array = array();

        $currentPostAdded = 0;

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }

            $foundAfterPost = false;
            foreach ($sortable_array as $k => $v) {
                if($currentPostAdded >= PrivacyController::$maxPostLoad)
                {
                  break;
                }
                else{
                  if($afterid === 0)
                  {
                    $new_array[] = $array[$k];
                    $currentPostAdded++;
                  }
                  else{
                    if ($afterid === (string)$array[$k]['_id']) {
                      $foundAfterPost = true;
                      continue;
                    }
                    if($foundAfterPost)
                    {
                      $new_array[] = $array[$k];
                      $currentPostAdded++;
                    }
                  }
                }
            }
        }

        return $new_array;
    }
}
