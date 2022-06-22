<?php 
namespace App\Controllers;

use App\Controllers\Controller;

class Comments extends Controller {

    public function index($params = []) {
        if(empty($params)) {
            return $this->list();
        }
        
        $data = $this->list($params);
        
        return $data;

    }
    
    /**
     * List Comments history
     * 
     * @return Array
     */
    public function list($params = []) {

        // set item
        $limit = !empty($params['limit']) ? $params['limit'] : $this->max_count;
        $last_reply_id = !empty($params['last_reply_id']) ? $params['last_reply_id'] : null;

        // filters
        $filter = [];

        if($last_reply_id) {
            $filter['comments.id <'] = $last_reply_id;
        }
        
        if(!empty($params['resource'])) {
            $filter['resource'] = $params['resource'];
        }

        if(!empty($params['resource_id'])) {
            $filter['resource_id'] = $params['resource_id'];
        }

        // rows counter
        $count = $this->formObject->queryBuilder('comments', $filter, 'COUNT(*) AS rows_count', $limit)[0] ?? [];

        // rows query
        $this->formObject->leftjoins = ['users' => 'users.item_id = comments.created_by'];

        $comments = $this->formObject->queryBuilder(
            'comments', $filter, 
            'comments.*, users.firstname, users.lastname, DATE(comments.date_created) AS raw_date', $limit
        );
        $last_row = $comments[(count($comments) - 1)]['id'] ?? null;
        
        return [
            'code' => 200,
            'data' => [
                'comments_count' => $count['rows_count'] ?? 0,
                'replies_resource' => $params['resource'] ?? null,
                'last_reply_id' => $last_row,
                'replies_list' => $comments
            ]
        ];

    }

    /**
     * Save the Comment
     * 
     * @return Array
     */
    public function save(array $params = []) {

        if(empty($params)) {
            return [];
        }

        // confirm that the resource id is not empty
        if(empty($params['resource_id'])) {
            return 'The resource id is required.';
        }

        // confirm that the resource is not empty
        if(empty($params['resource'])) {
            return 'The resource is required.';
        }

        // set the comments
        $comments = clean_text($params['comment']);

        if(str_word_count($comments) < 3) {
            return 'Comment word must be at least 3 words long.';
        }

        // validate the record id
        $row = $this->formObject->queryBuilder($params['resource'], ['item_id' => $params['resource_id']], 'id', 1);

        // if the record id was not found
        if(empty($row)) {
            return 'Sorry! An invalid record id was parsed';
        }

        // create a new $item_id
        $item_id = random_string();

        // insert the data
        $this->formObject->insertRow('comments', [
            'created_by' => $params['_userData']['member_id'], 'item_id' => $item_id,
            'resource' => $params['resource'], 'resource_id' => $params['resource_id'],
            'comment' => $comments, 'ipaddress' => $params['ip_address'], 
            'user_agent' => $params['user_agent']
        ]);

        // rows query
        $this->formObject->leftjoins = ['users' => 'users.item_id = comments.created_by'];

        return [
            'code' => 200,
            'data' => [
                'result' => 'Comment was successfully save',
                'additional' => [
                    'data' => $this->formObject->queryBuilder(
                        'comments', ['comments.item_id' => $item_id], 
                        'comments.*, users.firstname, users.lastname, DATE(comments.date_created) AS raw_date', 1
                    )[0]
                ]
            ]
        ];

    }

}