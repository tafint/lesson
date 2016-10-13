<?php
namespace App\Controller\Api;
use Core\Controller as Controller;
use \Exception;
use App\Exception\UserException as UserException;
use App\Exception\CheckException as CheckException;
/**
 * This is a class MessageApiController
 */
class MessageApiController extends Controller
{	
	public function __construct()
	{	
		parent::__construct();
		$this->_model->load('friend_list');
		$this->_model->load('message_log');
	}

	/**
     * api create new message
     *
     */
	public function create()
	{	
		try {
			$data = $this->_data;		
			$user_id_to = $_POST['user_id_to'];
			$user_to = $this->user->find_id($user_id_to);
			
			if(!$user_to) {
				throw new Exception("Not exist user");	
			}
			
			$is_friend = $this->friend_list->is_friend($data['user']['id'], $user_to['id']);
			
			if ((!$is_friend && ($data['user']['group_id']) != 1) && ($data['user']['group_id'] != $user_to['group_id'])) {
				throw new Exception("Not have permisson");
			}

			// if(($user_to['group_id'] == 1) && ($data['user']['group_id'] != 1)) {
			// 	throw new Exception("Not send message to admin");
			// }
			
			$message = htmlspecialchars($_POST['message']);
			$current_message = htmlspecialchars($_POST['current_message']);
			$message_data = array(
	                        'user_id' => $data['user']['id'],
	                        'user_id_to' => $user_id_to,
	                        'message' => $message
				            );
			$new_message = $this->message_log->insert($message_data);
			
			if (!$new_message) {
				throw new Exception("Error when insert");
			}
			
			$new_message = $this->message_log->get_message_user($data['user']['id'], $user_id_to, $current_message);
			
			foreach ($new_message as $message) {
				$user = $this->user->find_id($message['user_id']);
				$message['fullname'] = $user['fullname'];
				$result['data'][] = $message;
			}
			
			$this->_result = array("error" => false);
		} catch (Exception $e) {
			$this->_result = array('error' => true, 'message' => $e->getMessage());
		}
		
		$this->response();
	}

	/**
     * api load message
     *
     */
	public function load()
	{	
		try {
			$data = $this->_data;	
			$user_id = $_POST['user_id'];
			$user_id_to = $_POST['user_id_to'];
			$user = $this->user->find_id($user_id);
			$user_to = $this->user->find_id($user_id_to);
			
			if (!$user) {
				throw new Exception("Not exist user");
			}

			if (($this->_data['user']['id'] != $user_id_to) && ($this->_data['user']['id'] != $user_id) && (($data['user']['group_id'] != 1) || ($user_to['group_id'] == 1))) {
				throw new Exception("Not have permisson");
			}

			$current_message = htmlspecialchars($_POST['current_message']);
			$new_message = $this->message_log->get_message_user($user_id, $user_id_to, $current_message);
			
			foreach ($new_message as $message) {
				$user = $this->user->find_id($message['user_id']);
				$message['fullname'] = $user['fullname'];
				$result['data'][] = $message;
			}
			
			$this->_result = array("error" => false);
		} catch (Exception $e) {
			$this->_result = array('error' => true, 'message' => $e->getMessage());
		}
		
		$this->_result();
	}
}