<?php 
namespace App\Controllers;

use App\Controllers\Controller;

class Support extends Controller {

    /**
     * Use this method to process all requests to the endpoint
     * 
     * POST, PUT, GET & DELETE
     * 
     * 
     * @return Mixed
     */
    public function contact($params = []) {
        
        if(empty($params) || !is_array($params)) {
            return [];
        }
        
        // if the name is empty
        if(empty($params['name'])) {
            return 'Sorry! Your fullname is required.';
        }

        // process the request
        if(!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            return 'A valid remail address is required.';
        }
        
        // if the comment is empty
        if(empty($params['comments'])) {
            return 'Sorry! The message field cannot be empty.';
        }

        // format the data to insert
        $contact_info = [
            'name' => $params['name'],
            'email' => $params['email'],
            'subject' => $params['subject'] ?? null,
            'comments' => substr($params['comments'], 0, 1000),
            'ip_address' => $params['ip_address'],
            'user_agent' => $params['user_agent'],
        ];

        // insert the form data
        $query = $this->formObject->insertRow('support_contact', $contact_info);

        if(empty($query)) {
            return 'Sorry! An error occurred while processing the request. Contact the Admin if problem persists.';
        }

        // remove the session
        $params['ci_session']->remove('form_hash');
        
        return ['code' => 200, 'data' => 'Message successfully sent. A personnel will get in touch shortly.'];

    }

}
?>