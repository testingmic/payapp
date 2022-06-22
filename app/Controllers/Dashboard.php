<?php
namespace App\Controllers;

use App\Controllers\Accounts;
use App\Controllers\Members;

class Dashboard extends Controller {
    
    public function index() {

        $data['pagetitle'] = 'Dashboard';

        // if the userData variable is not empty
        if(!empty($this->userData)) {
            
            // create an account object
            $accountObj = new Accounts();

            // set the filter
            $filter = [
                'status' => 1,
                'user_id' => $this->userData['user_id'],
                'client_id' => $this->userData['client_id']
            ];

            // get the accounts list
            $data['accounts'] = $accountObj->single($filter);


            // if the user has permission to list all Members
            if($this->hasAccess('members', 'list')) {
                // create new Members object
                $membersObj = new Members();

                // set the filter
                $filter = [
                    'status' => 1,
                    'client_id' => $this->userData['client_id']
                ];

                // get the accounts list
                $data['members'] = $membersObj->single($filter);
            }

        }

        return view('dashboard', $data);
        
    }

    public function splash() {

        $data['noheader'] = true;
        $data['pagetitle'] = 'Welcome!';
        
        return view('splash', $data);
        
    }

    public function session() {
        echo json_encode($_SESSION);
    }
    
}
?>