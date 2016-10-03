<?php
/**
 * This is a class Token
 */
class Token extends BaseModel

{
	public function __construct(DB $db){
		parent::__construct($db);
		// set table
		$this->_table = 'token';
	}

}