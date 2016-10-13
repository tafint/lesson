<?php
namespace Model;
use Core\DB\BaseModel as BaseModel;
use Core\DB\DB as DB;
/**
 * This is a class MessageLog
 */
class MessageLog extends BaseModel

{
	public function __construct(){
		parent::__construct();
		// set table
		$this->_table = 'message_log';
	}

	/**
     * @param int $id user id need get message
     *
     * @return all message in inbox.
     *
     */
	public function get_all_message($id) {
		return $this->query("SELECT user.fullname as fullname,message_log.id as id,message_log.message as message FROM `user` JOIN `message_log` ON user.id = message_log.user_id WHERE message_log.user_id_to = $id;");
	}

	/**
     * @param int $user_1 and $user_2 is use id need get message
     *
     * @param int $from_id message id start
     *
     * @return array message in conversation between $user_1 and $user_2
     *
     */
	public function get_message_user($user_1, $user_2, $from_id = 0) 
	{	
		if ($from_id == 0) {
			return $this->select('user_id,message,id')->where('user_id', $user_1)->where('user_id_to', $user_2)->or_where('user_id_to', $user_1)->or_where('user_id', $user_2)->get();
		} else {
			return $this->select('user_id,message,id')->where('user_id', $user_1)->where('user_id_to', $user_2)->where('id', $from_id, '>')->or_where('user_id_to', $user_1)->or_where('user_id', $user_2)->or_where('id', $from_id, '>')->get();
		}
	}

	/**
     * @param int $id is use id need get conversation
     *
     * @return array user id have conversation with user.
     *
     */
	public function get_all_con($id) 
	{	
		// get all conversation in inbox
		$message_to = $this->select('user_id')->where('user_id_to', $id)->group_by('user_id')->get();
		$message_to_array = array();
		foreach ($message_to as $msg) {
			$message_to_array[] = $msg['user_id'];
		}
		// get all conversation in send
		$message_from = $this->select('user_id_to')->where('user_id', $id)->group_by('user_id_to')->get();
		$message_from_array = array();
		foreach ($message_to as $msg) {
			$message_from_array[] = $msg['user_id'];
		}
		return array_unique(array_merge($message_to_array, $message_from_array));
	}

	/**
     * @param int $id is use id need count message
     *
     * @return number message
     *
     */
	public function count_all($id) 
	{
        return $this->where('user_id_to', $id)->count();
	}
}