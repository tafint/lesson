<?php
/**
 * This is a class Group
 */
class Group extends Model

{
	public function __construct(){
		parent::__construct();
		//set table
		$this->_table = 'group';
	}

}