<?php
namespace App\Service;

use Model\User;
use Model\Token;
use Model\FriendList;
use Model\FriendRequest;
use Model\MessageLog;
use Model\Follow;

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
}