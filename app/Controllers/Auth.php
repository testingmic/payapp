<?php
namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\AuthModel;
use App\Controllers\Cronjob;

class Auth extends Controller {

	public $session;
	private $locked_out_time = 30;

	/**
	 * Get the user login data
	 * 
	 * @return Array
	 */
	public function user_data() {}

	/**
	 * Index Page for Authentication Check
	 * 
	 * @return page
	 */
	public function index() {

		// if not logged in then redirect to the Login Page
		if(empty($this->session->adminLoggedIn)) {
			header("location: ".base_url("auth/login"));
			exit;
		}

		// else redirect to the dashboard
		header("location: " . base_url("dashboard"));

		// exit the page
		exit;
	}

	/**
	 * Display the Login Page
	 * 
	 * @return page
	 */
	public function login() {
		// no validation check
		$data['no_validation_check'] = true;
		$data['classelement'] = "container-fluid";

		// display the login page
		echo view("auth_login.php", $data);
	}

	/**
	 * Display the Reset Password Page
	 * 
	 * @return page
	 */
	public function reset() {
		// no validation check
		$data['no_validation_check'] = true;
		// display the login page
		echo view("auth_reset_password.php", $data);
	}

	/**
	 * Display the Two Step Authentication Page
	 * 
	 * @return page
	 */
	public function twostep() {
		// no validation check
		$data['no_validation_check'] = true;
		// display the login page
		echo view("auth_two_step.php", $data);
	}

	/**
	 * Display the Lockscreen Page
	 * 
	 * @return page
	 */
	public function lockscreen() {
		
		// no validation check
		$data['no_validation_check'] = true;

		// if not logged in then redirect to the Login Page
		if(empty($this->session->adminLoggedIn)) {
			// return view("auth_login.php", $data);
		}
		
		// display the Lockscreen page
		echo view("auth_lockscreen.php", $data);
	}

	/**
	 * Confirm Login Status Using
	 * 
	 * Get the Parameters $adminLoggedIn
	 * 
	 * @return Object
	 */
	final function is_loggedin() {
		
		// session service
		$session = \Config\Services::session();

		// confirm if the adminLoggedIn session has been parsed
		if(!empty($session->adminLoggedIn) && empty($session->adminIsLocked)) {
			// redirect the user to the dashboard page
			header("location: ".base_url("dashboard"));
			exit;
		}

		return $session;
	}

	/**
	 * Confirm Login Status Using Sessions
	 * 
	 * Get the Parameters $adminLoggedIn
	 * 
	 * @return Object
	 */
	final function login_check($page = 'dashboard') {
		
		// session service
		$session = \Config\Services::session();
		$data['classelement'] = "container-fluid";

		// confirm if the lockscreen session has been set
		if(!empty($session->adminIsLocked)) {
			// no validation check
			$data['no_validation_check'] = true;
			// display the login page
			echo view("auth_lockscreen", $data);
			exit;
		}

		// confirm if the adminLoggedIn session has been parsed
		if(empty($session->adminLoggedIn)) {
			// no validation check
			$data['no_validation_check'] = true;
			// display the login page
			echo view("auth_login", $data);
			exit;
		}

		// set the activity log
		$session->set('_last_activity_timer', time());

		return $session;
	}

	/**
	 * Validate the User Password by loading the userdata using the _userId session value
	 * 
	 * @param String $params['password']
	 * 
	 * @return Array
	 */
	final function unlock(array $params) {

		// trim the user password to 32 characters long
		$password = substr($params['password'], 0, 32);

		// confirm that the user is not empty
		if(empty($password)) {
			return apis('auth.required');
		}

		// create a new object of the ApiServices Class
		$ApiObject = new AuthModel();

		// set the login param
		$login_param = [
			'username' => $params['ci_session']->_userEmail,
			'password' => $password,
			'user_agent' => $params['user_agent'],
			'ip_address' => $params['ip_address']
		];

		// test the login
		$ApiLogin = $ApiObject->verify_login($login_param);
		
		// test the login response
		if(isset($ApiLogin['token'])) {

			// remove the session value
			$params['ci_session']->remove(['adminIsLocked']);

			// set the session
			$params['ci_session']->set([
				'_userInfo' => $ApiLogin['user'],
				'_userToken' => $ApiLogin['token']
			]);

			// return the redirection
			return [
				'code' => 200,
				'data' => [
					'result' => 'You have successfully logged in.',
					'additional' => [
						'href' => base_url('dashboard')
					]
				]
			];
		}

		return apis('auth.invalid');

	}

	/**
	 * Authenticate User Credentials And Login
	 * 
	 * Set the necessary sessions when confirmed
	 * 
	 * @param String $params['username']
	 * @param String $params['password']
	 * 
	 * @return Array
	 */
	final function _login(array $params = []) {

		// if the email address or password are empty
		$username = substr($params['username'], 0, 64);
		$password = substr($params['password'], 0, 32);

		// confirm that the user is not empty
		if(empty($username) || empty($password)) {
			return apis('auth.required');
		}
		
		try {

			// create a new object of the ApiServices Class
			$ApiObject = new AuthModel();
			
			// set the login param
			$login_param = [
				'username' => $username,
				'password' => $password,
				'user_agent' => $params['user_agent'],
				'ip_address' => $params['ip_address']
			];

			// test the login
			$ApiLogin = $ApiObject->verify_login($login_param);
			
			// test the login response
			if(isset($ApiLogin['token'])) {

				// successfully logged in
				$code = 200;

				// set the session
				$params['ci_session']->set([
					'_userEmail' => $username,
					'_userInfo' => $ApiLogin['user'],
					'adminLoggedIn' => random_string(),
					'_userToken' => $ApiLogin['token'],
					'_userId' => $ApiLogin['user']['id'],
					'_clientId' => $ApiLogin['user']['client_id']
				]);

				$params['ci_session']->remove(['_uToken']);

			} else {
				// invalid username or password
				return apis('auth.invalid');
			}

			// return the success results
			return [
				'code' => $code ?? 203,
				'data' => [
					'result' => 'You have successfully logged in.',
					'additional' => [
						'href' => base_url('dashboard'),
						'access_token' => $ApiLogin['token']
					]
				]
			];

		} catch(\Exception $e) {}

	}

	/**
	 * Log the user out of the system
	 * 
	 * @return Bool
	 */
	final function logout(array $params) {

		// set the token again
		$params['ci_session']->set('_uToken', $params['ci_session']->_userToken);

		// unset the sessions for
		$params['ci_session']->remove([
			'_last_activity_timer', 'adminLoggedIn', 'adminIsLocked', 
			'_userEmail', '_userToken', 'userId', '_clientId', '_userInfo', '_userPermission'
		]);
		$params['ci_session']->destroy();

		// return the success message
		return ['code' => 200, 'data' => apis('auth.logged_out')];

	}

	/**
	 * Lockscreen display after 10 minutes of inactivity
	 * 
	 * @return Bool
	 */
	final function _locked(array $params = []) {

		// set the locked value in session
		$params['ci_session']->set(['adminIsLocked' => true, '_userToken' => null]);

	}

	/**
	 * Ajax Cronjob
	 * This function is the root to perform loads of actions that have been saved in session
	 * First is to Check the time difference between the last activity and now and to lock the user out 
	 * when the user has been inactive for 10 minutes
	 * 
	 * @param Array 	$params
	 * 
	 * @return Array
	 */
	final function ajax_cronjob(array $params) {
		
		// get the login session
		$isLoggedIn = $params['ci_session']->adminLoggedIn;
		$lastActivity = $params['ci_session']->_last_activity_timer;

		// if logged out then redirect to the login page
		if(empty($isLoggedIn)) {
			return [
				'data' => [
					'result' => 'You have been logged out from the system.',
					'additional' => [
						'href' => base_url('auth/login')
					]
				]
			];
		}

		// if the last activity is empty
		if(!empty($lastActivity)) {

			// convert the time to normal
			$lastActivity = date("Y-m-d H:i", strftime($lastActivity));

			// get the time difference
			$minuteDifference = raw_time_diff($lastActivity);

			// compare if the time difference is greater than 9 minutes
			if($minuteDifference > $this->locked_out_time) {
				// log the user out
				$this->_locked($params);

				// return message
				return [
					'code' => 200,
					'data' => [
						'result' => 'You have been locked out due to inactivity',
						'additional' => [
							'href' => base_url('auth/lockscreen')
						]
					]
				];
			}

		}

		// process all other request
		$cronjob = new Cronjob();
		$cronjob->index($params);

		// return message
		return ['code' => 200, 'data' => time()];

	}

}
?>