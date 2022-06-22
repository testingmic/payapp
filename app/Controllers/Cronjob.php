<?php
namespace App\Controllers;

use App\Controllers\Controller;

class Cronjob extends Controller {

    
    public function index(array $params) {

		// if the themecolor has been parsed
		if(!empty($params['themecolor'])) {

			// confirm the colors
			if(!in_array($params['themecolor'], ['dark', 'light'])) {
                return [];
            }

            // user session
            $user = $params['ci_session']->_userInfo;
            $user['settings']['themecolor'] = $params['themecolor'];

			// set this color in session
			$params['ci_session']->set('_userInfo', $user);

            // save this in the users table
            $this->formObject->updateRow(
                'users', 
                ['settings' => json_encode($params['ci_session']->_userInfo['settings'])],
                ['member_id' => $params['ci_session']->_userInfo['member_id']]
            );

		}

    }

}