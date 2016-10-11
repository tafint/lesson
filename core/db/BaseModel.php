<?php
namespace Core\DB;
use \PDO;
/**
 * This is a class BaseModel
 */
abstract class BaseModel
{	
    /** @var string|null $_table set table in database */
	protected $_table;

    /** @var string|null $_db_host, $_db_name, $_db_user, $_db_pass config param to connect database */
	protected $_db_host;

	protected $_db_name;

	protected $_db_user;

	protected $_db_pass;

    /** @var string|null $_conn store connection */
	protected $_conn;

    /** @var string|null $_result store result after run query */
	protected $_result = array ();

    /** @var store all param before run query */
	protected $_query;

	protected $_num_row;

    protected $_select = '*';

    protected $_where = array();

    protected $_or_where = array();

    protected $_where_exist = '';

    protected $_where_not_exist = '';

    protected $_join = array();

    protected $_sort_by = '';

    protected $_group_by = '';

    protected $_take = 0;

    protected $_skip = 0;

    /** @var bool insert status flag */
    protected $_insert_status = false;

    public function __construct(DB $db)
    {   
        $this->_conn = $db;
    }


    /**
     * insert data to table 
     *
     * @param array|null $params data need insert.
     *
     * @return if insert success, store insert id to $_result, enable flag $_insert_status
     */
    public function insert($params = array())
    {
        if ($this->_table != '') {

        	$query = 'INSERT INTO `'.$this->_table.'`(`'.implode('`, `',array_keys($params)).'`) VALUES ("' . implode('", "', $params) . '")';
        	$result = $this->_conn->query($query);

        	if ($result) {
                $this->_insert_status = true;
                $this->_result = array();
        		array_push($this->_result, $this->_conn->lastInsertId());

        		return true;
        	} else {
        		return false;
        	}
        }
    }

    /**
     * check status flag $_insert_status, get row last query INSERT.
     *
     * @return return result and disable flag $_insert_status
     *
     */
    public function get_insert()
    {   

        if($this->_insert_status) {
            $this->_insert_status = false;
            $result = $this->where('id', $this->_result[0])->first();

            return $result;
        } else {
            return false;
        }
        
    }

    /**
     * store fields need select.
     *
     * @param string|* $params have string, once field is divided by comma
     *
     */
    public function select($params = '*')
    {   
        $params = explode(',', $params);
        $this->_select='`' . implode('`,`', $params) .'`';

        return $this;
    }

    /**
     * store key and value in where 
     *
     * @param string|null $key and $value need import
     *
     * @param string|= $condition is =,!=,>=,<=,LIKE default =
     *
     * @param string|AND $type is AND or OR, default AND
     *
     */
    public function where($key, $value, $condition = '=', $type = 'AND')
    {   
        $this->_where[] = array(
                          'key' => $key,
                          'value' => $value,
                          'condition' => $condition,
                          'type' => $type
                          );
        
        return $this;
    }

    /**
     * like function where
     *
     */
    public function or_where($key, $value, $condition = '=', $type = 'AND')
    {   
        $this->_or_where[] = array(
                          'key' => $key,
                          'value' => $value,
                          'condition' => $condition,
                          'type' => $type
                          );

        return $this;
    }

    public function join($table, $condition)
    {   
        $this->_join[] = array(
                         'table' => $table,
                         'condition' => $condition
                         );

        return $this;
    }

    public function sort_by($key, $value = 'DESC')
    {   
        $this->_sort_by = "ORDER BY $key $value";

        return $this;
    }

    public function take($params = 0)
    {   
        $this->_take = $param ;

        return $this;
    }

    public function skip($params = 0)
    {   
        $this->_skip = $param ;

        return $this;
    }

    /**
     * get first row
     *
     */
    public function first()
    {   
        $where = (count($this->_where) != 0) ? $this->parse_where($this->_where) : '1';
        $or_where = (($where != '1') && (count($this->_or_where) != 0)) ? 'OR '.$this->parse_where($this->_or_where) : '';
        
        $this->_query = "SELECT $this->_select FROM `$this->_table` WHERE $where $or_where $this->_where_exist $this->_where_not_exist $this->_sort_by LIMIT 1";
        $result = $this->_conn->query($this->_query);
        $this->reset();
        
        if ($result) {
            return $result->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * get limit row 
     *
     */
    public function get()
    {   
    	$where = (count($this->_where) != 0) ? $this->parse_where($this->_where) : '1';
        $or_where = (($where != '1') && (count($this->_or_where) != 0)) ? 'OR '.$this->parse_where($this->_or_where) : '';
        $take = ($this->_take != 0) ? 'LIMIT ' . $this->_take : '';
        $skip = ($this->_limit != 0) ? 'OFFSET ' . $this->_limit : '';

        $this->_query = "SELECT $this->_select FROM `$this->_table` WHERE $where $or_where $this->_where_exist $this->_where_not_exist $this->_sort_by $take $skip";
        $result = $this->_conn->query($this->_query);
        $this->reset();

        if ($result) {
            $data = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }

            return $data;
        } else {
            return false;
        }
    }

    /**
     * get all row
     *
     */
    public function getAll()
    {
        $where = (count($this->_where) != 0) ? $this->parse_where($this->_where) : '1';
        $or_where = (($where != '1') && (count($this->_or_where) != 0)) ? 'OR '.$this->parse_where($this->_or_where) : '';
        
        $this->_query = "SELECT $this->_select FROM `$this->_table` WHERE $where $or_where $this->_where_exist $this->_where_not_exist $this->_sort_by";
        $result = $this->_conn->query($this->_query);

        $this->reset();

        if ($result) {
            $data = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }

            return $data;
        } else {
            return false;
        }
    }

    /**
     * count row
     *
     * @return return number
     *
     */
    public function count()
    {
        $where = (count($this->_where) != 0) ? $this->parse_where($this->_where) : "1";
        $or_where = (($where != "1") && (count($this->_or_where) != 0)) ? "OR ".$this->parse_where($this->_or_where) : "";

        $this->_query = "SELECT COUNT(*) as num_row FROM `$this->_table` WHERE $where $or_where $this->_where_exist $this->_where_not_exist";
        $result = $this->_conn->query($this->_query);

        $this->reset();

        if ($result) {
            $row = $result->fetch(PDO::FETCH_ASSOC);

            return $row['num_row'];
        } else {
            return 0;
        }
    }

    public function query($query = "", $type = "select")
    {   
        $result = $this->_conn->query($query);
        $this->reset();

        if ($result) {

            switch ($type) {
                case "select":
                    $data = array();
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $data[] = $row;
                    }
                    return $data;
                    break;
                
                default:
                    return true;
                    break;
            }
        } else {
            return false;
        }
    }

    public function count_raw($query = "")
    {   
        $result = $this->_conn->query($query);
        $this->reset();

        if ($result) {
            return $result->fetchColumn();
        } else {
            return 0;
        }
    }

    public function update($params = array())
    {	
        if (count($params) > 0) {
            $update = "";

            foreach ($params as $key => $value) {
                $update .= "`$key` = '$value',";
            }

            $update = rtrim($update,",");
            $where = (count($this->_where) != 0) ? $this->parse_where($this->_where) : "1";
            $or_where = (($where != "1") && (count($this->_or_where) != 0)) ? "OR ".$this->parse_where($this->_or_where) : "";
            
            $this->_query = "UPDATE `$this->_table` SET $update WHERE $where $or_where";
            $result = $this->_conn->query($this->_query);

            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete()
    {	
        if ($this->_table != "") {
            $where = (count($this->_where) != 0) ? $this->parse_where($this->_where) : "1";
            $or_where = (($where != "1") && (count($this->_or_where) != 0)) ? "OR ".$this->parse_where($this->_or_where) : "";

            $this->_query = "DELETE FROM `$this->_table` WHERE $where $or_where";
            $result = $this->_conn->query($this->_query);
            $this->reset();

            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function parse_where($params = array())
    {   
        $string = "";

        foreach ($params as $p) {
            $string .= $p['type']." " . $p['key'] . " " . $p['condition'] . " '" . $p['value'] . "' ";
        }

        $string = ltrim($string,"AND");
        $string = ltrim($string,"OR");
        $string = "(" . $string . ")";

        return $string;
    }

    public function parse_join($params = array(), $type = "join")
    {   
        $string = "";

        foreach ($params as $p) {
            $string .=  $p['table'] . " ON " . $p['condition'];
        }

        switch ($type) {
            case "join":
                $string  = "JOIN $string";
                break;

            case "inner":
                $string  = "INNER JOIN $string";
                break;
            
            case "left":
                $string  = "LEFT JOIN $string";
                break;

            case "right":
                $string  = "RIGHT JOIN $string";
                break;

            case "full":
                $string  = "FULL JOIN $string";
                break;

            default:
                break;
        }

        return $string;
    }

    public function where_exist($query = "")
    {
        $this->_where_exist = ($query == "") ? "" : "AND EXISTS ($query)";

        return $this;
    }

    public function where_not_exist($query = "")
    {
        $this->_where_not_exist = ($query == "") ? "" : "AND NOT EXISTS ($query)";

        return $this;
    }

    public function group_by($query = "")
    {
        $this->_group_by = "GROUP BY $query";

        return $this;
    }
    

    /**
     * reset property after run query
     *
     */
    private function reset()
    {
        $this->_result = array ();
        $this->_query = "";
        $this->_select = "*";
        $this->_where = array();
        $this->_or_where = array();
        $this->_where_exist = "";
        $this->_where_not_exist = "";
        $this->_sort_by = "";
        $this->_join = array();
    }
}