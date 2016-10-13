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
            if (isset($this->_data["error"])) {
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
            if (!isset($this->_data["error"])) {
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
            if (!isset($this->_data["error"])) {
                throw new Exception("Error");
            }
            $data = array();

            if (isset($_POST['username'])) {

				$user_service = new UserService();
		        $user_info = array(
		            "username" => htmlspecialchars($_POST['username']),
		            "password" => htmlspecialchars($_POST['password'])
		        );
		        $data = $user_service->login($user_info);

                if ($data["error"] == false) {
                    $_SESSION['user_id'] = $data["user"]["id"];
                    redirect('/friend/index');
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
				
				$user_service = new UserService();
				$change_result = $user_service->change_profile($id, $edit_data);

				if($change_result["error"]) {
					$data["edit_status"] = true;
					$data["message"] = $change_result["message"];
				} else {
					$data['user']['fullname'] = $change_result['fullname'];
					$data['user']['address'] = $change_result['address'];
					$data['user']['birthday'] = $change_result['birthday'];
					$data['user']['sex'] = $change_result['sex'];
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
			    $user_service = new UserService();
			    $change_result = $user_service->change_email($id, $email);

			    if (!$change_result["error"]) {
			    	$data['edit_status'] = false;
			    } else {
			    	$data['message'][] = $change_result["message"];
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
			    	$data_change = array(
			    		"id" => $this->_data['user']['id'],
			    		"password" =>  htmlspecialchars($_POST['password']),
			    		"new_password" => htmlspecialchars($_POST['new-password']),
			    		"confirm_password" => htmlspecialchars($_POST['confirm-password'])
			    	);

			    	$user_service = new UserService();
			    	$change_result = $user_service->change_password($data_change);
			    	if($change_result["error"]) {
			    		throw new Exception($change_result["message"]);
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
		try {
			if (!isset($this->_data['error'])) {
				throw new Exception("Error");
			}
			$data = $this->_data;
			try {
				$key = $params[0];
				
				if (!validate($key, 'token')) {
					throw new Exception("Token invalid");
				}
		        
				$user_service = new UserService;
				$confirm_result = $user_service->confirm($key);

				if ($confirm_result["error"]) {
					throw new Exception($confirm_result["message"]);
				}

				$data["error"] = false;
				$data['message'][] = $confirm_result["message"];
			} catch (Exception $e) {
				$data["error"] = true;
				$data['message'][] = $e->getMessage();
			}
			
			$this->_view->load_content('confirm.result',$data);
		} catch (Exception $e) {
			redirect('/user/home');
		}
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
			
			$user_service = new UserService;
			$user_info = array(
				"group_id" => $data["user"]["group_id"]
			);
			$result = $user_service->manage($user_info);

			if ($result["error"] == true) {
				throw new CheckException("Not have permisson");
			}

			$data['users'] = $result["users"];
			$data['groups'] = $result["groups"];
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
			$key = htmlspecialchars($_POST['s']);
			if ($key == "") {
				throw new CheckException("Not have content search");
			}

			$user_service = new UserService;
			$data["users"] = $user_service->search($id, $key);
			$data['search_content'] = $key;
		} catch (CheckException $e) {
			$data['message'][]=$e->getMessage();
		} catch (UserException $e) {
			redirect();
		}
		
		$this->_view->load_content('friend.search', $data);
	}
}