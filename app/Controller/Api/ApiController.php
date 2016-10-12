<?php
namespace App\Controller\Api;
use Core\Api as Api;
use \Exception;
/**
 * This is a class ApiController
 */
abstract class ApiController extends Api
{	
	protected $_data = array ();
	protected $_result = array ();

	public function __construct()
	{	
		parent::__construct();
		$this->_model->load('user');
		
		//check session
		try {
			if (!isset($_SESSION['user_id'])) {
				throw new Exception("Error");
			}

			$user = $this->user->find_id($_SESSION['user_id']);

			if(!$user) {
				session_unset('user_id');
				throw new Exception("Error");
			}

			$this->_data['user'] = $user ;

		} catch (Exception $e) {
			$this->_result = array(
				                 "error" => true,
			                     "message" => "Please login"
			                 );
		}
	}

	/**
     * @return export data json
     *
     */
	public function response()
	{	
		header('Content-Type: application/json');
		echo json_encode($this->_result);
	}

}