<?php
namespace App\Controllers;

class Members extends Controller {

    public function single($params = []) {

        // confirm if the client_id should be included
        if(!empty($params['client_id'])) {
            $filters['client_id'] = (string) $params['client_id'];
        }

        // confirm if the user_id should be included
        if(!empty($params['user_id'])) {
            $filters['user_id'] = (string) $params['user_id'];
        }

        // confirm if the status should be included
        if(!empty($params['status'])) {
            $filters['status'] = (string) $params['status'];
        }

        // confirm if the user_type should be included
        if(!empty($params['user_type'])) {
            $filters['user_type'] = (string) $params['user_type'];
        }

        // confirm if the contact should be included
        if(!empty($params['contact'])) {
            $filters['contact'] = (string) $params['contact'];
        }

        // confirm if the email should be included
        if(!empty($params['email'])) {
            $filters['email'] = (string) $params['email'];
        }

        // get the users list
        return $this->formObject->queryBuilder('users', $filters, '*');
    }

}