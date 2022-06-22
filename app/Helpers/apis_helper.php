<?php
// global variables
global $responses, $favicon_array;

// set an array of responses to load and use
$responses = [
    'module' => [
        'found' => 'You have successfully selected the module.',
        'not_found' => 'Sorry! The module selected has not yet been activated.'
    ],
	'request' => [
		'add' => 'Request was successfully processed. A personnel will get in touch with you shortly.',
		'update' => 'Request was successfully updated.',
		'email' => 'An email response has been sent to the sender of the request.'
	],
	'contact' => [
		'add' => 'Contact Form was successfully processed. A personnel will get in touch with you shortly.',
		'email' => 'An email response has been sent to the sender of the request.'
	],
    'auth' => [
        'active' => 'User is Active.',
        'invalid' => 'Sorry! Invalid Username and/or Password.',
        'verified' => 'Congrats! You have successfully logged in.',
        'required' => 'Username/Password fields are required and cannot be left empty.',
        'logged_out' => 'Successfully logged out.'
    ],
    'thumbnail' => [
        'post_not_found' => 'Sorry! You have specified an invalid post id.',
        'invalid' => 'Sorry! You have specified an invalid media id.',
        'removed' => 'Post thumbnail successfully removed.',
        'uploaded' => 'Image was successfully uploaded',
        'updated' => 'Media item updated successfully.',
        'empty' => 'Sorry! Upload an image to proceed.'
    ],
    'post' => [
        'not_found' => 'Sorry! An invalid post id was parsed.',
        'updated' => ' updated successfully.',
        'created' => ' successfully created.',
        'deleted' => ' was successfully deleted.'
    ],
    'app' => [
        'invalid_key' => 'Sorry! An invalid parameter was parsed.',
        'updated' => 'App setting successfully updated.'
    ],
    'reply' => [
        'sent' => 'Reply was successfully processed.'
    ]
];

// files to be uploaded favicon globals
$favicon_array = [
    'jpg' => 'mdi mdi-file-image', 'png' => 'mdi mdi-file-image',
    'jpeg' => 'mdi mdi-file-image', 'gif' => 'mdi mdi-file-image',
    'pjpeg' => 'mdi mdi-file-image', 'webp' => 'mdi mdi-file-image',
    'pdf' => 'mdi mdi-file-pdf-box', 'doc' => 'mdi mdi-file-word',
    'docx' => 'mdi mdi-file-word', 'mp3' => 'mdi mdi-file-audio',
    'mpeg' => 'mdi mdi-file-video', 'mpg' => 'mdi mdi-file-video',
    'mp4' => 'mdi mdi-file-video', 'mov' => 'mdi mdi-file-video', 
    'movie' => 'mdi mdi-file-video', 'webm' => 'mdi mdi-file-video',
    'qt' => 'mdi mdi-file-video', 'zip' => 'mdi mdi-archive',
    'txt' => 'mdi mdi-file-alt', 'csv' => 'mdi mdi-file-csv',
    'rtf' => 'mdi mdi-file-alt', 'xls' => 'mdi mdi-file-excel',
    'xlsx' => 'mdi mdi-file-excel', 'php' => 'mdi mdi-file-alt',
    'css' => 'mdi mdi-file-alt', 'ppt' => 'mdi mdi-file-powerpoint',
    'pptx' => 'mdi mdi-file-powerpoint', 'sql' => 'mdi mdi-file-alt',
    'flv' => 'mdi mdi-file-video', 'json' => 'mdi mdi-file-alt'
];

/**
 * This returns the response of a request. It confirms whether the key exists
 * using an explode of the variables parsed separated with a full stop
 * 
 * @param String $key
 * 
 * @return String
 */
function apis($key) {
	global $responses;

	$string = explode('.', $key);

	if(!isset($string[1])) {
		return $responses[$string[0]] ?? 'Request successfully processed.';
	}

	return $responses[$string[0]][$string[1]] ?? 'Request successfully processed.';
}

/**
 * Format the Default Routes List
 * 
 * @param Array     $routes 
 * @return Array
 */
function routes_list($routes) {

    $routes_keys = array_keys($routes);

    $routes_array = [];

    foreach($routes_keys as $route) {
        $filter = url_title($route);
        $routes_array[] = $filter;
    }

    $routes_array = array_filter($routes_array);

    return $routes_array;

}

/**
 * Get the items that are acceptable by the file upload button
 * 
 * @param String $key
 * 
 * @return String
 */
function file_accept($key = null) {
    // set the mime types to accept
    $mimes = [
        'post' => '.png,.jpg,.gif,.jpeg,.pjeg,.webp'
    ];

    // if the mime type exists
    if(isset($mimes[$key])) {
        return "accept=\"{$mimes[$key]}\"";
    }
}

/**
 * Get the row time differences. 
 * Find the difference in hours between the current time and the timestamp parsed
 * 
 * @param String $timestamp     This is the timestamp in date format or int
 * 
 * @return Float
 */
function raw_time_diff($timestamp) {
	
	$current = date("Y-m-d H:i:s");
    $start_date = new DateTime($timestamp);
    $since_start = $start_date->diff(new DateTime($current));
    
    // minutes count
    $minutes = $since_start->days * 24 * 60;
    $minutes += $since_start->h * 60;
    $minutes += $since_start->i;

    return $minutes;	
}

/**
 * Clean the text especially for html_entity_encoded text
 * 
 * revert it back to its original format and the strip tags
 * 
 * @param Strin
 */
function clean_text($string, $exempt = ['br', 'strong', 'em', 'del', 'blockquote', 'ul', 'li', 'ol']) {

    $string = htmlspecialchars_decode(html_entity_decode($string));
    $string = strip_tags($string, $exempt);
    return $string;
}

/**
 * Clean the text especially for html_entity_encoded text
 * 
 * revert it back to its original format and the strip tags
 * 
 * @param Strin
 */
function complete_strip($string) {

    $string = htmlspecialchars_decode(html_entity_decode($string), ENT_QUOTES);
    $string = strip_tags(trim($string));
    $string = str_ireplace([',','&nbsp;','\''], ['', '', ''], $string);
    return $string;
}

/**
 * Pads our string out so that all titles are the same length to nicely line up descriptions.
 *
 * @param int $extra How many extra spaces to add at the end
 */
function setPad(string $item, int $max, int $extra = 2, int $indent = 0) {
    $max += $extra + $indent;

    return str_pad(str_repeat(' ', $indent) . $item, $max);
}

if (! function_exists('option_item')) {
    /**
     * Returns the current full URL based on the IncomingRequest.
     * String returns ignore query and fragment parts.
     *
     * @param String option
     *
     * @return string|URI
     */
    function option_item($app_options, $option = null, $key = 'value') {
        if(is_array($app_options)) {
            foreach($app_options as $item) {
                if($item['name'] === $option) {
                    return $item[$key];
                }
            }
        }
    }
}

if (! function_exists('string_to_array')) {
    /**
     * Convert into an Array
     * 
     * @desc Converts a string to an array
     * @param $string The string that will be converted to the array
     * @param $delimeter The character for the separation
     * 
     * @return Array
     */
    function string_to_array($string, $delimiter = ",", $key_name = [], $allowEmpty = false) {
        
        // if its already an array then return the data
        if(is_array($string) || empty($string)) {
            return $string;
        }
        
        $array = [];
        $expl = explode($delimiter, $string);
        
        foreach($expl as $key => $each) {
            if(!empty($each) || $allowEmpty) {
                if(!empty($key_name)) {
                    $array[$key_name[$key]] = (trim($each) === "NULL" ? null : trim($each));
                } else{
                    $t_value = (trim($each) === "NULL") ? null : trim($each, "\"");
                    $array[] = trim($t_value);
                }
            }
        }
        return $array;
    }
}

if (! function_exists('string_to_int_array')) {
    /**
     * Convert into an Integer Array
     * 
     * @desc Converts a string to an array
     * @param $string The string that will be converted to the array
     * @param $delimeter The character for the separation
     * 
     * @return Array
     */
    function string_to_int_array($string, $delimiter = ",") {
        
        // if its already an array then return the data
        if(is_array($string) || empty($string)) {
            return $string;
        }
        
        $array = [];
        $expl = explode($delimiter, $string);
        
        foreach($expl as $key => $each) {
            $array[] = (int) $each;
        }
        return $array;
    }
}

/**
 * Confirm if the string parsed is a number
 * 
 * @param String $number
 * 
 * @return Bool
 */
function is_number($number) {
	return (bool) preg_match("/^[0-9]+$/", $number);
}

/**
 * Permission denied page
 * 
 * @return String
 */
function permission_denied() {
    return '
    <div class="alert alert-danger">
        Sorry! You do not have the required permissions to view this page.
    </div>';
}

/** 
 * Form loader placeholder 
 * 
 * @return String
 */
function form_overlay($display = "none") {
    return '
    <div class="formoverlay" style="display: '.$display.'; position: absolute;">
        <div class="offline-content text-center">
            <p><i style="color:#000" class="mdi mdi-spin mdi-sync-circle fa-3x"></i></p>
        </div>
    </div>';
}

/**
 * Leave comment container
 * 
 * @param Strig $resource           This is the resource name.
 * @param String $recordId          The unique id of the record on which the comment is been shared on
 * @param String $comment           The default comment to show on the form.
 * 
 * @return String
 */
function leave_comments_builder($resource, $recordId, $comment = null) {
    
    // create the html form
    $html = "
        <style>
        .leave-comment-wrapper trix-editor {
            min-height: 100px;
            max-height: 100px;
        }
        </style>
        <div class='leave-comment-wrapper' data-id='{$recordId}'>
            <div class='formoverlay' style='display: none; position: absolute;'>
                <div class='offline-content text-center'>
                    <p><i style='color:#000' class='mdi mdi-spin mdi-sync-circle fa-1x'></i></p>
                </div>
            </div>
            <div class='form-group mt-1'>
                <label for='leave_comment_content' title='Click to display comment form' class='cursor'>
                    ( <i class='fa fa-comments'></i> 
                    <strong>
                        <span data-id='{$recordId}' data-record='comments_count'>0</span>
                         comments
                    </strong> 
                    ) 
                    ".(!empty($comment) ? $comment : "Leave a comment below")."
                    <small class='text-danger'>(cannot be modified once posted)</small>
                </label>
            </div>
            <div class='hidden_' id='leave-comment-content'>
                <div class='form-group mb-2'>
                    <trix-editor class='trix-slim-scroll' required='required' id='leave_comment_content' name='leave_comment_content'></trix-editor>
                </div>
                <div class='form-group mt-0 text-right'>
                    <button type='button' onclick='return _leave_comment(\"{$resource}\", \"{$recordId}\")' class='btn share-comment btn-sm btn-outline-success'>Leave Comment <i class='fa fa-send'></i></button>
                </div>
            </div>
        </div>";

    return $html;
}

/**
 * Format Document Display Content
 * 
 * @param Array $document
 * 
 * @return String
 */
function format_document(array $document = [], $unique_id = null, $baseURL = null, $class = "col-md-4") {

    // end the query if the document is empty
    if(empty($document) || !is_array($document)) {
        return "";
    }
    
    // global variable
    global $favicon_array;

    // get the extensions
    $extension = $document['extension'];
    $filename = !empty($document['name']) ? substr($document['name'], 0, 35) : explode("/", $document['path'])[0];
    
    // // default
    $color = 'danger';
    $extension = strtolower($extension);
    
    // //: Background color of the icon
    if(in_array($extension, ['doc', 'docx'])) {
        $color = 'primary';
    } elseif(in_array($extension, ['xls', 'xlsx', 'csv'])) {
        $color = 'success';
    } elseif(in_array($extension, ['txt', 'json', 'rtf', 'sql', 'css', 'php'])) {
        $color = 'default';
    }

    // image extensions
    $image_mime = ["jpg", "jpeg", "png", "gif", ".webp"];
    $isImage = in_array($extension, $image_mime);

    // set the favicon
    $favicon = $favicon_array[$extension] ?? 'fa fa-file';
    $image_url = $document['url'] ?? "{$baseURL}{$document['path']}";

    // set the favicon
    if($isImage) {
        $preview = "<img height='110px' src='{$image_url}' width='100%'>";
    } else {
        $preview = "
        <div class='p-1'>
            <span class='text-{$color}'>
                <i class='{$favicon} la-7x'></i>
            </span>
        </div>";
    }

    // set the file data
    $file_data = "
    <div class='{$class} p-1 text-center col-sm-6'>
        <div class='document'>
            <div class='document-icon'>
                <a class='btn btn-outline-success btn-sm' title='Download document' target='_blank' href='{$image_url}'>
                    <i class='las la-download text-success'></i>
                </a>
                <span onclick='return _delete_document({$unique_id});' class='btn btn-outline-danger btn-sm' title='Delete this document'>
                    <i class='las la-trash'></i>
                </span>
            </div>
            {$preview}
            <div>{$filename}</div>
            <div></div>
        </div>
    </div>";
    

    return $file_data;
}

/**
 * Confirm if the text contains a word or string
 * 
 * @param String    $string     => The string to search for a word
 * @param Array     $words      => An array of words to look out for in the string
 * 
 * @return Bool
 */
function contains($string, array $words) {
    if(function_exists('str_contains')) {
        foreach($words as $word) {
            if(str_contains($string, $word)) {
                return true;
            }
        }
    } else {
        foreach($words as $word) {
            if(stristr($string, $word) !== false) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Order Id format by adding zeros to the begining
 * 
 * @param String $requestId		This is the id to format
 * 
 * @return String
 */
function append_zeros($requestId, $number = 3) {
    $preOrder = str_pad($requestId, $number, '0', STR_PAD_LEFT);
    return $preOrder;
}

/**
 * The status labels
 * 
 * @param String $status
 * 
 * @return String
 */
function status_label($status) {

    $label = $status;
    if(in_array($status, ["Pending", "Due Today"])) {
        $label = "<span class='badge bg-info'>{$status}</span>";
    }
    elseif(in_array($status, ["Rejected", "Quality Check", "Expenditure", "Cancelled", "Not Paid", "Unpaid", "Unseen", "Closed", "Overdue"])) {
        $label = "<span class='badge bg-danger'>{$status}</span>";
    }
    elseif(in_array($status, ["Reopen", "Waiting", "Quality Check", "Draft", "Processing", "In Review", "Confirmed", "Graded", "Requested"])) {
        $label = "<span class='badge bg-warning text-white'>{$status}</span>";
    }
    elseif(in_array($status, ["Answered", "Income", "Validated", "Solved", "Dispatched", "Enrolled", "Active", "Approved", "Paid", "Published", "Seen", "Submitted", "Completed", "Issued", "Delivered"])) {
        $label = "<span class='badge bg-success'>{$status}</span>";
    }

    return $label;
}

/**
 * Verify if a string parsed is a valid date
 * 
 * @param String $date 		This is the date String that has been parsed by the user
 * @param String $format 	This is the format for that date to use
 * 
 * @return Bool
 */
function valid_date($date, $format = 'Y-m-d') {
    
    $date = date($format, strtotime($date));

    // if the date equates this, then return false
    if($date === "1970-01-01") { return false; }

    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * @method listDays
 * 
 * @desc It lists dates between two specified dates
 * 
 * @param String $startDate 	This is the date to begin query from
 * @param String $endDate	This is the date to end the request query
 * @param String $format 	This is the format that will be applied to the date to be returned
 * 
 * @return Array
**/
function list_days($startDate, $endDate, $format='Y-m-d', $weekends = true) {

    $period = new DatePeriod(
        new DateTime($startDate),
        new DateInterval('P1D'),
        new DateTime(date('Y-m-d', strtotime($endDate. '+1 days')))
    );

    $days = array();
    $sCheck = (array) $period->start;

    // check the date parsed
    if(date("Y-m-d", strtotime($sCheck['date'])) == "1970-01-01") {
        
        // set a new start date and call the function again
        return list_days(date("Y-m-d", strtotime("first day of this week")), date("Y-m-d", strtotime("today")));

        // exit the query
        exit;
    }
    
    // fetch the days to display
    foreach ($period as $key => $value) {
        // exempt weekends from the list
        if($weekends || !in_array(date("l", strtotime($value->format($format))), ['Sunday', 'Saturday'])) {
            $days[] = $value->format($format);
        }
        
    }
    
    return $days;
}