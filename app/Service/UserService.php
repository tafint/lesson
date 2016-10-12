<?php
namespace App\Service;
/**
 * This is a class UserService
 */
class UserService extends Service
{	
	/**
     * action registration
     *
     */
    public function registration($data = array())
    {	
    	$data["error"] = false;
    	$data["message"] = array();

        if (!validate($data['fullname'], 'fullname')) {
			$data['error'] = true;
			$data['message'][] = 'Fullname a-Z, length 4-30';
		}

		if (!validate($data['username'], 'user_name')) {
			$data['error'] = true;
			$data['message'][] = 'Username a-Z0-9 and underscore, length 4-30';
		}

		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			$data['error'] = true;
			$data['message'][] = 'Email invalid';
		}

		if (!validate($data['password'], 'password')) {
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

		return $data;
    }
}