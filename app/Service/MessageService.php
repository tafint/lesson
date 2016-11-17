<<<<<<< Updated upstream
<?php
namespace App\Service;

use Model\User;
use Model\FriendList;
use Model\MessageLog;

/**
 * This is a class MessageService
 */
class MessageService extends Service
{	
	/**
     * load data for header
     *
     */
    public function data($id)
    {	
    	$message_log = new MessageLog();
    	$result = $message_log->get_all_message($id);
    	return $result;
    }

    /**
     * create message
     *
     */
    public function create($id)
    {      
            $user_id_to = $_POST['user_id_to'];
            $user_to = $this->user->find_id($user_id_to);
            
            if(!$user_to) {
                throw new Exception("Not exist user");  
            }
            
            $is_friend = $this->friend_list->is_friend($data['user']['id'], $user_to['id']);
            
            if ((!$is_friend && ($data['user']['group_id']) != 1) && ($data['user']['group_id'] != $user_to['group_id'])) {
                throw new Exception("Not have permisson");
            }
            
            $message = htmlspecialchars($_POST['message']);
            $current_message = htmlspecialchars($_POST['current_message']);
            $message_data = array(
                            'user_id' => $data['user']['id'],
                            'user_id_to' => $user_id_to,
                            'message' => $message
                            );
            $new_message = $this->message_log->insert($message_data);
            
            if (!$new_message) {
                throw new Exception("Error when insert");
            }
            
            $new_message = $this->message_log->get_message_user($data['user']['id'], $user_id_to, $current_message);
            
            foreach ($new_message as $message) {
                $user = $this->user->find_id($message['user_id']);
                $message['fullname'] = $user['fullname'];
                $result['data'][] = $message;
            }
        return $result;
    }

    /**
     * conversation
     *
     */
    public function conversations($user_id)
    {   
        $message_log = new MessageLog();
        $conversations = $message_log->get_all_con($user_id);
        $user = new User();

        foreach ($conversations as $key => $value) {
            $conver_user = $user->find_id($value);
            $result[$key] = array('id' => $value, 'fullname' => $conver_user['fullname']);
        }

        return $result;
    }
=======
<?php
namespace App\Service;

use Model\User;
use Model\FriendList;
use Model\MessageLog;

/**
 * This is a class MessageService
 */
class MessageService extends Service
{	
	/**
     * load data for header
     *
     */
    public function data($id)
    {	
    	$message_log = new MessageLog();
    	$result = $message_log->get_all_message($id);
    	return $result;
    }

    /**
     * create message
     *
     */
    public function create($id)
    {   
       $data = $this->_data;       
            $user_id_to = $_POST['user_id_to'];
            $user_to = $this->user->find_id($user_id_to);
            
            if(!$user_to) {
                throw new Exception("Not exist user");  
            }
            
            $is_friend = $this->friend_list->is_friend($data['user']['id'], $user_to['id']);
            
            if ((!$is_friend && ($data['user']['group_id']) != 1) && ($data['user']['group_id'] != $user_to['group_id'])) {
                throw new Exception("Not have permisson");
            }
            
            $message = htmlspecialchars($_POST['message']);
            $current_message = htmlspecialchars($_POST['current_message']);
            $message_data = array(
                            'user_id' => $data['user']['id'],
                            'user_id_to' => $user_id_to,
                            'message' => $message
                            );
            $new_message = $this->message_log->insert($message_data);
            
            if (!$new_message) {
                throw new Exception("Error when insert");
            }
            
            $new_message = $this->message_log->get_message_user($data['user']['id'], $user_id_to, $current_message);
            
            foreach ($new_message as $message) {
                $user = $this->user->find_id($message['user_id']);
                $message['fullname'] = $user['fullname'];
                $result['data'][] = $message;
            }
        return $result;
    }
>>>>>>> Stashed changes
}