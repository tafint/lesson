<?php
namespace App\Service;
use Model\User;
use Model\Token;
use Model\FriendList;
use Model\FriendRequest;
use Model\MessageLog;
use Model\Follow;
/**
 * This is a class HeaderService
 */
class HeaderService extends Service
{	
	/**
     * load data for header
     *
     */
    public function load_data($id)
    {	
    	$data = array("error" => false);
    	try {
	    	$user = new User();
			$user_find = $user->find_id($id);

			if(!$user_find) {
				throw new Exception("");
			}

			$data["user"] = $user_find;
			$data["navbar"] = true;

			$friend_list = new FriendList();
			$data['count_friend'] = $friend_list->count_all($id);

			$friend_request = new FriendRequest();
			$data['count_request'] = $friend_request->count_all($id);

			$message_log = new MessageLog();
			$data['count_message'] = $message_log->count_all($id);

			$follow = new Follow();
			$data['count_follow'] = $follow->count_all($id);
    	} catch (Exception $e) {
    		$data["error"] = true;
    	}

    	return $data;
    }
}