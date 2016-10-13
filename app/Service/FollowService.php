<?php
namespace App\Service;

use Model\User;
use Model\Follow;
use Model\UserLogView;

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
    public function add($id)
    {	

        return $result;
    }

    /**
     * remove follow
     *
     */
    public function remove($id)
    {	

        return $result;
    }
}