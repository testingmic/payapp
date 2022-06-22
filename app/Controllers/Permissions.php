<?php 
namespace App\Controllers;

use App\Models\FormsModel;

class Permissions {
    
    public function userData($session = null) {

        // set the session
        $session = !empty($session) ? $session : session();
        
        // if the user session is not empty
        if(empty($session->_userToken) || !isset($session->_userInfo['user_id'])) {
            return [];
        }

        // get the user data
        $formObject = new FormsModel();

        // get the logged in user
        $loggedInUser = $formObject->queryBuilder('users', ['user_id' => $session->_userInfo['user_id']], '*', '1')[0] ?? [];

        // if the user was not found
        if(empty($loggedInUser)) {
            return [];
        }
        
        // remove the password
        unset($loggedInUser['password']);

        // convert the permission to a json data
        $loggedInUser['permissions'] = json_decode($loggedInUser['permissions'], true);
        $loggedInUser['settings'] = json_decode($loggedInUser['settings'], true);
                
        return $loggedInUser;
    }

}
?>