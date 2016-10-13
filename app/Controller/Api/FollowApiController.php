<?php
namespace App\Controller\Api;

use App\Service\FollowService;
use \Exception;

/**
 * This is a class FollowApiController
 */
class FollowApiController extends ApiController
{   
    /**
     * api add follow
     *
     */
    public function add()
    {   
        try {
            $data = $this->_data;
            $user_id = $_POST['user_id'];
            $user = $this->user->find_id($user_id);
            
            if (!$user) {
                throw new Exception("User not exist");
            } 

            if ($user_id == $data['user']['id']) {
                throw new Exception("Not follow yourself");
            } 
            
            $is_follow = $this->follow->is_follow($data['user']['id'], $user_id);
            
            if ($is_follow) {
                throw new Exception("Have follow");
            }
            
            $follow = $this->follow->insert(array('user_id' => $data['user']['id'], 'user_id_to' => $user_id));
            
            if (!$follow) {
                throw new Exception("Insert error");
            }

            $this->_result = array('error' => false);

        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }

        $this->response();
    }

    /**
     * api unfollow
     *
     */
    public function remove()
    {   
        try {
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
            
            $this->_result = array('error' => false);
            
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }

        $this->response();
    }
}