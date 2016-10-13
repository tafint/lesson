<?php
namespace App\Service;
use Model\User;
use Model\Token;
use Model\Group;
use Model\FriendRequest;
use \Exception;
/**
 * This is a class UserService
 */
class UserService extends Service
{   
    /**
     * registration new user
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

        if (!validate($data['username'], 'username')) {
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
            $user = new User;

            //check exist username
            $username_check = $user->where('username', $data['username'])->first();
            if($username_check){
                $data['error'] = true;
                $data['message'][] = 'Username is exist';
            }
    
            //check exist email
            $email_check = $user->where('email', $data['email'])->first();
            
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
                if ($user->insert($data)) {
                    
                    $current_user=$user->get_insert();

                    //create token
                    $token_code = md5(time());
                    $data_token = array(
                                      'user_id' => $current_user['id'],
                                      'token' => $token_code,
                                      'type' => "account",
                                      'status' => 0
                                  );
                    $token = new Token;
                    if ($token->insert($data_token)) {
                        $header = mail_header();
                        $content_email = "Click <a href='http://dev.lampart.com.vn/user/confirm/$token_code'>here</a> to active account in <a href='http://dev.lampart.com.vn'>http://dev.lampart.com.vn</a> \n ";
                        @mail('thanh_tai@lampart-vn.com', 'Active account', $content_email, $header);
                    } 

                    $data= array("error" => false);
                } else {
                    $data['error'] = true;
                    $data['message'][] = 'Error when create new user';
                }
            }
        }
        $result = $data;
        return $result;
    }

    /**
     * login
     *
     */
    public function login($data = array())
    {   
        try {
            //validate
            if(!(validate($data['username'], 'username') && validate($data['password'], 'password'))) {
                throw new Exception("Username or password invalid");
            }

            //check user exist
            $user = new User();
            $user = $user->login($data['username'], $data['password']);

            if (!$user) {
                throw new Exception("Username or password invalid");
            }

            // check active status
            if ($user['status'] == 0) {
                throw new Exception("Please active account before login");
            }

            $result = array("error" => false, "user" => $user);
        } catch (Exception $e) {
            $result["error"] = true;
            $result['message'][]= $e->getMessage();
        }
        return $result;
    }

    /**
     * manage
     *
     *@return users and groups if level is admin
     * 
     */
    public function manage($data = array())
    {   
        try {

            if ($data["group_id"] != 1) {
                throw new Exception("");
            }
            
            $user = new User();
            $users = $user->get();
            $result['users'] = $users ? $users : array();

            $group = new Group();
            $groups = $group->get();
            $result['groups'] = $groups;

            $result["error"] = false;
        } catch (Exception $e) {
            $result["error"] = true;
        }

        return $result;
    }

    /**
     * search
     *
     *@return return array users match content search
     * 
     */
    public function search($id, $key)
    {   
        $user = new User();
        $result = $user->search_not_friend($id, $key);

        foreach ($result as $key => $value) {   
            $friend_request = new FriendRequest();
            $user_request = $friend_request->have_request($id, $value['id']);
            $value['request_status'] = $user_request ? true : false;
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * confirm
     * 
     */
    public function confirm($key)
    {   
        try {
            $token = new Token();
            $token_info = $token->where('token', $key)->where('status', 0)->first();
            if (!$token_info) {
                throw new Exception("Token not exists");
            }
            
            $confirm_class = "App\\Service\\Confirm\\Confirm" . ucfirst(strtolower($token_info["type"]));
            if (!class_exists($confirm_class)) {
                throw new Exception("Type token not exists");
            }

            $confirm = new  $confirm_class($token_info);
            $confirm_result = $confirm->confirm();
            
            if (!$confirm_result["status"]) {
                throw new Exception($confirm_result["message"]);
            }

            $result["error"] = false;
            $result["message"] = $confirm_result["message"];
        } catch (Exception $e) {
            $result = array("error" => true, "message" => $e->getMessage());
        }

        return $result;
    }

    /**
     * change email
     *
     *@return return array users match content search
     * 
     */
    public function change_email($id, $email)
    {
        try {
            // validate
            if ($email == '') {
                throw new Exception("Email not empty");
            } 
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Email fomat invalid");
            }
            
            $user = new User();
            $result = $user->where('email', $email)->first ();

            if ($result) {
                throw new Exception("Email is exist");
            } 

            $token = new Token();
            $result = $token->where('content', $email)->where("user_id", $id)->where("status", 0)->first ();

            if ($result) {
                throw new Exception("This request exists");
            } 
            
            // create token
            $token_code = md5(time());
            $data_token = array(
                'token' => $token_code,
                'user_id' => $id,
                'content' => $email,
                'type' => "email",
                'status' => 0
            );

            
            $result = $token->insert($data_token);
            
            if (!$result){
                throw new Exception("Change email have error");
            }

            //send mail
            $header = mail_header();
            $content_email = "Click <a href='http://dev.lampart.com.vn/lesson/user/confirm/$token_code'>here</a> to agree change email to $email \n ";
            @mail('thanh_tai@lampart-vn.com', 'Change email', $content_email, $header);

            $result = array("error" => false);
        } catch (Exception $e) {
            $result = array("error" => true, "message" => $e->getMessage());
        }

        return $result;
    }

    /**
     * change password
     *
     */
    public function change_password($params = array())
    {   
        try {
            $id = $params["id"];
            $password = $params["password"];
            $new_password = $params["new_password"];
            $confirm_password = $params["confirm_password"];

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
            
            $user = new User();
            $result = $user->where('id', $id)->where('password', md5($password))->first();
            
            if (!$result) {
                throw new Exception("Current password invalid");
            }
            
            if ($password == $new_password) {
                throw new Exception("New password is current password");
            }
            
            // update
            $result = $user->where('id', $id)->update(array('password' => md5($new_password)));
            
            if (!$result) {
                throw new Exception("Change password have error");
            }

            $result = array("error" => false);
        } catch (Exception $e) {
            $result = array("error" => true, "message" =>$e->getMessage());
        }
        
        return $result;
    }

    /**
     * profile
     * 
     */
    public function change_profile($id, $params = array())
    {   
        $result = $params;
        $flag = false;
        // validate
        if (!validate($params['fullname'], 'fullname')) {
            $flag = true;
            $result['message'][] = 'Fullname a-Z, length 4-30';
        }

        if (strlen($params['address']) == 0) {
            $flag = true;
            $result['message'][] = 'Address is required';
    }

        if (!(($params['sex'] == 1) || ($params['sex'] == 2))) {
            $flag = true;
            $result['message'][] = 'Sex invalid';
        }

        if(!(checkdate(explode('-',$params['birthday'])[1], explode('-',$params['birthday'])[2],explode('-',$params['birthday'])[0]) && (strtotime($params['birthday'])<time()))) {
            $flag = true;
            $result['message'][] = 'Birthday invalid';
        }

        // update
        if(!$flag) {
            $user = new User();
            if($user->update_id($id, $params)) {
                $result = $params;
                $result['error'] = false;
            }
            else {
                $result['error'] = false;
                $result['message'][] = 'Update profile have error';
            }
        } else {
            $result['error'] = true;
        }

        return $result;
    }
}   