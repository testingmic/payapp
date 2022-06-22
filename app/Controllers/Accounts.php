<?php
namespace App\Controllers;

class Accounts extends Controller {

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

        // confirm if the account_id should be included
        if(!empty($params['account_number'])) {
            $filters['account_number'] = (string) $params['account_number'];
        }

        // confirm if the account_type should be included
        if(!empty($params['account_type'])) {
            $filters['account_type'] = (string) $params['account_type'];
        }

        // get the accounts list
        return $this->formObject->queryBuilder('accounts', $filters, '*');
    }

}