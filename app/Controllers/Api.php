<?php

namespace App\Controllers;

use App\Models\ApiModel;
use App\Models\FormsModel;
use CodeIgniter\API\ResponseTrait;

class Api extends BaseController {

    use ResponseTrait;

    protected $_userId = 'payapp';
    protected $_clientId = 'payapp';
    protected $accessCheck = [];
    protected $_userData;

    protected $request;
    protected $api_model;
    protected $endpoints;
    protected $outer_url;
    protected $inner_url;
    protected $req_method;
    protected $req_params;
    protected $default_params;
    protected $request_endpoint;
    protected $global_limit = 250;
    protected $AuthorizationToken;

    // keys to exempt when processing the request parameter keys
    protected $keys_exempted = [
        "the_button", "faketext", "faketext_2", "remote", "message", "limit", "date_range", 
        "ip_address", "ci_session", "methcens_csrf_token", "csrf_cookie_name", "files", "user_agent"
    ];

    // keys to bypass when checking for the csrf_token
    protected $bypass_csrf_token_list = ['_logout', 'ajax_cronjob'];

    public function __construct() {

        $this->request = \Config\Services::request();
        $this->api_model = new ApiModel;

    }

    /**
     * Receive the incoming request and process it.
     * 
     * It will validate the Api Keys, Request Endpoint & Request Parameters
     * The resulting data will be processed and returned as a JSON
     * 
     * @param String $inner_url
     * @param String $outer_url
     * 
     * @return Array
     */
    public function index($init = null, string $inner_url = null, string $outer_url = null, string $record_id = null) {

        // get the request method
        $this->req_method = strtoupper($_SERVER['REQUEST_METHOD']);

        // set the full endpoint url and breakdown
        $this->request_endpoint = "{$inner_url}/{$outer_url}";
        $this->inner_url = $inner_url;
        $this->outer_url = $outer_url;

        // get the request path
        $this->req_path = $this->request->getPath();

        // get the variables
        $post_get_params = $this->request->getVar();
        $post_get_params = array_map('esc', (array) $post_get_params);
        
        // set the files list
        $files_list = $this->request->getFiles();

        // merge the post, get and files items
        $t_params = array_merge($post_get_params, $files_list);

        // set the dataset in an array
        if( !in_array($outer_url, ['ajax_cronjob']) ) {
            $this->session->set('_request_params', $post_get_params);
        }

        // get the user agent
        $userAgent = $this->request->getUserAgent();

        // set the user agent
        $t_params['user_agent'] = $userAgent->__toString();
        $t_params['ip_address'] = $this->request->getIPAddress();
        $t_params['ci_session'] = $this->session;

        // get the authorization header parsed token
        $authToken = $this->request->header('Authorization');

        // set the authorization token
        if(!empty($authToken)) {

            // set the authorization token
            $this->AuthorizationToken = $authToken->getValue();
            
            // swap the inner url to the list method and set the init value as the record id
            $t_params['record_id'] = $outer_url !== 'list' ? $outer_url : null;

            // If the request is a POST request and the outer url is not empty
            if(in_array($this->req_method, ['POST']) && !empty($outer_url)) {
                // return invalid
                $this->outer_url = '_invalid_parameter';
            }

            // if the request is a PUT request and the outer url is empty
            elseif(in_array($this->req_method, ['PUT']) && empty($outer_url)) {
                $this->outer_url = '_mission_parameter';
            }

            // else then set the item
            else {
                // swap the outer url to list
                $this->outer_url = in_array($this->req_method, ['PUT', 'POST']) ? 'save' : 'list';
            }

            // set the new request endpoint
            $this->request_endpoint = "{$inner_url}/{$this->outer_url}";

        }

        // set the default parameters
        $this->req_params = $t_params;

        /** Process the API Request */
        $make_request = $this->initRequest($t_params);

        // set the data to return
        return $this->respond($make_request, 200);
        
    }

    /**
     * Load the endpoint information for processing
     * 
     * @param Array $params         This is an array of the parameters parsed in the request
     * @param String $method        The request method
     * 
     * @return Array
     */
    private function initRequest($params) {

        /** Split the endpoint */
        $expl = explode("/", $this->request_endpoint);
        $endpoint = isset($expl[1]) && !empty($expl[1]) ? strtolower($this->request_endpoint) : null;

        // trim the edges
        $endpoint = trim($endpoint, '/');

        /* Run a check for the parameters and method parsed by the user */
        $paramChecker = $this->keysChecker($params);

        // if an error was found
        if( $paramChecker['code'] !== 100) {
            // set it if not existent
            $paramChecker['description'] = $paramChecker['description'] ?? null;

            // check the message to parse
            $paramChecker['data']['result'] = $paramChecker['data']['result'] ?? $paramChecker['description'];

            // print the json output
            return $paramChecker;
        }

        // run the request
        $ApiRequest = $this->requestHandler($params);

        // set the data if no data was set
        if(!isset($ApiRequest['data']['result'])) {
            $ApiRequest['data']['result'] = $ApiRequest['description'];
        }
        
        // remove access token if in
        if(isset($params["access_token"])) {
            unset($params["access_token"]);
        }

        // print out the response
        return $ApiRequest;
    }

    /**
     * This method checks the params parsed by the user
     * 
     *  @param {array} $params  This is the array of parameters sent by the user
    */
    private function keysChecker(array $params) {
        
        /** Load the Endpoint JSON File **/
        $db_req_params = json_decode(file_get_contents(WRITEPATH . 'data/endpoints.json'), true);

        /**
         * check if there is a valid request method in the endpoints
         * 
         * Return an error / success message with a specific code
         */
        if( !isset($db_req_params[$this->req_method][$this->request_endpoint]) ) {
            
            $code = empty($this->inner_url) ? 200 : 404;

            // return error if not valid
            return $this->requestOutput($code);
        } else {
            // set the acceptable parameters
            $accepted =  $db_req_params[$this->req_method][$this->request_endpoint];
            
            // confirm that the parameters parsed is not more than the accpetable ones
            if( !isset($accepted['params']) || empty($accepted['params']) ) {
                // return all tests parsed
                return $this->requestOutput(100);
            } 
            else {
                
                // get the keys of all the acceptable parameters
                $endpointKeys = array_keys($accepted['params']);
                $errorFound = [];
                
                // confirm that the supplied parameters are within the list of expected parameters
                foreach($params as $key => $value) {
                    if(!in_array($key,  $this->keys_exempted) && !in_array($key, $endpointKeys)) {
                        // set the error variable to true
                        $errorFound[] = $key;                   
                        // break the loop
                        break;
                    }
                }

                // if an invalid parameter was parsed
                if($errorFound) {
                    // return invalid parameters parsed to the endpoint
                    return $this->requestOutput(405, [
                        'accepted' => ["parameters" => $accepted['params']],
                        'invalids' => $errorFound
                    ]);
                } else {

                    /* Set the required into an empty array list */
                    $required = [];
                    $required_text = [];

                    // loop through the accepted parameters and check which one has the description 
                    // required and append to the list
                    foreach($accepted['params'] as $key => $value) {
                        // evaluates to true
                        if( strpos($value, "required") !== false) {
                            $required[] = $key;
                            $required_text[] = $key . ": " . str_replace(["required", "-"], "", $value);
                        }
                    }

                    /**
                     * Confirm the count using an array_intersect
                     * What is happening
                     * 
                     * Get the keys of the parsed parameters
                     * count the number of times the required keys appeared in it
                     * 
                     * compare to the count of the required keys if it matches.
                     * 
                     */
                    $confirm = (count(array_intersect($required, array_keys($params))) == count($required));
                    
                    // If it does not evaluate to true
                    if(!$confirm) {
                        // return the response of required parameters
                        return $this->requestOutput(401, ['required' => $required_text]);
                    } else {
                        // return all tests parsed
                        return $this->requestOutput(100);
                    }

                }
            }
        }

    }

    /**
     * Outputs to the screen
     * 
     * @param Int                   $code   This is the code after processing the user request
     * @param Mixed{string/array}    $data   Any addition data to parse to the user
     */
    private function requestOutput($code, $message = null) {
        // format the data to return
        $data = [
            'code' => $code,
            'description' => $this->outputMessage($code)
        ];
        // remove the description endpoint if the response is 200
        if($code == 200) {
            unset($data['description']);
        }
        ( !empty($message) ) ? ($data['data'] = $message) : null;

        return $data;
    }

    /**
     * This is the output message based on the code
     * 
     * @param Int $code
     * 
     * @return String
     */
    private function outputMessage($code) {

        $description = [
            200 => "The request was successfully executed and returned some results.",
            201 => "The request was successful however, no results was found.",
            205 => "The record was successfully updated.",
            202 => "The data was successfully inserted into the database.",
            203 => "No Content Found.",
            400 => "Invalid request method parsed.",
            401 => "Sorry! Please ensure all required fields are not empty.",
            404 => "Invalid request node parsed.",
            405 => "Invalid parameters was parsed to the endpoint.",
            100 => "All tests parsed",
            500 => "Internal Server Error.",
            501 => "Sorry! You do not have the required permissions to perform this action.",
            600 => "Sorry! Your current subscription does not grant you permission to perform this action.",
            700 => "Unknown request parsed",
            999 => "An error occurred please try again later",
            1000 => "Blocked!!! CSRF Attempt"
        ];
        
        return $description[$code] ?? $description[700];
    }

    /**
     * This handles all requests by redirecting it to the appropriate
     * Controller class for that particular endpoint request
     * 
     * @param stdClass $params         - This the array of parameters that the user parsed in the request
     * 
     * @return  Array
     */
    private function requestHandler() {

        // preset the response
        $result = [];
        $code = 500;

        $params = $this->req_params;

        // if the authorization token is not empty
        if(!empty($this->AuthorizationToken)) {

            // confirm the user token
            $token = str_ireplace('Bearer', '', $this->AuthorizationToken);
            $token = trim($token);

            // create
            $formObj = new FormsModel();
            $formObj->subquery = ['(SELECT users.username FROM users WHERE users.user_id = users_login_history.user_id LIMIT 1) AS username'];

            // validate the Api Keys
            $apiKeyValidation = $formObj->queryBuilder('users_login_history', ['token' => $token], 'user_id', 1);

            if(!empty($apiKeyValidation)) {

                // create an auth object
                $authObj = new \App\Models\AuthModel();

                // get the user data
                $query = $authObj->verify_login(['username' => $apiKeyValidation[0]['username'], '_skip_password_validation' => true, 'token' => $token]);
                
                // confirm that the query was not empty
                if(!empty($query)) {

                    // set some additional information 
                    $params['_userId'] = $query['user']['user_id'];
                    $params['_userData'] = $query['user'];
                    $params['_apiRequest'] = true;
                    $params['_clientId'] = $query['user']['client_id'];

                    // set the global userData parameter
                    $controllerObj = new \App\Controllers\Controller();
                    $controllerObj->setUserData($query['user']);
                }

            }
        } else {
            // session
            $_userId = $params['ci_session']->_userId;

            // permissions Object
            $permitObject = new \App\Controllers\Permissions();

            // set additional parameters
            $params['_userId'] = !empty($_userId) ? $_userId : $this->_userId;
            $params['_userData'] = $permitObject->userData($params['ci_session']);

            // if the user data is not empty
            if(!empty($params['_userData'])) {
                $params['_clientId'] = $params['_userData']['client_id'];
            }
        }

        // reqest made via api request
        $params['_apiRequest'] = true;
        $params['remote'] = (bool) isset($apiKeyValidation);

        // confirm that the user is logged in
        if(empty($params['_userData']) && ($this->req_method === 'GET')) {
            return $this->requestOutput(501);;
        }

        // parse the code to return
        $code = !empty($code) ? $code : 201;

        // set the default limit to 1000
        $params['_limit'] = isset($params['limit']) ? (int) $params['limit'] : $this->global_limit;

        // if the $params['methcens_csrf_token'] is set
        if(isset($params['form_hash']) && !isset($apiKeyValidation)) {

            // get the csrf data
            $csrf_hash = $params['ci_session']->form_hash;

            // csrf data check
            if($csrf_hash !== $params['form_hash']) {
                // return invalid parameters parsed to the endpoint
                return $this->requestOutput(1000);
            }
        }
        
        // if the client id is empty and yet the user is not selecting which account to manage
        if(empty($this->_userId) && (!in_array($this->outer_url, ["select", "pay", "verify"]) && !in_array($this->inner_url, ["account", "payment"]))) {
            // set the request output results
            return $this->requestOutput($code, $result);
        }
        
        // set the classname
        $classname = "\\App\\Controllers\\".ucfirst($this->inner_url);

        // confirm if the class actually exists
        if(class_exists($classname)) {

            // create a new class for handling the resource
            $classObject = new $classname;
            
            // confirm that there is a method to process the resource endpoint
            if(method_exists($classObject, $this->outer_url)) {

                // set the method to load
                $method = $this->outer_url;
                
                // append the request handler to the parameters
                $params['req_method'] = $this->req_method;
                
                // convert the response into an arry if not already in there
                $request = $classObject->$method($params);
                
                // set the response code to return
                $code = is_array($request) && isset($request['code']) ? $request['code'] : 203;
                
                // set the result
                $result['result'] =  is_array($request) && isset($request["data"]) ? ($request["data"]['result'] ?? $request["data"]) : $request;

                // if additional parameter was parsed
                if(is_array($request) && isset($request['data']['additional'])) {
                    // set the additional parameter
                    $result['additional'] = $request['data']["additional"];
                }

                // save the user last activity time
                if(!in_array($this->outer_url, $this->bypass_csrf_token_list)) {
                    $params['ci_session']->set('_last_activity_timer', time());
                }
                
            }

        }

        // output the results
        return $this->requestOutput($code, $result);

    }

}
?>