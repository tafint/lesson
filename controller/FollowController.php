<?php
/**
 * This is a class FollowController
 */
class FollowController extends Controller

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
		$this->_model->load('follow');
		
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
		}
	}

	/**
     * action follow list
     *
     */
	public function index()
	{	
		try {
			$data = $this->_data;

			if (isset($data['error'])) {
				throw new UserException("Please login");
			}

			$follows = $this->follow->get_all($data['user']['id']);
			
			if ($follows) {
				$data['follows'] = $follows;
			}
			
		} catch (UserException $e) {
			redirect();
		}
		
	    $this->_view->load_content('follow', $data);
	}
	/**
     * api add follow
     *
     */
	public function add()
	{	
		try {
			$data = $this->_data;

			if (isset($data['error'])) {
				throw new Exception("Please login");
			}

			$data = $this->_data;
			$user_id = $_POST['user_id'];
			$user = $this->user->find_id($user_id);
			
			if (!$user) {
				throw new Exception("User not exist");
			} 
			
			$is_follow = $this->follow->is_follow($data['user']['id'], $user_id);
			
			if ($is_follow) {
				throw new Exception("Have follow");
			}
			
			$follow = $this->follow->insert(array('user_id' => $data['user']['id'], 'user_id_to' => $user_id));
			
			if (!$follow) {
				throw new Exception("Insert error");
			}

			$result = array('error' => false);

		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}

		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * api delete image
     *
     */
	public function remove()
	{	
		try {
			$data = $this->_data;

			if (isset($data['error'])) {
				throw new Exception("Please login");
			}

			$data = $this->_data;
			$user_id = $_POST['user_id'];
			$follow = $this->follow->is_follow($data['user']['id'], $user_id);
			
			if (!$follow) {
				throw new Exception("Follow user not exist");
			} 
			
			$delete = $this->follow->where('user_id',$data['user']['id'])->where('user_id_to', $user_id)->delete();
			
			if (!$delete) {
				throw new Exception("Delete error");
			}
			
			$result = array('error' => false);
			
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}

		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}
}