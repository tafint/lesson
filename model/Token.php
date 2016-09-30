<?php
/**
 * This is a class Token
 */
class Token extends Model

{
	public function __construct(){
		parent::__construct();
		// set table
		$this->_table = 'token';
	}

}