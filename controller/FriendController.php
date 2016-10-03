<?php
/**
 * This is a class FriendController
 */
class FriendController extends Controller

{	
	protected $_data = array ();

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
			$data['count_follow'] = $this->follow->count_all($data['user']['id']);
			
			$this->load_template_before('header', $data);
			$this->load_template_after('footer');
		} catch (Exception $e) {
			$this->_data['error'] = true;
		}
	}

	/**
     * action friend list
     *
     */
	public function index()
	{	
		try {
			$data = $this->_data;

			if (isset($data['error'])) {
				throw new UserException("Please login");
			}

			$list_friends = $this->friend_list->get_all($data['user']['id']);
			
			if ($list_friends) {
				foreach ($list_friends as $key => $friend) {
	            	
	            	if ($friend['user_id'] == $data['user']['id']) {
	            		$friend['user'] = $this->user->find_id($friend['user_id_to']);
	            	} else {
	            		$friend['user'] = $this->user->find_id($friend['user_id']);
	            	}
	            	
	            	if ($friend['user']) {
	            		$data['list_friends'][$key] = $friend;
	            	}
	            }
			}
		} catch (UserException $e) {
			redirect();
		}
		
	    $this->_view->load_content('friend.list', $data);
	}

	/**
     * action view friend detail
     *
     */
	public function view($params)
	{	
		$data = $this->_data;
		try {
			
			if (isset($data['error'])) {
				throw new UserException("Please login");
			}
			
			if (isset($params[0])) {
				$id = $params[0];
				$data['permisson'] = false;
				
				// get info
				if ($id == $data['user']['id']){
					$data['is_owner'] = true;
					$data['profile'] = $data['user'];
					$data['permisson'] = true;
				} else {
					$data['is_owner'] = false;
					$user = $this->user->find_id($id);
					if (!$user) {
						throw new CheckException("Not exist user");
					}
					$data['profile'] = $this->user->find_id($id);
				}
				
				// check relation
				$result = $this->friend_list->is_friend($id, $data['user']['id']);
				
				if ($result) {
					$data['is_friend'] = true;
					$data['permisson'] = true;
				} elseif ($data['user']['group_id'] ==1) {
					$data['is_friend'] = false;
					$data['permisson'] = true;
				} else {
					$data['is_friend'] = false;
				}
				
				if (!$data['is_friend']) {
					$data['is_request'] = $this->friend_request->have_request($data['user']['id'], $data['profile']['id']);
				}
				
				// check favorite & follow
				$data['is_favorite'] = $this->favorite->is_favorite($data['user']['id'], $id);
				$data['is_follow'] = $this->follow->is_follow($data['user']['id'], $id);
				
				// get message
				if ($data['user']['id'] != $data['profile']['id']) {
					$messages = $this->message_log->get_message_user($id, $data['user']['id']);
					$data['message_log']=$messages;
				}
				
				// get friend
				if ($data['is_owner'] || $data['is_friend']) {
					$friends = $this->friend_list->get_all($data['profile']['id']);
					$data['friends'] = array();

					foreach ($friends as $friend) {
						if ($friend['user_id'] == $data['profile']['id']) {
							$friend['user_info'] = $this->user->find_id($friend['user_id_to']);
						} else {
							$friend['user_info'] = $this->user->find_id($friend['user_id']);
						}

						if($friend['user_info']) {
							$friend['is_friend'] = $this->friend_list->is_friend($data['user']['id'], $friend['user_info']['id']);
							
							if (!$friend['is_friend']) {
								$friend['is_request'] = $this->friend_request->have_request($data['user']['id'], $friend['user_info']['id']);
							}

							$data['friends'][] = $friend;
						}
					}

					// get favorite
					$data['favorites'] = array();
					$favorites = $this->favorite->get_all($data['profile']['id']);

					foreach ($favorites as $favorite) {
						$favorite['user_info'] = $this->user->find_id($favorite['user_id_to']);
						if ($favorite['user_info']) {
							$favorite['is_friend'] = $this->friend_list->is_friend($data['user']['id'], $favorite['user_info']['id']);

							if (!$favorite['is_friend']) {
								$favorite['is_request'] = $this->friend_request->have_request($data['user']['id'], $favorite['user_info']['id']);
							}

							$favorite['is_favorite'] = $this->favorite->is_favorite($data['user']['id'], $favorite['user_info']['id']);
							$data['favorites'][] = $favorite;
						}
					}
					
				}
				
				// get conversation
				if (($data['user']['group_id'] == 1) && ($data['user']['id'] != $data['profile']['id'])) {
					$conversations = $this->message_log->get_all_con($data['profile']['id']);
					
					foreach ($conversations as $key => $value) {
						$conver_user = $this->user->find_id($value);
						$data['conversations'][$key] = array('id' => $value, 'fullname' => $conver_user['fullname']);
					}
				}
				
				// get group
				$groups = $this->group->get();
		    	$data['groups'] = $groups;
		    	
		    	// get image
		    	$images = $this->image->get_all($data['profile']['id']);
		    	$data['images'] = [];
		    	
		    	foreach ($images as $image) {
		    		
		    		if ($this->image_like->is_like($data['user']['id'], $image['id'])) {
		    			$image['is_like'] = true;
		    		} else {
		    			$image['is_like'] = false;
		    		}

		    		$image['like'] = $this->image_like->count_all($image['id']);
		    		$data['images'][] = $image;
		    	}
			}
		} catch (CheckException $e) {
			$data['error'] = true;
			$data['message'] = $e->getMessage();
		} catch (UserException $e) {
			redirect();
		}
	    
	    $this->_view->load_content('friend.view', $data);
	}

	/**
     * action suggest user
     *
     */
	public function suggest()
	{	
		$data = $this->_data;
		try {
			
			if (isset($data['error'])) {
				throw new UserException("Please login");
			}
            
            $result = $this->friend_list->suggest_friend($data['user']['id']);
            
            if (!$result) {
            	throw new CheckException("Not found");
            }
        	
        	foreach ($result as $key => $value) {	
        		$user_request = $this->friend_request->have_request($id, $value['id']);
        		$value['request_status'] = $user_request ? true : false;
        		$data['users'][$key] = $value;
        	}
		} catch (CheckException $e) {
			$data['message'][]=$e->getMessage();
		} catch (UserException $e) {
			redirect();
		}
		
		$this->_view->load_content('friend.suggest', $data);
	}

	/**
     * api send to friend request
     *
     */
	public function add()
	{	
		try {
			$data = $this->_data;
			
			if (isset($data['error'])) {
				throw new Exception("Please login");
			}
			
			$user_id = $_POST['user_id_to'];
			$is_friend = $this->friend_list->is_friend($data['user']['id'], $user_id);
			
			if ($data['user']['id'] == $user_id) {
				throw new Exception("Not request friend to yourself");
			}
			
			if ($is_friend) {
				throw new Exception("Have friend");
			}
			
			$user = $this->user->find_id($user_id);
			if (!$user) {
				throw new Exception("User not exist");
			}

			$user_request = $this->friend_request->have_request($data['user']['id'], $user_id);
			
			if ($user_request) {
				throw new Exception("Request exist");
			}
			
			$request_data = array('user_id' => $data['user']['id'], 'user_id_to' =>$user_id);
			$request_new = $this->friend_request->insert($request_data);
			
			if (!$request_new) {
				throw new Exception("Not inset");
			} 
			
			$log_data = array(
				            'user_id' => $data['user']['id'],
				            'user_id_to' => $user_id,
				            'type' => 'send request make friend'
				            );
			$user_log = $this->user_log->insert($log_data);
			$result = array('error' => false);
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		} 
		
		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * api handle friend request
     *
     */
	public function handle()
	{	
		try {
			$data = $this->_data;
			
			if (isset($data['error'])) {
				throw new Exception("Please login");
			}
			
			$id = $_POST['id'];
			$type = $_POST['type'];
			$user_request = $this->friend_request->where('id', $id)->first();
			
			if (!$user_request) {
				throw new Exception("Not have request");
			}
			
			if ($this->_data['user']['id'] != $user_request['user_id_to']) {
				throw new Exception("Error owner");
			}
			
			$user = $this->user->find_id($user_request['user_id']);
			if (!$user) {
				throw new Exception("User not exist");
			}

			//check friend relation is exist
			if ($this->friend_list->is_friend($user_request['user_id'], $user_request['user_id_to'])) {
				$this->friend_request->where('id',$id)->delete();
				throw new Exception("Have friend");
			}
			
			if ($type ==1 ) {
				$data_friend = array(
					           'user_id' => $user_request['user_id'],
	                           'user_id_to' => $user_request['user_id_to']
					           );
				$new_friend = $this->friend_list->insert($data_friend);
				
				if (!$new_friend){
					throw new Exception("Error when insert");
				}
				
				$log_data = array(
				            'user_id' => $data['user']['id'],
				            'user_id_to' => $user_request['user_id'],
				            'type' => 'accept request friend'
				            );
				$user_log = $this->user_log->insert($log_data);
	            $this->friend_request->where('id',$id)->delete();
	            $result = array('error' => false);
			} else {
				$this->friend_request->where('id',$id)->delete();
				$result = array('error' => false);
			}
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * api unfriend
     *
     */
	public function remove()
	{	
		try {
			$data = $this->_data;
			
			if (isset($data['error'])) {
				throw new Exception("Please login");
			}
			
			$user_id = $_POST['user_id'];
			$user = $this->user->find_id($user_id);
			
			if (!$user) {
				throw new Exception("User not exist");
			}
			
			$friend = $this->friend_list->friend($data['user']['id'], $user_id);
			
			if (!$friend) {
				throw new Exception("Not is friend");
			}
			
			$delete = $this->friend_list->where('id', $friend['id'])->delete();
			
			if (!$delete) {
				throw new Exception("Delete error");
			}		
			
			$log_data = array(
				            'user_id' => $data['user']['id'],
				            'user_id_to' => $user_id,
				            'type' => 'unfriend'
				            );
			$user_log = $this->user_log->insert($log_data);
			$result['error'] = false;
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * action view all friend request
     *
     */
	public function request()
	{
		$data = $this->_data;
		try {
			if (isset($data['error'])) {
				throw new UserException("Please login");
			}
			
			$user_request = $this->friend_request->where('user_id_to', $data['user']['id'])->get();
			
			if (!$user_request) {
				throw new CheckException("Not have friend request");
			}
			
			foreach ($user_request as $key => $value) {
				$user_info = $this->user->where('id',$value['user_id'])->first();

				if ($user_info) {
					$value['user_info'] = $user_info;
					$data['users'][$key] = $value;
				}
			}
		} catch (CheckException $e) {
			$data['message'][] = $e->getMessage();
		} catch (UserException $e) {
			redirect('');
		}
		
		$this->_view->load_content('friend.request', $data);

	}


}