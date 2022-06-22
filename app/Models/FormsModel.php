<?php
namespace App\Models;

use CodeIgniter\Model;

class FormsModel extends Model {

    protected $table = 'comments';
    public $db;
    public $max_count = 3000;
    public $orderBy = 'a.id ASC';
    public $orderColumn = null;
    public $orderDirection = 'DESC';
    public $whereIn = [];
    public $whereIn_r = [];
    public $whereLike = [];
    public $leftjoins = [];
    public $subquery = [];
    public $groupBy = [];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Get the list of all users
     * 
     * @param String        $table_name
     * @param Array         $where
     * @param Int           $limit
     * @param Array         $joins
     * @param Array         $subquery
     * 
     * @return Array
     */
    public function allRows($table_name = null, array $where = [], $limit = 500, array $joins = [], array $subquery = []) {

        if(empty($table_name)) {
            return [];
        }

        $where_clause = 'WHERE 1 ';
        $subqueries = null;

        // loop through the subqueries
        foreach($subquery as $query) {
            $subqueries .= ','. $query;
        }

        if(!empty($where)) {
            if(!is_array($where)) {
                $where_clause = "AND a.{$where}";
            } else {
                foreach($where as $key => $value) {
                    if(!empty($value)) {
                        $val = str_ireplace("LIKE ", "", $value);
                        $where_clause .= ( strpos($value, "LIKE") !== false) ? 
                            " AND a.{$key} LIKE '%{$val}%'" : (' AND a.'.$key.'="'.$val.'"');
                    }
                }
                $where_clause = trim($where_clause, ' AND');
            }
        }

        // if the wherein is not empty
        if(!empty($this->whereIn_r)) {
            foreach($this->whereIn_r as $key => $where) {
                if(!empty($where)) {
                    $where_clause .= " AND a.{$key} IN ".$this->queryInList($where);
                }
            }
        }

        // set the joins information
        $join_columns = $this->joinQuery($joins);

        // perform the database query
        $stmt = $this->db->query("SELECT a.* {$subqueries} ".(isset($join_columns['column']) ? ",{$join_columns['column']}" : null)."
            FROM {$table_name} a ".($join_columns['table'] ?? null)."
            {$where_clause} ORDER BY {$this->orderBy} LIMIT {$limit}");
        
        // return the results array
        return !empty($stmt) ? $stmt->getResultArray() : [];

    }

    /**
     * Query Builder
     * 
     * @param String    $table_name     This is the name of the table to perform the query
     * @param Array     $where_clause   An array of the where clause to use
     * @param String    $columns        This is the columns to return in the result set
     * @param Int       $limit          The number of rows to return in the result
     * @param Int       $offset         This is an offset to use for the limit clause
     * 
     * @return Array
     */
    public function queryBuilder($table_name, $where_clause = [], $columns = "*", $limit = 1000, $offset = 0) {

        // append the subqueries to the columns to load
        if(!empty($this->subquery)) {
            foreach($this->subquery as $query) {
                $columns .= ', '. $query;
            }
        }

        // set the order by 
        $orderColumn = empty($this->orderColumn) ? "{$table_name}.id" : $this->orderColumn;

        // set the builder query
        $builder = $this->db->table($table_name)
                        ->select($columns)
                        ->where($where_clause)
                        ->whereIn()
                        ->limit($limit, $offset)
                        ->orderBy($orderColumn, $this->orderDirection);

        // confirm if there are any left joins whatso so ever
        if(!empty($this->leftjoins)) {
            // loop through the joins and append it to the query
            foreach($this->leftjoins as $table => $join) {
                $builder->join($table, $join, 'left');
            }
        }

        // confirm if the where in is parsed
        if(!empty($this->whereIn)) {
            // loop through the joins and append it to the query
            foreach($this->whereIn as $key => $value) {
                if( !empty($value) ) {
                    $builder->whereIn($key, $value);
                }
            }
        }

        // confirm if the like is not empty
        if(!empty($this->whereLike)) {
            // counter
            $count = 0;
            // loop through the like columns and append it to the query
            foreach($this->whereLike as $key => $value) {
                if( !is_array($value) ) {
                    if($count == 0) {
                        $builder->like($key, $value, 'both');
                    } else {
                        $builder->orLike($key, $value, 'both');
                    }
                    $count++;
                } else {
                    foreach($value as $i => $v) {
                        if($i == 0) {
                            $builder->like($key, $v);
                        } else {
                            $builder->orLike($key, $v);
                        }                        
                        $count++;
                    }
                }
            }
        }

        // confirm if the group by was parsed
        if(!empty($this->groupBy)) {
            // convert into an array
            $groupBy = string_to_array($this->groupBy);

            // loop through the joins and append it to the query
            foreach($groupBy as $value) {
                $builder->groupBy($value);
            }
        }
                        
        $result = $builder->get();

        $result = !empty($result) ? $result->getResultArray() : [];

        return $result;
    }

    /**
     * Prepare the in list
     * Loop through the list and then form a where in clause for the query builder
     * 
     * @param String|Array $list
     * 
     * @return Array
     */
    public function queryInList($list) {
        // convert the list into an array if not set
        $list = string_to_array($list);

        $items = '(';
        foreach($list as $item) {
            $items .= "'{$item}',";
        }
        $items = trim($items, ',');
        $items .= ")";

        return $items;

    }

    /**
     * This runs a left join query in addition to the main query
     * 
     * @param Array $joins
     * 
     * @return Array
     */
    public function joinQuery(array $joins) {

        if(empty($joins)) {
            return [];
        }

        $tables = [
            'bibleclasses' => [
                'cols' => ['name', 'description', 'language'],
                'on' => 'class_leader_id',
            ],
            'marital_status' => [
                'cols' => ['name', 'code'],
                'on' => 'marital_status'
            ],
            'members_status' => [
                'cols' => ['name'],
                'on' => 'status_code'
            ],
            'societies' => [
                'cols' => ['name', 'description', 'logo', 'location'],
                'on' => 'society_id'
            ],
            'circuits' => [
                'cols' => ['name', 'description', 'logo'],
                'on' => 'circuit_id'
            ],
            'dioceses' => [
                'cols' => ['name', 'description', 'logo', 'cathedral_circuit', 'cathedral_society'],
                'on' => 'diocese_id'
            ],
            'regions' => [
                'cols' => ['name'],
                'on' => ['members_contact.region_id = regions.id']
            ],
            'members_contact' => [
                'cols' => ['contact_1', 'contact_2', 'email', 'postal', 'residence', 'hometown'],
                'on' => ['members_contact.member_id = a.item_id']
            ]
        ];

        $joins_query['table'] = null;
        $joins_query['column'] = null;

        foreach($joins as $join) {

            if(isset($tables[$join])) {

                $joins_query['table'] .= isset($tables[$join]['on']) ? 
                    " LEFT JOIN {$join} ON " . (is_array($tables[$join]['on']) ? $tables[$join]['on'][0] : "{$join}.id  = a.{$tables[$join]['on']}") : null;

                foreach($tables[$join]['cols'] as $column) {
                    $joins_query['column'] .= "{$join}.{$column} AS {$join}_{$column},";
                }

            }

        }
        $joins_query['column'] = trim(trim($joins_query['column']), ',');

        return $joins_query;

    }

    /**
     * Quick insertion of data into the database
     * 
     * @param String        $table_name
     * @param Array         $columns
     * 
     * @return Bool
     */
    public function insertRow($table_name, array $columns) {
        
        return $this->db->table($table_name)->insert($columns);
        
    }

    /**
     * Quick update of data into the database
     * 
     * @param String        $table_name
     * @param Array         $columns
     * @param Array         $whereclause
     * @param Int           $limit
     * 
     * @return Bool
     */
    public function updateRow($table_name, array $columns, array $where_clause, $limit = 1) {
        
        // update the row
        return $this->db->table($table_name)
                        ->set($columns)
                        ->where($where_clause)
                        ->limit($limit)
                        ->update();
        
    }

}