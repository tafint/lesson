<?php
namespace Controller\Api;
use Core\Controller as Controller;
use \Exception;
/**
 * This is a class FavoriteController
 */
class FavoriteApiController extends Controller

{	
	protected $_data = array ();

	public function __construct()
	{	
		parent::__construct();
		$this->_model->load('user');
		$this->_model->load('favorite');

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

		} catch (Exception $e) {
			$this->_data['error'] = true;
		}
	}

	/**
     * api delete image
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

			if ($user_id == $data['user']['id']) {
				throw new Exception("Not follow yourself");
			} 
			
			$is_favorite = $this->favorite->is_favorite($data['user']['id'], $user_id);
			
			if ($is_favorite) {
				throw new Exception("Have favorite");
			}

			$favorite = $this->favorite->insert(array('user_id' => $data['user']['id'], 'user_id_to' => $user_id));
			
			if (!$favorite) {
				throw new Exception("Insert error");
			}

			$result = array('error' => false);

		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}

		return_json($result);
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
			$user = $this->user->find_id($user_id);
			
			if (!$user) {
				throw new Exception("User not exist");
			}

			$favorite = $this->favorite->is_favorite($data['user']['id'], $user_id);
			
			if (!$favorite) {
				throw new Exception("Favorite user not exist");
			} 

			$delete = $this->favorite->where('user_id',$data['user']['id'])->where('user_id_to', $user_id)->delete();
			
			if (!$delete) {
				throw new Exception("Delete error");
			}

			$result = array('error' => false);

		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		return_json($result);
	}
}