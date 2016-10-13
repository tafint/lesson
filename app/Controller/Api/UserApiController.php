<?php
namespace App\Controller\Api;

use \Exception;

/**
 * This is a class UserApiController
 */
class UserApiController extends ApiController
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
        $this->_model->load('image');
        $this->_model->load('friend_list');
        $this->_model->load('friend_request');
        $this->_model->load('image_like');
        $this->_model->load('group');
        $this->_model->load('follow');
        $this->_model->load('message_log');
    }


    /**
     * api edit profile
     *
     */
    public function update()
    {   
        try {   
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
                
                if (!validate($_POST['fullname'], 'fullname')) {
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
            
            $this->_result = array("error" => false);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }

    /**
     * action delete user
     *
     */
    public function delete($params)
    {   
        try {
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

            $this->_result = array('error' => false);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }

        $this->response();
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
            $this->_result = array("error" => false, "data" => $user);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }

    /**
     * api dynamic edit profile
     *
     */
    public function dynamicupdate()
    {   
        try {       
            $data = $this->_data;
            $type = $_POST['type'];
            $content = trim(htmlspecialchars($_POST['content']));
            $edit_data = array('key' =>'', 'value' =>$content);
            
            switch ($type) {
                case 'introduction':
                    
                    $edit_data['key'] = 'introduction';
                    break;

                case 'fullname':
                    if (!validate($content, 'fullname')) {
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
                    
                    $check_email = $this->user->where('email', $email)->where('id',$data['user']['id'],'!=')->first ();
                    
                    if ($check_email) {
                        throw new Exception("Email is exist");
                    } 
                    break;  

                default:
                    throw new Exception("Type not exist");
                    break;
            }

            $result_update = $this->user->update_id($data['user']['id'],array($edit_data['key'] => $edit_data['value'] ));
            
            if (!$result_update) {
                throw new Exception("Update error");
            }

            $this->_result = array("error" => false, "value" => $edit_data["value"], "data" =>$content);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }

        $this->response();
    }

    /**
     * api change group_id
     *
     */
    public function change_group()
    {   
        try {
            $data = $this->_data;
            
            if ($this->_data['user']['group_id'] != 1) {
                throw new Exception("Not have permisson");
            }

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
            $this->_result = array('error' => false);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }

}