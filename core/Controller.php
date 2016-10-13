<?php
namespace Core;
/**
 * This is a class Controller
 */
abstract class Controller
{   
    protected static $_instance;

    protected $_config;

    protected $_view;

    protected $_model;

    protected $_helper;
    
    public function __construct()
    {   
        self::$_instance = &$this;

        $this->_view = new View;

        $this->_model = new Model();
    }

    public function load_template_before($view,$data = array())
    {
        $this->_view->load_template_before($view, $data);
    }

    public function load_template_after($view,$data = array())
    {
        $this->_view->load_template_after($view, $data);
    }

    public function __destruct()
    {
        $this->_view->show();
    }

    public static function get_instance()
    {   
        return self::$_instance;
    }
}