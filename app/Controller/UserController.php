<?php
namespace App\Controller;
use App\Service\UserService;
use \Exception;
use App\Exception\UserException as UserException;
use App\Exception\CheckException as CheckException;
/**
 * This is a class UserController
 */
class UserController extends Controller

{	
    public function __construct()
    {
    	parent::__construct();
    }

	/**
     * action home.
     *
     */
    public function home()
    {
    	try {
    		if (!isset($_SESSION['user_id'])) {
    			throw new Exception('');
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
				throw new Exception("");
			}

			$data = array();

			if (isset($_POST['fullname'])) {
				$data = array(
                            'code' => htmlspecialchars($_POST['code']),
                            'fullname' => trim(htmlspecialchars($_POST['fullname'])),
                            'username' => htmlspecialchars($_POST['username']),
                            'email' => htmlspecialchars($_POST['email']),
                            'password' => htmlspecialchars($_POST['password']),
                            're_password' => htmlspecialchars($_POST['re-password']),
                            'address' => htmlspecialchars($_POST['address']),
                            'sex' => $_POST['sex'],
                            'birthday' => $_POST['birthday'],
					    );
				$user_service = new UserService;
				$data = $user_service->registration($data);
				if ($data["error"] == false) {
					redirect("/successful");
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
					if(!(validate($data['username'], 'username') && validate($data['password'], 'password'))) {
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
				if (!validate($edit_data['fullname'], 'fullname')) {
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
			    	
			    	if (!(validate($password, 'password') && validate($new_password, 'password'))) {
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
     * action logout
     *
     */
	public function logout()
	{	
	    session_unset('user_id');
	    redirect();
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
			
			if (!validate($key, 'token')) {
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
			if ($content == "") {
				throw new CheckException("Not have content search");
			}
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