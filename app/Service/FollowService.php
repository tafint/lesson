<?php
namespace App\Service;

use Model\User;
use Model\Follow;
use Model\UserLogView;
use \Exception;

/**
 * This is a class FollowService
 */
class FollowService extends Service
{	
	/**
     * get data follow
     *
     */
    public function data($id)
    {	
    	$result = array();

		$follow = new Follow();
    	$follows = $follow->get_all($id);

    	$user_log_view = new UserLogView();

        foreach ($follows as $follow) {
        	$user = new User();
        	$is_view = $user_log_view->is_view($id, $follow["log_id"]);
            if ($is_view) {
                $follow['is_view'] = true;
            } else {
                $follow['is_view'] = false;
                $user_log_view->insert(array("user_id" => $id, "log_id" => $follow['log_id']));
            }
            $result[] = $follow;
        }

        return $result;
    }

    /**
     * add new follow
     *
     */
    public function add($user_id, $user_id_to)
    {	
        try {
            $user = new User();
            $user_info = $user->find_id($user_id);
            
            if (!$user_info) {
                throw new Exception("User not exist");
            } 

            if ($user_id == $user_id_to) {
                throw new Exception("Not follow yourself");
            } 
            
            $follow = new Follow();
            $is_follow = $follow->is_follow($user_id, $user_id_to);
            
            if ($is_follow) {
                throw new Exception("Have follow");
            }
            
            $follow_insert = $follow->insert(array('user_id' => $user_id, 'user_id_to' => $user_id_to));
            
            if (!$follow_insert) {
                throw new Exception("Insert error");
            }

            $result = array('error' => false);
        } catch (Exception $e) {
            $result = array("error" =>true, "message" => $e->getMessage());
        }
        
        return $result;
    }

    /**
     * remove follow
     *
     */
    public function remove($user_id, $user_id_to)
    {	
        try {
            $follow = new Follow();
            $follow_info = $follow->is_follow($user_id, $user_id_to);
            
            if (!$follow_info) {
                throw new Exception("Follow user not exist");
            } 
            
            $delete = $follow->where('user_id', $user_id)->where('user_id_to', $user_id_to)->delete();
            
            if (!$delete) {
                throw new Exception("Delete error");
            }
            
            $result = array('error' => false);
            
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
        }

        return $result;
    }
}