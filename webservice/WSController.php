<?php

require_once __DIR__ . '/../class/User.php';
require_once __DIR__ . '/../class/PrivacyController.php';
use \Jacwright\RestServer\RestException;
/**
 * Description of WSController
 *
 * @author padman
 */
class WSController {
    
    /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     *
     * @url GET /
     */
    public function test()
    {
        return "Welcome to the VirtualID Web Service. Please read official VirtualID Developper Documentation to use it.";
    }
    
    /**
     * Logs in a user with the given username and password POSTed. Though true
     * REST doesn't believe in sessions, it is often desirable for an AJAX server.
     *
     * @url POST /login
     */
    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $obj = new User();
        if($obj->loginUser($username, $password))
        {
            $_SESSION['user'] = PrivacyController::pleaseShowMeUserInformation($obj->getId(), $obj->getId());
            return array("success" => "Logged in " . $username);
        }
        else
        {
            return array("error" => "Nom d'utilisateur ou mot de passe incorrect");
        }
    }

    /**
     * Throws an error
     * 
     * @url GET /error
     */
    public function throwError() {
        throw new RestException(401, "Empty password not allowed");
    }
}
