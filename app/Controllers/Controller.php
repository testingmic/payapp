<?php
namespace App\Controllers;

use App\Models\FormsModel;
use App\Controllers\Permissions;

class Controller extends BaseController {

    public $formObject;
    public $baseURL;
    public $sessionObject;
    protected $AppName = "PayApp";
    protected $mnotify_key = "9pakDgVAWMfAAiOqdiPnCuSUWcuBrHRA62UKiQlfCAPdS";
    protected $mnotify_sender = "EMMALEXTECH";
    public $userData = [];
    protected $max_count = 50000;

    // set the permission denied note
    public $permission_denied = 'Sorry! You do not have the required permissions to perform this action.';

    // date error codes
    public $error_codes = [
        "invalid-date" => "Sorry! An invalid date was parsed for the start date.",
        "invalid-range" => "Sorry! An invalid date was parsed for the end date.",
        "exceeds-today" => "Sorry! The date date must not exceed today's date",
        "exceeds-count" => "Sorry! The days between the two ranges must not exceed 366 days",
        "invalid-prevdate" => "Sorry! The start date must not exceed the end date.",
    ];

    // accepted period for query
    public $accepted_period = [
        'today', 'this_week', 'last_week', 
        'this_month', 'last_month', 'this_year', 
        'last_year', 'last_14days', 'last_30days', 
        'last_3months', 'last_6months'
    ];

    public function __construct() {
        $this->formObject = new FormsModel;
        
        // permission object
        $this->sessObj = session();
        $this->baseURL = base_url();
        $this->permitObject = new Permissions();
        $this->userData = $this->permitObject->userData();
    }

    public function setUserData($user) {
        $this->userData = $user;
    }

    /**
     * Permissions Check
     * 
     * @return Bool
     */
    public function hasAccess($section, $permission) {

        $permit = $this->userData['permissions']['roles'] ?? [];

        return (bool) isset($permit[$section][$permission]);
    }

    /**
     * Send SMS to the User using the MNotify APi
     * 
     * @param String            $fullname
     * @param String|Array      $contact
     */
    public function sendRegistrationSMS($fullname, $contact, $sender = null) {

        $message = "Hello {$fullname},\n";
        $message .= "You have successfully registered on the {$this->AppName}. ";
        $message .= "A personnel from the society will reach out to you should the need be.\nThank you.";
		
		// get the list of all recipients
		$recipients_contact = is_array($contact) ? implode(",", $contact) : $contact;

		//open connection
        $ch = curl_init();

		// set the field parameters
        $fields_string = [
            "key" => $this->mnotify_key,
			"recipient" => [$recipients_contact],
			"sender" => $this->mnotify_sender,
			"message" => $message
        ];
        

		// send the message
		curl_setopt_array($ch, 
            array(
                CURLOPT_URL => "https://api.mnotify.com/api/sms/quick",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_POST => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($fields_string),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                ]
            )
        );

        //execute post
        $result = json_encode(curl_exec($ch));
        
		return $result;

    }

	/**
	 * 404 Page
	 * 
	 * @return Mixed
	 */
	final function page_not_found() {

        $data['pagetitle'] = '404 Not Found';
		$data['no_validation_check'] = true;
		
		echo view('auth_404', $data);
	}

	/**
	 * Redirect to Login Page
	 * 
	 * @return Mixed
	 */
	final function must_login() {
        // show the header
        $data['no_header'] = true;
        // show the login page
		echo view('auth_login', $data);
	}

    /**
     * Delete a Record
     * This method will handle all delete record request from the system. 
     * It will then log the activity in a separate table
     * 
     * @param String        $params['resource']
     * @param String        $params['resource_id']
     * 
     * @return Array
     */
    public function _delete(array $params = []) {

        // if the resource and id are empty
        if(empty($params['resource']) || empty($params['resource_id'])) {
            return 'Ensure all required options are set';
        }

        else {

            // confirm the user information
            $record = $this->formObject->queryBuilder($params['resource'], [
                'item_id' => $params['resource_id'], 'client_id' => $params['_userData']['client_id']
            ], 'id', 1);

            // confirm if the record was found
            if(empty($record)) {
                return 'Please provide a valid id to proceed.';
            }

            // update the record status
            $this->formObject->updateRow($params['resource'], ['status' => '0'], ['item_id' => $params['resource_id']], 1);

            // return the success message
            return [
                'code' => 200,
                'data' => [
                    'result' => 'The record was succesfully deleted.'
                ]
            ];
        }
    }

    /**
     * validate a Record
     * 
     * This method will handle all validation of record request in the system. 
     * It will then log the activity in a separate table
     * 
     * This will set the state to Validated
     * 
     * @param String        $params['resource']
     * @param String        $params['resource_id']
     * 
     * @return Array
     */
    public function _validate(array $params = []) {

        // if the resource and id are empty
        if(empty($params['resource']) || empty($params['resource_id'])) {
            return 'Ensure all required options are set';
        }

        // confirm the user information
        $record = $this->formObject->queryBuilder($params['resource'], [
            'item_id' => $params['resource_id'], 'client_id' => $params['_userData']['client_id']
        ], 'id, state', 1);

        // confirm if the record was found
        if(empty($record)) {
            return 'Please provide a valid id to proceed.';
        }

        if($record[0]['state'] == 'Validated') {
            return 'Sorry! This record has already been validated.';
        }

        // update the record status
        $this->formObject->updateRow($params['resource'], ['state' => 'Validated'], ['item_id' => $params['resource_id']], 1);

        // return the success message
        return [
            'code' => 200,
            'data' => [
                'result' => 'The record was succesfully validated.'
            ]
        ];
        
    }

    /**
     * Quick Save an Item
     * 
     * @param String        $params['item_id']
     * @param String        $params['column']
     * @param String        $params['table']
     * 
     * @return Array
     */
    public function quicksave(array $params = []) {
        if(empty($params['table']) || empty($params['column']) || empty($params['item_id'])) {
            return 'Ensure all required parameters are parsed.';
        }

        // confirm record
        $record = $this->formObject->queryBuilder($params['table'], [
            'item_id' => $params['item_id'], 'client_id' => $params['_userData']['client_id']
        ], $params['column'], 1);

        if(empty($record)) {
            return 'An invalid record id was parsed.';
        }

        // update the record
        $this->formObject->updateRow($params['table'], [$params['column'] => $params['value']], ['item_id' => $params['item_id']], 1);

        return [
            'code' => 200,
            'data' => [
                'result' => 'Query successfully saved.'
            ]
        ];

    }

    /**
     * Temporary Session Data
     * 
     * @param String $params['request']     => list or remove
     * @param String $params['data']        => the name of the session
     * @param String $params['record_id']   => required when deleting a record
     * 
     * @return Array
     */
    public function temp(array $params = []) {

        if( empty($params) ) {
            return [];
        }

        // list session data
        if( !empty($params['data']) && $params['request'] == 'list') {
            $data = $params['ci_session']->{$params['data']};
            if( !empty($data) ) {
                return [
                    'code' => 200,
                    'data' => $data
                ];
            }
        }

        // remove record
        else if( !empty($params['data']) && $params['request'] == 'remove') {
            // confirm that the record_id is not empty
            if(empty($params['record_id'])) {
                return 'Sorry! Ensure the record id to delete is submitted';
            }

            //: create a new session
            $sessionClass = new TempSessions();
            $sessionClass->remove($params['data'], $params['record_id'], false, 'second');

            return [
                'code' => 200,
                'data' => 'Record successfully deleted.'
            ];
        }

    }

	/**
     * This formats the correct date range
     *  
     * @param String    $datePeriod      This is the date period that was parsed
     * 
     * @return This     $this->start_date, $this->end_date;
     */
    public function format_date($datePeriod = "this_week") {

        // Check Sales Period
        switch ($datePeriod) {
            case 'this_week':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "This Week";
                $previousTitle = "Last Week";
                $dateFrom = date("Y-m-d", strtotime("today -1 weeks"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-d", strtotime("today -2 weeks"));
                $prevTo = date("Y-m-d", strtotime("today -1 weeks"));
                break;
            case 'last_week':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "Last Weeks";
                $previousTitle = "Last 2 Weeks";
                $dateFrom = date("Y-m-d", strtotime("-2 weeks"));
                $dateTo = date("Y-m-d", strtotime("-1 weeks"));
                $prevFrom = date("Y-m-d", strtotime("today -3 weeks"));
                $prevTo = date("Y-m-d", strtotime("today -2 weeks"));
                break;
            case 'this_month':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "This Month";
                $previousTitle = "Last Month";
                $dateFrom = date("Y-m-01");
                $dateTo = date("Y-m-t");
                $prevFrom = date("Y-m-01", strtotime("last month"));
                $prevTo = date("Y-m-t", strtotime("last month"));
                break;
            case 'last_month':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "Last Months";
                $previousTitle = "Last 2 Months";
                $dateFrom = date("Y-m-01", strtotime("last month"));
                $dateTo = date("Y-m-t", strtotime("last month"));
                $prevFrom = date("Y-m-01", strtotime("last 2 month"));
                $prevTo = date("Y-m-t", strtotime("last 2 month"));
                break;
            case 'last_14days':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "Last 14 Days";
                $previousTitle = "Previous 14 Days";
                $dateFrom = date("Y-m-d", strtotime("-2 weeks"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-d", strtotime("-4 weeks"));
                $prevTo = date("Y-m-d", strtotime("-2 weeks"));
                break;
            case 'last_30days':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "Last 30 Days";
                $previousTitle = "Previous 30 Days";
                $dateFrom = date("Y-m-d", strtotime("-30 days"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-d", strtotime("-60 days"));
                $prevTo = date("Y-m-d", strtotime("-30 days"));
                break;
            case 'last_3months':
                $groupBy = "MONTH";
                $format = "jS M Y";
                $currentTitle = "Last 3 months";
                $previousTitle = "Previous 3 months";
                $dateFrom = date("Y-m-d", strtotime("today -3 months"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-d", strtotime("today -6 months"));
                $prevTo = date("Y-m-d", strtotime("today -3 months"));
                break;
            case 'last_6months':
                $groupBy = "MONTH";
                $format = "jS M Y";
                $currentTitle = "Last 6 Months";
                $previousTitle = "Previous 6 Months";
                $dateFrom = date("Y-m-d", strtotime("today -6 months"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-01", strtotime("today -12 months"));
                $prevTo = date("Y-m-t", strtotime("today -6 months"));
                break;
            case 'this_year':
                $groupBy = "MONTH";
                $format = "F";
                $dateFrom = date('Y-01-01');
                $dateTo = date('Y-12-31');
                $currentTitle = "This Year";
                $previousTitle = "Last Year";
                $prevFrom = date("Y-01-01", strtotime("last year"));
                $prevTo = date("Y-12-31", strtotime("last year"));
                break;
            case 'last_year':
                $groupBy = "MONTH";
                $format = "F";
                $currentTitle = "Last Year";
                $previousTitle = "Last Year";
                $dateFrom = date('Y-01-01', strtotime("last year"));
                $dateTo = date('Y-12-31', strtotime("last year"));
                $prevFrom = date('Y-01-01', strtotime("-2 years"));
                $prevTo = date('Y-12-31', strtotime("-2 years"));
                break;
            default:
				$groupBy = "HOUR";
                $format = "jS M Y";
                $currentTitle = "Today";
                $previousTitle = "Yesterday";
                $dateFrom = date("Y-m-d", strtotime("today -1 days"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-d", strtotime("today -2 days"));
                $prevTo = date("Y-m-d", strtotime("today -1 days"));
                break;
        }

        $range = [
            'start_date' => $dateFrom,
            'end_date' => $dateTo,
            'prevstart_date' => $prevFrom,
            'prevend_date' => $prevTo,
            'current_title' => $currentTitle,
            'previous_title' => $previousTitle,
            'group_by' => $groupBy,
            'date_format' => $format
        ];

        return $range;

    }

    /**
     * Preformat the date
     * 
     * This algo formats the dates that have been submitted by the user
     * 
     * @param String $period        This is the date to process
     */
    public function preformat_date($period, $bypass = false) {

        /** initial variables */
        $today = date("Y-m-d");
        $explode = explode(":", $period);
        $explode[1] = isset($explode[1]) ? $explode[1] : date("Y-m-d");

        /** Confirm that a valid date was parsed */
        if(!valid_date($explode[0])) {
            return "invalid-date";
        }

        /** If the next param was set */
        if(isset($explode[1]) && !valid_date($explode[1])) {
            return "invalid-range";
        }

        /** Confirm that the last date is not more than today */
        if(isset($explode[1]) && strtotime($explode[1]) > strtotime($today) && !$bypass) {
            return "exceeds-today";
        }

        /** confirm that the starting date is not greater than the end date */
        if(isset($explode[1]) && strtotime($explode[0]) > strtotime($explode[1])) {
            return "invalid-prevdate";
        }

        /** Confirm valid dates */
        if(!preg_match("/^[0-9-]+$/", $explode[0]) || !preg_match("/^[0-9-]+$/", $explode[1])) {
            return "invalid-range";
        }

        /** Check the days difference */
        $days_list = list_days($explode[0], $explode[1]);
        $count = count($days_list);

        /** ensure that the days count does not exceed 90 days */
        if($count > 366) {
            return "exceeds-count";
        }

        $format = "jS M Y";
        $group = "DATE";
        if($count >= 90) {
            $group = "MONTH";
            $format = "F";
        }
        
        $range = [
            'start_date' => $days_list[0],
            'end_date' => end($days_list),
            'group_by' => $group,
            'current_title' => "Past {$count} days",
            'previous_title' => "Previous {$count} days",
            'date_format' => $format,
            'prevstart_date' => date("Y-m-d", strtotime("today -".($count * 2)." days")),
            'prevend_date' => date("Y-m-d", strtotime("today -{$count} days"))
        ];

        return $range;

    }

}
?>