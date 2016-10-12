<?php
namespace Controller;
use Core\Controller as BaseController;
use \Exception;
use \Exception\UserException as UserException;
use \Exception\CheckException as CheckException;
/**
 * This is a class FollowController
 */
abstract class Controller extends BaseController

{	
	protected $_data = array ();

	public function __construct()
	{	
		parent::__construct();
		$this->_model->load('user');
		$this->_model->load('follow');
		$this->_model->load('friend_list');
		$this->_model->load('friend_request');
		$this->_model->load('message_log');
		$this->_model->load('user_log');
		$this->_model->load('user_log_view');
		$this->_model->load('follow');
		
		$this->_helper->load('functions');
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
			$data = $this->_data;
			$data['navbar'] = true;
			$data['count_friend'] = $this->friend_list->count_all($data['user']['id']);
			$data['count_request'] = $this->friend_request->count_all($data['user']['id']);
			$data['count_message'] = $this->message_log->count_all($data['user']['id']);
			$data['count_follow'] = $this->follow->count_all($data['user']['id']);
			
			$this->load_template_before('header', $data);
			$this->load_template_after('footer');
		} catch (Exception $e) {
			$this->_data['error'] = true;
			$this->load_template_before('header');
			$this->load_template_after('footer');
		}
	}

	
}