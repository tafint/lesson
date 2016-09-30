<?php
/**
 * This is a class Config
 */
class Config

{
	/**
      * load new config file from folder /config
      *
      * @param string $config name of file config.
      *
      */
    public function load($config)
    {   
        require_once PATH.'/config/' . $config . '.php';
    }
}