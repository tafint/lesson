<?php
namespace App\Service\Confirm;
/**
 * This is a class Confirm
 */
abstract class Confirm
{   

    protected $_token = array();

    public function __construct($token)
    {
        $this->_token = $token;
    }

    abstract public function confirm();
}