<?php
namespace Model;
use Core\DB\BaseModel as BaseModel;
/**
 * This is a class UserLogView
 */
class UserLogView extends BaseModel

{
	public function __construct(DB $db){
		parent::__construct($db);
		// set table
		$this->_table = "user_log_view";
	}

	/**
     * check user follow user action log
     *
     */
	public function is_view($user_id, $log_id) 
	{
        return $this->where("user_id", $user_id)->where("log_id", $log_id)->first();
	}

}