<?php
namespace Model;
use Core\DB\BaseModel as BaseModel;
use Core\DB\DB as DB;
/**
 * This is a class UserLog
 */
class UserLog extends BaseModel

{
	public function __construct(DB $db){
		parent::__construct($db);
		// set table
		$this->_table = 'user_log';
	}

}