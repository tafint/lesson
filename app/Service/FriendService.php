<?php
namespace App\Service;

use Model\User;
use Model\FriendList;
use Model\FriendRequest;
use Model\MessageLog;
use Model\Image;
use Model\ImageLike;

/**
 * This is a class FriendService
 */
class FriendService extends Service
{	
	public function info($user_id,$user_id_to)
	{
	    $data['permisson'] = false;
	    $user = new User();
	    
	    // get info
	    if ($user_id == $user_id_to){
	        $data['is_owner'] = true;
	        $data['profile'] = $data['user'];
	        $data['permisson'] = true;
	    } else {
	        $data['is_owner'] = false;
	        $user = $this->user->find_id($id);
	        if (!$user) {
	            throw new CheckException("Not exist user");
	        }
	        $data['profile'] = $this->user->find_id($id);
	    }
	    
	    // check relation
	    $result = $this->friend_list->is_friend($id, $data['user']['id']);
	    
	    if ($result) {
	        $data['is_friend'] = true;
	        $data['permisson'] = true;
	    } elseif ($data['user']['group_id'] ==1) {
	        $data['is_friend'] = false;
	        $data['permisson'] = true;
	    } elseif (($data['user']['group_id'] == $data['profile']['group_id']) && ($data['profile']['id'] != $data['user']['id'])) {
	        $data['permisson'] = true;
	        $data['is_friend'] = false;
	    } else {
	        $data['is_friend'] = false;
	    }
	    
	    if (!$data['is_friend']) {
	        $data['is_request'] = $this->friend_request->have_request($data['user']['id'], $data['profile']['id']);
	    }
	    
	    // check favorite & follow
	    $data['is_favorite'] = $this->favorite->is_favorite($data['user']['id'], $id);
	    $data['is_follow'] = $this->follow->is_follow($data['user']['id'], $id);
	    
	    // get message
	    if ($data['user']['id'] != $data['profile']['id']) {
	        $messages = $this->message_log->get_message_user($id, $data['user']['id']);
	        $data['message_log']=$messages;
	    }
	    
	    // get friend
	    if ($data['is_owner'] || $data['is_friend']) {
	        $friends = $this->friend_list->get_all($data['profile']['id']);
	        $data['friends'] = array();

	        foreach ($friends as $friend) {
	            if ($friend['user_id'] == $data['profile']['id']) {
	                $friend['user_info'] = $this->user->find_id($friend['user_id_to']);
	            } else {
	                $friend['user_info'] = $this->user->find_id($friend['user_id']);
	            }

	            if($friend['user_info']) {
	                $friend['is_friend'] = $this->friend_list->is_friend($data['user']['id'], $friend['user_info']['id']);
	                
	                if (!$friend['is_friend']) {
	                    $friend['is_request'] = $this->friend_request->have_request($data['user']['id'], $friend['user_info']['id']);
	                }

	                $data['friends'][] = $friend;
	            }
	        }

	        // get favorite
	        $data['favorites'] = array();
	        $favorites = $this->favorite->get_all($data['profile']['id']);

	        foreach ($favorites as $favorite) {
	            $favorite['user_info'] = $this->user->find_id($favorite['user_id_to']);
	            if ($favorite['user_info']) {
	                $favorite['is_friend'] = $this->friend_list->is_friend($data['user']['id'], $favorite['user_info']['id']);

	                if (!$favorite['is_friend']) {
	                    $favorite['is_request'] = $this->friend_request->have_request($data['user']['id'], $favorite['user_info']['id']);
	                }

	                $favorite['is_favorite'] = $this->favorite->is_favorite($data['user']['id'], $favorite['user_info']['id']);
	                $data['favorites'][] = $favorite;
	            }
	        }
	        
	    }
	    
	    // get conversation
	    $message_log = new MessageLog();
	    if (($data['user']['group_id'] == 1) && ($data['user']['id'] != $data['profile']['id']) && ($data['profile']['group_id'] != 1)) {
	        $conversations = $message_log->get_all_con($data['profile']['id']);
	        
	        foreach ($conversations as $key => $value) {
	            $conver_user = $user->find_id($value);
	            $result['conversations'][$key] = array('id' => $value, 'fullname' => $conver_user['fullname']);
	        }
	    }
	    
	    // get group
	    $group = new Group();
	    $groups = $group->get();
	    $result['groups'] = $groups;
	    
	    // get image
	    $image = new Image();
	    $images = $image->get_all($data['profile']['id']);
	    $result['images'] = array();
	    
	    foreach ($images as $image) {
	        
	        if ($this->image_like->is_like($data['user']['id'], $image['id'])) {
	            $image['is_like'] = true;
	        } else {
	            $image['is_like'] = false;
	        }

	        $image['like'] = $this->image_like->count_all($image['id']);
	        $result['images'][] = $image;
	    }

	    return $result;
	}

	/**
     * suggest
     *
     */
	public function suggest($user_id)
	{	
		try {
			$friend_list = new FriendList();
			$result = $friend_list->suggest_friend($user_id);

			if (!$result) {
	           throw new Exception("Not found");
	        }
	        
	        $friend_request = new FriendRequest();
	        foreach ($result as $key => $value) {   
	            $user_request = $friend_request->have_request($id, $value['id']);
	            $value['request_status'] = $user_request ? true : false;
	            $result["users"][$key] = $value;
	        }
		} catch (Exception $e) {
			$result = array("error" => true, "message" => $e->getMessage());
		}

        return $result;
	}

	/**
     * static request friend 
     *
     */
	public function request($user_id)
	{	
		try {
			$friend_request = new FriendRequest();
			$requests = $friend_request->where('user_id_to', $user_id)->get();
            
	        if (!$requests) {
	            throw new CheckException("Not have friend request");
	        }
	        
	        $user = new User();

	        $result = array("error" => false, "users" => array());

	        foreach ($requests as $key => $value) {
	            $user_info = $user->where('id',$value['user_id'])->first();

	            if ($user_info) {
	                $value['user_info'] = $user_info;
	                $result['users'][$key] = $value;
	            }
	        }
		} catch (Exception $e) {
			$result = array("error" => true, "message" => $e->getMessage());
		}
		
		return $result;
	}

	/**
     * static request friend 
     *
     */
	public function index($user_id)
	{	
		try {
			$friend_list = new FriendList();
			$list_friends = $friend_list->get_all($user_id);   
            $result = array();

            if ($list_friends) {
            	$user = new User();
                foreach ($list_friends as $key => $friend) {
                    
                    if ($friend['user_id'] == $data['user']['id']) {
                        $friend['user'] = $user->find_id($friend['user_id_to']);
                    } else {
                        $friend['user'] = $user->find_id($friend['user_id']);
                    }
                    
                    if ($friend['user']) {
                        $data['list_friends'][$key] = $friend;
                    }
                }
            }
		} catch (Exception $e) {
			$result = array("error" => true, "message" => $e->getMessage());
		}
		
		return $result;
	}
}