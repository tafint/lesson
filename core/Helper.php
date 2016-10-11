<?php 
namespace Core;
/**
 * This is a class Helper
 */
class Helper
{	
	/**
      * load new helper file from folder /helper
      *
      * @param string $helper name of file helper.
      *
      */
	public function load($helper)
	{
		if (file_exists(PATH . '/helper/' .$helper .'.php')) {
			require_once PATH . '/helper/' .$helper .'.php';
		}
	}
}