<?php 
namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model {
    
    public $db;

    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    /**
     * Verify the user login credentials
     * 
     * @param Array     $params
     * @param String    $params['username']
     * @param String    $params['password']
     * @param String    $params['ip_address']
     * @param String    $params['user_agent']
     * 
     * @return Array
     */
    public function verify_login(array $params) {

        // confirm that username has been set
        if(empty($params['username'])) {
            return [];
        }

        // the builder
        $builder = $this->db->table('users')
                            ->select('id, client_id, user_id, username, email, password, 
                                firstname, lastname, two_factor_auth, image, settings, contact')
                            ->where('email', $params['username'])
                            ->where('status', '1')
                            ->orWhere('username', $params['username'])
                            ->get(1);
        
        // get result as array
        $result = $builder->getResultArray();
        
        // confirm if the data is empty
        if(empty($result)) {
            return [];
        }

        // if not set _skip_password_validation
        if(empty($params['_skip_password_validation'])) {

            // confirm that the password is not empty
            if(empty($params['username'])) {
                return [];
            }

            // check the password
            if(!password_verify($params['password'], $result[0]['password'])) {
                return [];
            }

            // unset the password
            unset($result[0]['password']);

            // convert the settings value into an array
            $result[0]['settings'] = !empty($result[0]['settings']) ? json_decode($result[0]['settings'], true) : [];

            // generate an access token
            $token = random_string('alnum', 54);

            // insert the user login history
            $this->db->table('users_login_history')->insert([
                'token' => $token, 'ip_address' => $params['ip_address'], 
                'token_expiry' => date('Y-m-d h:i:s', strtotime('+24 hour')),
                'user_agent' => $params['user_agent'], 'user_id' => $result[0]['user_id'], 
                'client_id' => $result[0]['client_id']
            ]);

            // update the user record
            $this->db->table('users')
                    ->set(['last_login' => date("Y-m-d H:i:s")])
                    ->where('id', $result[0]['id'])
                    ->update();

        }

        // return the result
        return [
            'token' => $token ?? ($params['token'] ?? []),
            'user' => $result[0]
        ];

    }

}
?>