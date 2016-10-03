<?php
/**
 * This is a class UserController
 */
class UserController extends Controller

{	
	/** @var $_data store info */
	protected $_data = array ();
	
	/**
     * construct fuction.
     *
     */
	public function __construct()
	{	
		parent::__construct();

		$this->_model->load('user');
		$this->_model->load('token');
		$this->_model->load('image');
		$this->_model->load('friend_list');
		$this->_model->load('friend_request');
		$this->_model->load('image_like');
		$this->_model->load('group');
		$this->_model->load('message_log');
		$this->_helper->load('functions');
		$this->_helper->load('exception');
		
		// check session and get user info 
		if (isset($_SESSION['user_id'])) {
			$user = $this->user->find_id($_SESSION['user_id']);
			if($user) {
				$this->_data['user'] = $user ;
			}
			$this->_data['user'] = $user ;
			$data = $this->_data;
			$data['navbar'] = true;
			$data['count_friend'] = $this->friend_list->count_all($data['user']['id']);
			$data['count_request'] = $this->friend_request->count_all($data['user']['id']);
			$data['count_message'] = $this->message_log->count_all($data['user']['id']);
			
			$this->load_template_before('header', $data);
			$this->load_template_after('footer');
		}

		$this->load_template_before('header');
		$this->load_template_after('footer');
	}

	/**
     * action home.
     *
     */
	public function home()
	{	
		try {
			if (!isset($_SESSION['user_id'])) {
				throw new Exception('message');
			}
			$data = $this->_data;
		 	$this->_view->load_content('home',$data);
		} catch (Exception $e) {
			redirect();
		}
	}

	/**
     * action registration
     *
     */
	public function registration()
	{	
		try {
			if (isset($_SESSION['user_id'])) {
				throw new Exception('message');
			}

			$data = array();

			if (isset($_POST['fullname'])) {
				$data = array(
						    'code' => htmlspecialchars($_POST['code']),
						    'fullname' => htmlspecialchars($_POST['fullname']),
						    'username' => htmlspecialchars($_POST['username']),
						    'email' => htmlspecialchars($_POST['email']),
						    'password' => htmlspecialchars($_POST['password']),
						    're_password' => htmlspecialchars($_POST['re-password']),
						    'address' => htmlspecialchars($_POST['address']),
						    'sex' => $_POST['sex'],
						    'birthday' => $_POST['birthday'],
						    'error' => false,
						    'message' => array()
					    );

				//validate
				if (!validate($data['fullname'], 'alphabet' , 4, 30)) {
					$data['error'] = true;
					$data['message'][] = 'Fullname a-Z, length 4-30';
				}

				if (!validate($data['username'], 'alp_number_under', 4 , 30)) {
					$data['error'] = true;
					$data['message'][] = 'Username a-Z0-9 and underscore, length 4-30';
				}

				if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
					$data['error'] = true;
					$data['message'][] = 'Email invalid';
				}

				if (!validate($data['password'], 'password', 3, 20)) {
					$data['error'] = true;
					$data['message'][] = 'Password a-Z0-9, special characters !@#$%, length 3-20';
				}

				if ($data['password'] != $data['re_password']) {
					$data['error'] = true;
					$data['message'][] = 'Re-password invalid';
				}

				if ($data['password'] == $data['username']) {
					$data['error'] = true;
					$data['message'][] = 'Username and password must different';
				}

				if (strlen($data['address']) == 0) {
					$data['error'] = true;
					$data['message'][] = 'Address is required';
				}

				if ($data['code'] != $_SESSION['code_capcha']) {
					$data['error'] = true;
					$data['message'][] = 'Sercurity code invalid';
				}

				if (!(($data['sex'] ==1) || ($data['sex'] ==2))) {
					$data['error'] = true;
					$data['message'][] = 'Sex invalid';
				}

				if(!(checkdate(explode('-',$data['birthday'])[1], explode('-',$data['birthday'])[2],explode('-',$data['birthday'])[0]) && (strtotime($data['birthday'])<time()))) {
					$data['error'] = true;
					$data['message'][] = 'Birthday invalid';
				}

				if ($data['error'] == false) {

					//check exist username
					$username_check = $this->user->where('username', $data['username'])->first();
					if($username_check){
						$data['error'] = true;
						$data['message'][] = 'Username is exist';
					}

					//check exist email
					$email_check = $this->user->where('email', $data['email'])->first();
					
					if ($email_check) {
						$data['error'] = true;
						$data['message'][] = 'Email is exist';
					} 
					
					if ($data['error'] == false) {
						unset($data['error']);
						unset($data['message']);
						unset($data['re_password']);
						unset($data['code']);
						$data['password'] = md5($data['password']);

						//insert 
						if ($this->user->insert($data)) {
						 	$current_user=$this->user->get_insert();

						 	//create token
						 	$token_code = md5(time());
						 	$data_token = array(
						 		              'user_id' => $current_user['id'],
						 		              'token' => $token_code,
						 		              'type' => 1,
						 		              'status' => 0
						 		          );
						 	if ($this->token->insert($data_token)) {
						 		//send mail
						 		$header = mail_header();
							    $content_email = "Click <a href='http://dev.lampart.com.vn/user/confirm/$token_code'>here</a> to active account in <a href='http://dev.lampart.com.vn'>http://dev.lampart.com.vn</a> \n ";
							    mail('thanh_tai@lampart-vn.com', 'Active account', $content_email, $header);
						 	} 

						 	redirect('/successful');
						} else {
							$data['error'] = true;
							$data['message'][] = 'Error when create new user';
						}
					}
				}
			}

			$this->_view->load_content('registration', $data);
		} catch (Exception $e) {
			redirect('/user/home');
		}
	}

	/**
     * action successfull after regist
     *
     */
	public function successful()
	{	
		try {
			if (isset($_SESSION['user_id'])) {
				throw new Exception("Error");
			} 

			$data = $this->_data;

			$this->_view->load_content('successful');
		} catch (Exception $e) {
			redirect('/user/home');
		}
	}

	/**
     * action login
     *
     */
	public function login()
	{	
		try {
			if (isset($_SESSION['user_id'])) {
				throw new Exception("Error");
			}
			$data = $this->_data;

			if (isset($_POST['username'])) {
				$data['username'] = htmlspecialchars($_POST['username']);
				$data['password'] = htmlspecialchars($_POST['password']);
				
				try {
					//validate
					if(!(validate($data['username'], 'alp_number') && validate($data['password'], 'password'))) {
						throw new Exception("Username or password invalid");
					}

					//check user exist
					$user = $this->user->login($data['username'], $data['password']);
					if (!$user) {
						throw new Exception("Username or password invalid");
					}

					// check active status
					if ($user['status'] == 0) {
						throw new Exception("Please active account before login");
					}

					//save session if login success
					$_SESSION['user_id'] = $user['id'];
					
					redirect('/friend/index');
				} catch (Exception $e) {
					$data['message'][]= $e->getMessage();
				}
			}
			
			$this->_view->load_content('login', $data);
		} catch (Exception $e) {
			redirect('/user/home');
		}
	}

	/**
     * action profile
     *
     */
	public function profile($params)
	{	
		if (!isset($_SESSION['user_id'])) {
			redirect();
		} else {
			$data = $this->_data;
			$data['page'] = 'Profile';

			// if edit status = true is edit mode, if false is view mode
			$data['edit_status'] = false;
			
			if (isset($_POST['fullname'])) {
				$id = $this->_data['user']['id'];
				$edit_data = array(
	                             'fullname' => htmlspecialchars($_POST['fullname']),
	                             'address' => htmlspecialchars($_POST['address']),
	                             'birthday' => $_POST['birthday'],
	                             'sex' => $_POST['sex']
							 );
				// validate
				if (!validate($edit_data['fullname'], 'alphabet' , 4, 30)) {
					$data['edit_status'] = true;
					$data['message'][] = 'Fullname a-Z, length 4-30';
				}

				if (strlen($edit_data['address']) == 0) {
					$data['edit_status'] = true;
					$data['message'][] = 'Address is required';
				}

				if (!(($edit_data['sex'] ==1) || ($edit_data['sex'] ==2))) {
					$data['edit_status'] = true;
					$data['message'][] = 'Sex invalid';
				}

				if(!(checkdate(explode('-',$edit_data['birthday'])[1], explode('-',$edit_data['birthday'])[2],explode('-',$edit_data['birthday'])[0]) && (strtotime($edit_data['birthday'])<time()))) {
					$data['edit_status'] = true;
					$data['message'][] = 'Birthday invalid';
				}

				// update
				if($data['edit_status'] == false) {
					if($this->user->update_id($id, $edit_data)) {
						$data['edit_status'] = false;
						$data['user']['fullname'] = $edit_data['fullname'];
						$data['user']['address'] = $edit_data['address'];
						$data['user']['birthday'] = $edit_data['birthday'];
						$data['user']['sex'] = $edit_data['sex'];
					}
					else {
						$data['edit_status'] = true;
						$data['message'][] = 'Update profile have error';
					}
				}
			}
			
			$this->_view->load_content('profile', $data);
		}
	}

	/**
     * action change email
     *
     */
	public function change_email()
	{	
		try {
			if (!isset($_SESSION['user_id'])) {
				throw new Exception("Error");	
			} 
		    
		    $data = $this->_data;
		    $data['page'] = 'Change email';
		    $data['edit_status'] = true;
			
			if (isset($_POST['email'])) {
			    $id = $this->_data['user']['id'];
			    $email = $_POST['email'];
			    try {
			    	// validate
			    	if ($email == '') {
			        	throw new Exception("Email not empty");
				    } 
				    
				    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				    	throw new Exception("Email fomat invalid");
				    }
				   
				    $result = $this->user->where('email', $email)->first ();
				    
				    if ($result) {
					    throw new Exception("Email is exist");
					} 
					
					// create token
				    $token_code = md5(time());
				    $data_token = array(
                                      'token' => $token_code,
                                      'user_id' => $id,
                                      'content' => $email,
                                      'type' => 2,
                                      'status' => 0
				    	          );
				    $result = $this->token->insert($data_token);
				    
				    if (!$result){
				    	throw new Exception("Change email have error");
				    }
				    
				    $data['edit_status'] = false;
				    
				    // send mail
				    $header = mail_header();
				    $content_email = "Click <a href='http://dev.lampart.com.vn/lesson/user/confirm/$token_code'>here</a> to agree change email to $email \n ";
				    mail('thanh_tai@lampart-vn.com', 'Change email', $content_email, $header);
			    } catch (Exception $e) {
			    	$data['message'][] = $e->getMessage();
			    }
			}
		    
		    $this->_view->load_content('change-email', $data);
		} catch (Exception $e) {
			redirect();
		}
		
	}

	/**
     * action change password
     *
     */
	public function change_password()
	{	
		try {
			if (!isset($_SESSION['user_id'])) {
				throw new Exception("Error");
			}
			$data = $this->_data;
		    $data['page'] = 'Change password';
		    $data['edit_status'] = true;
		    
		    if (isset($_POST['password'])) {
		    	try {
		    		$data['edit_status'] = false;
			    	$id = $this->_data['user']['id'];
			    	
			    	$password = htmlspecialchars($_POST['password']);
			    	$new_password = htmlspecialchars($_POST['new-password']);
			    	$confirm_password = htmlspecialchars($_POST['confirm-password']);

			    	// validate
			    	if (($password == '') || ($new_password == '') ||($confirm_password == '') ) {
			    	    throw new Exception("Please enter all fields");
			    	}
			    	
			    	if (!(validate($password, 'password', 3, 20) && validate($new_password, 'password', 3, 20))) {
			    		throw new Exception("Password a-Z0-9, special characters !@#$%, length 3-20");
			    	}
			    	
			    	if ($new_password != $confirm_password) {
			    		throw new Exception("Confirm password invalid");
			    	}
		    		
		    		$result = $this->user->where('id', $id)->where('password', md5($password))->first();
		    		
		    		if (!$result) {
		    			throw new Exception("Current password invalid");
		    		}
	    			
	    			if ($password == $new_password) {
			    		throw new Exception("New password is current password");
			    	}
			    	
			    	// update
	    			$result = $this->user->where('id', $id)->update(array('password' => md5($new_password)));
	    			
	    			if (!$result) {
	    				throw new Exception("Change password have error");
	    			}
		    	} catch (Exception $e) {
		    		$data['edit_status'] = true;
		    		$data['message'][] = $e->getMessage();
		    	}
		    }
		   
		    $this->_view->load_content('change-password', $data);
		} catch (Exception $e) {
			redirect();
		}
	}

	/**
     * api edit profile
     *
     */
	public function update()
	{	
		try {
			if(!isset($_SESSION['user_id'])) {
				throw new Exception("Please login");
			}
			
			$user_id = $_POST['user_id'];
			$user = $this->user->find_id($user_id);
			
			if (!$user) {
				throw new Exception("User not exist");
			}
			
			$data = $this->_data;
			
			if (($data['user']['group_id'] != 1) && ($data['user']['id'] != $user_id)) {
				throw new Exception("Not have permission");
			}
			
			$edit_data = array();
			
			// validate
			if (isset($_POST['fullname'])) {
				$edit_data['fullname'] =  htmlspecialchars($_POST['fullname']);
				
				if (!validate($edit_data['fullname'], 'alphabet' , 4, 30)) {
					throw new Exception("Fullname a-Z, length 4-30");
				}
			}
			
			if (isset($_POST['address'])) {
				$edit_data['address'] =  htmlspecialchars($_POST['address']);
				
				if (strlen($edit_data['address']) == 0) {
					throw new Exception("Address is required");
				}
			}
			
			if (isset($_POST['birthday'])) {
				$edit_data['birthday'] =  htmlspecialchars($_POST['birthday']);
				
				if(!(checkdate(explode('-',$edit_data['birthday'])[1], explode('-',$edit_data['birthday'])[2],explode('-',$edit_data['birthday'])[0]) && (strtotime($edit_data['birthday'])<time()))) {
					throw new Exception("Birthday invalid");
				}
			}
			
			if (isset($_POST['sex'])) {
				$edit_data['sex'] = htmlspecialchars($_POST['sex']);
				
				if (!(($edit_data['sex'] ==1) || ($edit_data['sex'] ==2))) {
					throw new Exception("Sex invalid");
				}
			}
			
			if (isset($_POST['introduction'])) {
				$edit_data['introduction'] = htmlspecialchars($_POST['introduction']);
			}
			
			if (isset($_POST['lat'])) {
				$edit_data['lat'] = htmlspecialchars($_POST['lat']);
			}
			
			if (isset($_POST['lng'])) {
				$edit_data['lng'] = htmlspecialchars($_POST['lng']);
			}
			
			if (!($this->user->update_id($user_id, $edit_data))) {
				throw new Exception("Update profile have error");
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
     * action logout
     *
     */
	public function logout()
	{	
	    session_unset('user_id');
	    redirect();
	}

	/**
     * action delete user
     *
     */
	public function delete($params)
	{	
		try {
			if(!isset($_SESSION['user_id'])) {
				throw new Exception("Please login");
			}
			
			$data = $this->_data;
			
			if ($data['user']['group_id'] != 1) {
				throw new Exception("Not have permission");
			}
			
			// check user id
			$id = $params[0];
			$user = $this->user->find_id($id);

			if (!$user) {
				throw new Exception("Not exist user");
			}

			if (!is_numeric($id)) {
				throw new Exception("User id invalid");
			}

			if ($data['user']['id'] == $id ) {
				throw new Exception("Not delete yourself");
			}

			$friend_request = $this->friend_request->where('user_id', $id)->delete();
			$friend_list = $this->friend_list->where('user_id', $id)->or_where('user_id_to', $id)->delete();
			$image_like = $this->image_like->where('user_id', $id)->delete();
			$image = $this->image->where('user_id', $id)->delete();
			$user_change = $this->user->where('id', $id)->delete();

			$result = array('error' => false);
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}

		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * action confirm
     *
     */
	public function confirm($params)
	{	
		$data = $this->_data;
		try {
			$key = $_GET['key'];
			
			if (!validate($key, 'alp_number', 32, 32)) {
				throw new Exception("Token invalid");
			}
	        
	        $result = $this->token->where('token', $key)->where('status', 0)->first();
	        
	        if (!$result) {
	        	throw new Exception("Token not exists");
	        }
        	
        	switch ($result['type']) {
        		case 1:
        			//active account
        			$this->user->update_id($result['user_id'], array('status' => 1));
        			$this->token->where('id', $result['id'])->update(array('status' => 1));
        			
        			throw new Exception("Active account success");
        			break;
        		
        		case 2:
        		    //change email
        			$user = $this->user->where('email', $result['content'])->first();
        			
        			if ($user) {
        				throw new Exception("Email is exist");
        			} else {
        				$this->user->update_id($result['user_id'], array('email'=>$result['content']));
        			    $this->token->where('user_id', $result['user_id'])->where('status', 0)->update(array('status' => 1));
        			    throw new Exception("Change email success");
        			}
        			
        			break;

        		default:
        			throw new Exception("Type token not exist");
        			break;
        	}

		} catch (Exception $e) {
			$data['message'][] = $e->getMessage();
		}
		
		$this->_view->load_content('confirm.result',$data);
	}

	/**
     * api user_info
     *
     */
	public function info($params)
	{	
		try {
			if (!isset($_SESSION['user_id'])) {
			    throw new Exception("Please login");
			} 
			
			$data = $this->_data;
			$user_id = $params[0];
			
			if (($data['user']['group_id'] != 1) && ($data['user']['id'] != $user_id)) {
			    throw new Exception("Not have permisson");
			}
			
			$user = $this->user->find_id($user_id);
			
			if (!$user) {
				throw new Exception("User not exist");
			} 
			$result['data'] = $user;
			$result['error'] = false;
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * api dynamic edit profile
     *
     */
	public function dynamicupdate()
	{	
		try {
			if(!isset($_SESSION['user_id'])) {
				throw new Exception("Please login");
			}
			
			$data = $this->_data;
			$type = $_POST['type'];
			$content = htmlspecialchars($_POST['content']);
			$edit_data = array('key' =>'', 'value' =>$content);
			
			switch ($type) {
				case 'introduction':
					
					$edit_data['key'] = 'introduction';
					break;

				case 'fullname':
					if (!validate($content, 'alphabet' , 4, 30)) {
						throw new Exception("Fullname a-Z, length 4-30");
					}
					
					$edit_data['key'] = 'fullname';
					break;

				case 'sex':
					if (!(($content ==1) || ($content ==2))) {
						throw new Exception("Sex invalid");
					}
					
					$edit_data['key'] = 'sex';
					break;

				case 'birthday':
					if(!(checkdate(explode('-', $content)[1], explode('-', $content)[2],explode('-', $content)[0]) && (strtotime($content)<time()))) {
						throw new Exception("Birthday invalid");
					}
					
					$edit_data['key'] = 'birthday';
					break;

				case 'address':
					if (strlen($content) == 0) {
						throw new Exception("Address is required");
					}
					
					$edit_data['key'] = 'address';
					break;	

				case 'avatar':
					$image = $this->image->find_id($edit_data['value']);
					
					if (!$image) {
						throw new Exception("Image not exist");
					}
					
					$edit_data['value'] = $image['path'];
					$edit_data['key'] = 'avatar';
					break;	

				case 'email':
					$edit_data['key'] = 'email';
					$email = $edit_data['value'];
					
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				    	throw new Exception("Email fomat invalid");
				    }
				    
				    $check_email = $this->user->where('email', $email)->first ();
				    
				    if ($check_email) {
					    throw new Exception("Email is exist");
					} 
					
					// create token
				    $token_code = md5(time());
				    $data_token = array(
                                      'token' => $token_code,
                                      'user_id' => $data['user']['id'],
                                      'content' => $email,
                                      'type' => 2,
                                      'status' => 0
				    	          );
				    $token = $this->token->insert($data_token);
				    
				    if (!$token){
				    	throw new Exception("Change email have error");
				    }
				    
				    // send mail
				    $header = mail_header();
				    $content_email = "Click <a href='http://dev.lampart.com.vn/lesson/user/confirm/$token_code'>here</a> to agree change email to $email \n ";
				    mail('thanh_tai@lampart-vn.com', 'Change email', $content_email, $header);
					break;	

				default:
					throw new Exception("Type not exist");
					break;
			}

			if ($edit_data['key'] != 'email') {
				$result_update = $this->user->update_id($data['user']['id'],array($edit_data['key'] => $edit_data['value'] ));
				
				if (!$result_update) {
					throw new Exception("Update error");
				}
			}

			$result['data'] = $content;
			$result['error'] = false;
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}

		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * api change group_id
     *
     */
	public function change_group()
	{	
		try {
			$data = $this->_data;
			
			if (isset($data['error'])) {
				throw new Exception("Please login");
			}
			
			if ($this->_data['user']['group_id'] != 1) {
				throw new Exception("Not have permisson");
			}
			
			$data = $this->_data;
			$id = $_POST['user_id'];
			$group_id = $_POST['group_id'];
			
			$user = $this->user->find_id($id);

			if (!$user) {
				throw new Exception("Not exist user");
			}	
			//get array group level and check input in this array
			$groups = $this->group->get();
			$group_array= array();
			
			foreach ($groups as $group) {
				$group_array[] = $group['level'];
			}
			
			if (!in_array($group_id, $group_array)) {
				throw new Exception("Not have this group");
			}
			
			$user_change = $this->user->update_id($id, array('group_id' => $group_id));
			$result = array('error' => false);
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * action management users
     *
     */
	public function manage()
	{	
		try {
			$data = $this->_data;
			
			if (isset($data['error'])) {
				throw new UserException("Please login");
			}
			
			if ($this->_data['user']['group_id'] !=1) {
				throw new CheckException("Error group permisson");
			}
			
			$users = $this->user->get();
			
			if ($users) {
			    $data['users'] = $users;
			    $groups = $this->group->get();
			    $data['groups'] = $groups;
			} else {
				$data['message'][] = 'Not have user';
			}
		} catch (CheckException $e) {
			redirect('/friend/index');
		} catch (UserException $e) {
			redirect();
		}
	    
	    $this->_view->load_content('management', $data);
	}

	/**
     * action search user
     *
     */
	public function search()
	{	
		$data = $this->_data;
		try {
			
			if (isset($data['error'])) {
				throw new UserException("Please login");
			}
			
			if (!isset($_POST['s'])) {
				throw new CheckException("Not have content search");
			}
			
			$id = $data['user']['id'];
			$content = htmlspecialchars($_POST['s']);
			$data['search_content'] = $content;
            $result = $this->user->search_not_friend($id, $content);
            
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
		
		$this->_view->load_content('friend.search', $data);
	}
}