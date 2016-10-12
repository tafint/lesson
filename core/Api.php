<?php
namespace Core;

/**
 * This is a class Controller
 */
abstract class Api
{	
	protected static $_instance;

	protected $_config;

	protected $_model;

	protected $_helper;
	
    public function __construct()
    {	
        self::$_instance =& $this;

        global $db;
        $this->_model = new Model($db);

        $this->_helper = new Helper;
    }

	public static function get_instance()
    {	
        return self::$_instance;
    }
}