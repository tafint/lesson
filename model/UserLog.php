<?php
/**
 * This is a class UserLog
 */
class UserLog extends Model

{
	public function __construct(){
		parent::__construct();
		// set table
		$this->_table = 'user_log';
	}

}