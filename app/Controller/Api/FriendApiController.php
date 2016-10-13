<?php
namespace App\Controller\Api;

use \Exception;

/**
 * This is a class FriendApiController
 */
class FriendApiController extends ApiController
{   
    public function __construct()
    {   
        parent::__construct();
        $this->_model->load('friend_list');
        $this->_model->load('friend_request');
        $this->_model->load('favorite');
        $this->_model->load('user_log');
    }

    /**
     * api send to friend request
     *
     */
    public function add()
    {   
        try {
            $data = $this->_data;       
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
            $this->_result = array('error' => false);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        } 
        
        $this->response();
    }

    /**
     * api handle friend request
     *
     */
    public function handle()
    {   
        try {
            $data = $this->_data;
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
                $this->_result = array('error' => false);
            } else {
                $this->friend_request->where('id',$id)->delete();
                $this->_result = array('error' => false);
            }
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }

    /**
     * api unfriend
     *
     */
    public function remove()
    {   
        try {
            $data = $this->_data;       
            $user_id = $_POST['user_id'];

            $user = $this->user->find_id($user_id);
            
            if (!$user) {
                throw new Exception("User not exist");
            }
            
            if ($user['id'] == $data['user']['id']) {
                throw new Exception("Not remove friend yourself");
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
            $this->_result = array("error" => false);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }

}