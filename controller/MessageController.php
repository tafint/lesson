<?php
/**
 * This is a class MessageController
 */
class MessageController extends Controller
{	
	public function __construct()
	{	
		parent::__construct();
		$this->_model->load('user');
		$this->_model->load('friend_list');
		$this->_model->load('friend_request');
		$this->_model->load('message_log');
		$this->_model->load('group');
		$this->_model->load('image');
		$this->_model->load('image_like');
		$this->_model->load('favorite');
		$this->_model->load('follow');
		$this->_model->load('user_log');
		$this->_helper->load('functions');
		$this->_helper->load('exception');
		//check if exist session user_id, redirect to index page if not exist session
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
			
			$this->load_template_before('header', $data);
			$this->load_template_after('footer');
		} catch (Exception $e) {
			$this->_data['error'] = true;
		}
	}
	

	/**
     * action view message in inbox
     *
     */
	public function index()
	{
		$data = $this->_data;
		try {
			if (isset($data['error'])) {
				throw new UserException("Please login");
			}
			
			$messages = $this->message_log->get_all_message($data['user']['id']);
			
			if (!$messages) {
				throw new CheckException("Not have message");
				
			}
			
			$data['data_messages'] = $messages;
		} catch (CheckException $e) {
			$data['message'][] = $e->getMessage();
		} catch (UserException $e) {
			redirect();
		}
	    
	    $this->_view->load_content('friend.message', $data);

	}


	/**
     * api create new message
     *
     */
	public function create()
	{	
		try {
			$data = $this->_data;
			
			if (isset($data['error'])) {
				throw new Exception("Please login");
			}
			
			$user_id_to = $_POST['user_id_to'];
			$user_to = $this->user->find_id($user_id_to);
			
			if(!$user_to) {
				throw new Exception("Not exist user");	
			}
			
			if(($user_to['group_id'] == 1) && ($data['user']['group_id'] != 1)) {
				throw new Exception("Not send message to admin");
			}
			
			$is_friend = $this->friend_list->is_friend($data['user']['id'], $user_to['id']);
			
			if (!$is_friend && ($data['user']['group_id']) != 1) {
				throw new Exception("Not have permisson");
			}
			
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
			
			$result['error'] = false;
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * api load message
     *
     */
	public function load()
	{	
		try {
			$data = $this->_data;
			
			if (isset($data['error'])) {
				throw new Exception("Please login");
			}
			
			$user_id = $_POST['user_id'];
			$user_id_to = $_POST['user_id_to'];
			
			if (($this->_data['user']['id'] != $user_id_to) && ($this->_data['user']['id'] != $user_id) && ($data['user']['group_id'] != 1)) {
				throw new Exception("Not have permisson");
			}
			
			$user = $this->user->find_id($user_id);

			if (!$user) {
				throw new Exception("Not exist user");
			}

			$current_message = htmlspecialchars($_POST['current_message']);
			$new_message = $this->message_log->get_message_user($user_id, $user_id_to, $current_message);
			
			foreach ($new_message as $message) {
				$user = $this->user->find_id($message['user_id']);
				$message['fullname'] = $user['fullname'];
				$result['data'][] = $message;
			}
			
			$result['error'] = false;
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}
}