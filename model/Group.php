<?php
/**
 * This is a class Group
 */
class Group extends BaseModel

{
	public function __construct(DB $db){
		parent::__construct($db);
		//set table
		$this->_table = 'group';
	}

}