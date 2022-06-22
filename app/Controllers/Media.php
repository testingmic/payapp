<?php
namespace App\Controllers;

use App\Controllers\Controller;

class Media extends Controller {

	protected $max_attachment_size = 200;

	protected $upload_params = [
        "module" => "documents",
        "ismultiple" => true,
        "accept" => ".doc,.docx,.pdf,.png,.jpg,.jpeg,.xls,.xlsx"
    ];

    protected $accepted_attachment_file_types = [
        'jpg', 'png', 'jpeg', 'txt', 'pdf', 'docx', 'doc', 'xls', 'xlsx', 
        'mpeg', 'ppt', 'pptx', 'csv', 'gif', 'pub',  'mpg', 'flv', 'webm', 
        'movie', 'mov', 'pjpeg', 'webp', 'mp4', 'rtf'
    ];

    // files to be uploaded favicon globals
    public $favicon_array = [
        'jpg' => 'fa fa-file-image-o', 'png' => 'fa fa-file-image-o',
        'jpeg' => 'fa fa-file-image-o', 'gif' => 'fa fa-file-image-o',
        'pjpeg' => 'fa fa-file-image-o', 'webp' => 'fa fa-file-image-o',
        'pdf' => 'fa fa-file-pdf-o', 'doc' => 'fa fa-file-word-o',
        'docx' => 'fa fa-file-word-o', 'mp3' => 'fa fa-file-audio',
        'mpeg' => 'fa fa-file-video', 'mpg' => 'fa fa-file-video',
        'mp4' => 'fa fa-file-video', 'mov' => 'fa fa-file-video', 
        'movie' => 'fa fa-file-video', 'webm' => 'fa fa-file-video',
        'qt' => 'fa fa-file-video', 'zip' => 'fa fa-archive',
        'txt' => 'fa fa-file-alt', 'csv' => 'fa fa-file-csv',
        'rtf' => 'fa fa-file-alt', 'xls' => 'fa fa-file-excel',
        'xlsx' => 'fa fa-file-excel', 'php' => 'fa fa-file-alt',
        'css' => 'fa fa-file-alt', 'ppt' => 'fa fa-file-powerpoint',
        'pptx' => 'fa fa-file-powerpoint', 'sql' => 'fa fa-file-alt',
        'flv' => 'fa fa-file-video', 'json' => 'fa fa-file-alt'
    ];

	/**
	 * This is the form upload placeholder Method
	 * 
	 * @param Array $params
	 * 
	 * @return Mixed
	 */
	public function form_uploader(array $params = []) {

		// set the file parameters
		$params = empty($params) ? $this->upload_params : $params;

		// preload some file attachments
        $preloaded_attachments = null;
        $attachments = $this->attachments($params);
       
        // get the attachments list
        $fresh_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;

		// set the file content
        $html_content = "
        <div class=\"mb-1\">
            <div class='post-attachment'>
                <div class='row'>
                    <div class=\"col-lg-12\" id=\"".($params["module"] ?? null)."\">
                        <div class=\"file_attachment_url\" data-url=\"".base_url("api/media/attachments")."\"></div>
                    </div>
                    <div class=\"".(isset($params["class"]) ? $params["class"] : "col-md-12")." text-left\">
                        <div class='d-flex justify-content-between'>";
                        if(!isset($params["no_title"])) {
                            $html_content .= "<label>Attach a Document ".(empty($params["no_notice"]) ? "<small class='text-danger'>(Maximum size <strong>{$this->max_attachment_size}MB</strong>)</small>" : null)."</label><br>";
                        }
                    $html_content .= "
                            <div class=\"ml-3\">
                                <button type='button' id='".($params["input_button"] ?? "ajax-upload-input")."' class='btn btn-outline-primary btn-sm'><i class='fa fa-paperclip'></i> Attach File(s)</button>
                                <input ".(isset($params["accept"]) && !empty($params["accept"]) ? "accept='{$params["accept"]}'" : null)." ".(isset($params["ismultiple"]) && !empty($params["ismultiple"]) ? "multiple" : null)." class='form-control hidden cursor ".($params["form_input_class"] ?? "attachment_file_upload")."' data-form_item_id=\"".($params["item_id"] ?? "temp_attachment")."\" data-form_module=\"".($params["module"] ?? null)."\" type=\"file\" name=\"".($params["form_input_class"] ?? "attachment_file_upload")."\" id=\"".($params["form_input_class"] ?? "attachment_file_upload")."\">
                            </div>
                            <div class=\"upload-document-loader hidden\"><span class=\"float-right\"><i style='color:#000' class='mdi mdi-spin mdi-sync-circle fa-1x'></i></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class=\"file-preview slim-scroll\" preview-id=\"".($params["module"] ?? null)."\">{$fresh_attachments}</div>
                <div class='form-group text-center mb-1'>{$preloaded_attachments}</div>
            </div>";
        $html_content .= "</div>";

        return $html_content;

	}

	/**
	 * File Attachment Upload
	 * 
	 * @param Array $params
	 * 
	 * @return Array
	 */
	public function attachments(array $params = []) {

        /** Initialize for processing */
        $root_dir = WRITEPATH . "uploads";

        // set the session value
        $this->sessionObject = $params['ci_session'];

        /** set the current user id **/
        $currentUser_Id = !empty($params['ci_session']->_userInfo['society_id']) ? $params['ci_session']->_userInfo['society_id'] : $params['_userData']['society_id'];

        /** perform all this checks if the attachmments list was not parsed **/
        if(!isset($params['attachments_list'])) {

        	//: create a new session
            $sessionClass = new TempSessions();

            // assign a variable to the user information
            $module = $params['module'];

            // if no directory has been created for the user then create one
            if(!is_dir("{$root_dir}/{$currentUser_Id}")) {
                // create additional directories for user
                mkdir("{$root_dir}/{$currentUser_Id}/tmp/download", 0755, true);
            }

            // set the user's directory
            $tmp_dir = "{$root_dir}/{$currentUser_Id}/tmp/{$module}/";
            $dwn_dir = "{$root_dir}/{$currentUser_Id}/tmp/download/";

            // create the temporary file directory
            if(!is_dir($tmp_dir)) {
                mkdir("{$tmp_dir}", 0755, true);
            }

            // create the download file directory
            if(!is_dir($dwn_dir)) {
                mkdir("{$dwn_dir}", 0755, true);
            }

            /** Get the data for processing */
            if($params['label'] == "upload") {

                // if the attachment file upload is not parsed
                if(!isset($params["attachment_file_upload"])) {
                    return ["code" => 203, "data" => "No file attached."];
                }

                // file uploaded
                $uploaded_file = $params["attachment_file_upload"];

                // attachment list
                $attachments_list = $params['ci_session']->get($module);

                // calculate the file size
                $totalFileSize = 0;

                // create a new object of the File class
				$file = new \CodeIgniter\Files\File($uploaded_file);
				
				// get the image information
				$fileName = $file->getBasename();
				$originalName = $uploaded_file->getName();
				$extension = $file->guessExtension();
				$kilobytes = $file->getSizeByUnit('kb');
				$megabytes = $file->getSizeByUnit('mb');

				// set a new image name
				$newname = str_ireplace([' ', '-'], '_', $originalName);

                // if the file attachment is empty
                if(empty($fileName)) {
                    return ["code" => 203, "data" => "No file attached."];
                }
                
                // set the file details to upload
                $n_FileTitle_Real = str_ireplace([' ', '-'], '_', $originalName);
                $fileType = $extension;
                $obj_key = $fileType;

                // set the accepted files to this list
                $accepted_files = $this->accepted_attachment_file_types;

                // if the accept variable was also parsed
                if(isset($params['accept']) && !empty($params['accept'])) {
                    // set the object key
                    $obj_key = ".{$fileType}";

                    // convert the accepted files to an array
                    $accepted_files = string_to_array($params['accept']);

                    // confirm that each item is indeed accepted on this server
                    foreach($accepted_files as $check) {
                        // remove the fullstop
                        $check = str_replace(".", "", $check);

                        // if the file type is not accepted at all on this server.
                        if(!in_array($check, $this->accepted_attachment_file_types)) {
                            return ["code" => 203, "data" => "Uploaded file type not accepted."];
                        }
                    }
                }

                // if the file type is in the list of accepted types
                if(!in_array($obj_key, $accepted_files)){
                    return ["code" => 203, "data" => "Uploaded file type not accepted."];
                }

                // validate the file uploaded
                if(!$uploaded_file->isValid() && !$uploaded_file->hasMoved()) {
                	return ["code" => 203, "data" => "Sorry! The uploaded file type not accepted."];
                }

                // file size
                $n_FileSize = 0;

                // meaning load the attachments
                if(!empty($attachments_list)) {
                    
                    // loop through the list of files
                    foreach($attachments_list as $each_file) {

                        //: get the file size
                        $n_FileSize = $megabytes;
                        $n_FileSize_KB = $kilobytes;
                        $totalFileSize += $n_FileSize_KB;
                    }
                    $n_FileSize = round(($totalFileSize / 1024));

                }

                // maximum files fize check
                if($n_FileSize > $this->max_attachment_size) {
                    return ["code" => 203, "data" => "Maximum attachment size is {$this->max_attachment_size}MB"];
                }

                // set a new filename
                $params['item_id'] = isset($params['item_id']) ? $params['item_id'] : "temp_attachment";

                // Upload file to the server 
                if($uploaded_file->move($tmp_dir, $newname)){ 
                    //: set this session data
                    $sessionClass->add($params['module'], $newname, $n_FileTitle_Real, $params['item_id'], $megabytes, $fileType);
                }

                // return the temporary files list after upload
                return [
                    "code" => 200,
                    "data" => [
                        "result" => $this->list_temp_attachments($module, $tmp_dir)["data"],
                        "additional" => [
                            "filename" => $originalName
                        ]
                    ]
                ];
            }

            /** Load the files */
            elseif($params['label'] == "list") {
                // set module
                $attachments_list = $params['ci_session']->get($module);
                
                // meaning load the attachments
                if(!empty($attachments_list)) {
                    // list the temporary attachment list
                    return [
                        "code" => 200,
                        "data" => $this->list_temp_attachments($module, $tmp_dir)["data"]
                    ];
                }
            }

            /** Remove an item */
            elseif($params['label'] == "remove") {
                // if the item id is not parsed
                if(!isset($params['item_id']) || empty($params['ci_session']->get($module))) {
                    return;
                }
                
                // remove the item
                if($sessionClass->remove($params['module'], $params['item_id'], $tmp_dir)) {
                    return ["code" => 200, "data" => "Attachment successfully removed."];
                }
            }

            /** Download Temporary File */
            elseif($params['label'] == "download") {

                // if the item id is not parsed
                if(!isset($params['item_id']) || empty($params['ci_session']->get($module))) {
                    return;
                }

                // create the download directory if non existent
                if(!is_dir($dwn_dir)) {
                    mkdir($dwn_dir);
                }

                // set attachment list
                $attachments_list = $params['ci_session']->get($module);
                
                // loop through list
                foreach($attachments_list as $each) {
                    
                    // if the value for first key matches the item id
                    if($each["first"] == $params['item_id']) {
                        
                        // format the download string
                        $file_to_download = "{$dwn_dir}{$each["second"]}";

                        // replace empty fields with underscore
                        $file_to_download = preg_replace("/[\s]/", "_", $file_to_download);
                        
                        // create the document for download
                        copy("{$tmp_dir}{$params['item_id']}", $file_to_download);
                        
                        // return the file link
                        return [
                            "code" => 200,
                            "data" => "writable/uploads/{$currentUser_Id}/tmp/download/{$each["second"]}"
                        ];

                        // break the loop
                        break;
                    }

                }
            }

        }

	}

    /**
     * List the temporary attached files list
     * 
     * @param String $module
     * @param String $tmp_dir
     * 
     * @return Array
     */
    public function list_temp_attachments($module, $tmp_dir) {

        // attachments list
        $attachments_list = $this->sessionObject->get($module);

        // calculate the file size
        $totalFileSize = 0;		

        // html string
        $attachments = "<div class='row'>";

        // loop through the list of files
        foreach($attachments_list as $each_file) {

            //: get the file size
            $n_FileSize_KB = $each_file['forth'];
            $totalFileSize += $n_FileSize_KB;
            
            // default
            $color = 'danger';

            //: Background color of the icon
            if(in_array($each_file["fifth"], ['doc', 'docx'])) {
                $color = 'primary';
            } elseif(in_array($each_file["fifth"], ['xls', 'xlsx', 'csv'])) {
                $color = 'success';
            } elseif(in_array($each_file["fifth"], ['txt', 'json', 'rtf', 'sql', 'css', 'php'])) {
                $color = 'default';
            }
            $attachments .= "<div title=\"Click to download: {$each_file["second"]}\" class=\"col-md-12 mb-2 text-left\" data-document-link=\"{$each_file["first"]}\">";
            $attachments .= "<div class=\"bg-inverse-primary text-black pr-2 pl-2\"><strong onclick=\"return download_ajax_temp_file('{$module}','{$each_file["first"]}');\" class=\"cursor download-temp-file\"><span class=\"text-{$color}\"><i class=\"{$this->favicon_array[$each_file["fifth"]]} fa-1x\"></i></span> ".substr($each_file["second"], 0, 40)."</strong> ({$each_file["forth"]}MB)";
            $attachments .= "<span class=\"float-right\"><button type=\"button\" href=\"#\" onclick=\"return delete_ajax_file_uploaded('{$module}','{$each_file["first"]}')\" data-document-module=\"{$module}\" data-document-link=\"{$each_file["first"]}\" style=\"padding: 0.1rem 0.4rem;\" class=\"btn btn-outline-danger btn-sm delete-attachment-file\"><i class=\"las la-trash\"></i></button></span>";
            $attachments .= "</div>";
            $attachments .= "</div>";
        }
        $attachments .= "</div>";
        $n_FileSize = round(($totalFileSize / 1024), 2);

        return [
            "code" => 200,
            "data" => [
                "files" => $attachments,
                "module" => $module,
                "details" => "<strong>Files Size:</strong> {$n_FileSize}MB"
            ]
        ];

    }

    /**
     * Save the Image
     * 
     * @param String        $params['resource']
     * @param String        $params['resource_id']
     * 
     * @return Array
     */
    public function upload(array $params = []) {
        
        if(empty($params)) {
            return [];
        }

        $module = "{$params['resource']}" . (!empty($params['resource_id']) ? "_{$params['resource_id']}" : null);
        
        // if the session data is empty
        if(empty($params['ci_session']->{$module})) {
            return 'You must upload at least one item to upload.';
        }

        /** Initialize for processing */
        $root_dir = WRITEPATH . "uploads";

        $documents_list = $params['ci_session']->{$module};

        /** set the current user id **/
        $currentUser_Id = $params['_userData']['society_id'];

        // set the temp directory
        $tmp_dir = "{$root_dir}/{$currentUser_Id}/tmp/{$module}/";
        $doc_dir = "{$root_dir}/{$currentUser_Id}/documents/{$module}/";

        // create the temporary file directory
        if(!is_dir($doc_dir)) {
            mkdir("{$doc_dir}", 0755, true);
        }

        $file_size = 0;
        $files_list = [];

        
        // loop through the documents list
        foreach($documents_list as $document) {

            // increment the file size
            $file_size += $document['forth'];

            // create a unique string for the image
            $unique_id = random_string();

            // insert the file
            $files_list[$unique_id] = [
                'name' => $document['first'],
                'path' => "writable/uploads/{$currentUser_Id}/documents/{$module}/" . $document['first'],
                'size' => $document['forth'],
                'extension' => $document['fifth']
            ];

            // Upload file to the server 
            if(copy($tmp_dir . $document['first'], $doc_dir . $document['first'])){}

        }

        // if the size
        if(round($file_size) > $this->max_attachment_size) {
            return 'Sorry! The maximum upload size must not exceed ' . $this->max_attachment_size . 'MB.';
        }

        // reset the resource id
        $params['resource_id'] = empty($params['resource_id']) ? ($params['record_id'] ?? null) : $params['resource_id'];

        // confirm if the resource has already been uploaded
        $is_existing = $this->formObject->queryBuilder('attachments', ['resource_id' => $params['resource_id']], 'attachments, file_size', 1);

        // if the record is not empty
        if(!empty($is_existing)) {
            // set the media
            $media = json_decode($is_existing[0]['attachments'], true);
            $file_size = $is_existing[0]['file_size'] + $file_size;

            // merge the two data set
            $new_array = array_merge($media, $files_list);

            // set the files list to the new array
            $files_list = $new_array;
        }

        // the total size of the document
        $total_size = number_format($file_size, 2);

        // confirm if the record exists
        if(isset($media, $new_array)) {
            // update the existing data
            $this->formObject->updateRow('attachments', [
                'attachments' => json_encode($files_list), 'file_size' => $total_size
            ], ['resource_id' => $params['resource_id'], 'resource' => $params['resource']], 1);
        } else {
            // insert the data into the data
            $this->formObject->insertRow('attachments', [
                'society_id' => $params['_userData']['society_id'], 'circuit_id' => $params['_userData']['circuit_id'],
                'unique_id' => random_string(), 'resource_id' => $params['resource_id'], 'resource' => $params['resource'],
                'attachments' => json_encode($files_list), 'file_size' => $total_size, 
                'created_by' => $params['_userData']['member_id']
            ]);
        }

        // unset the session
        $params['ci_session']->remove($module);

        // delete all the files 
        delete_files($tmp_dir);

        // return the success message
        return [
            'code' => 200,
            'data' => [
                'result' => 'Images succesfully uploaded.'
            ]
        ];

    }

}
?>