<?php
/**
 * This is a class FriendList
 */
class FriendList extends BaseModel

{
	public function __construct(DB $db)
	{
		parent::__construct($db);
		// set table
		$this->_table = 'friend_relation';
	}

	/**
     * get all friend
     *
     * @param  $id is user id need get friend.
     *
     * @return array or false.
     *
     */
	public function get_all($id) 
	{
        return $this->where('user_id', $id)->or_where('user_id_to', $id)->get();
	}

	/**
     * count all friend
     *
     * @param  $id is user id need get friend.
     *
     * @return number.
     *
     */
	public function count_all($id) 
	{
        return $this->where('user_id', $id)->or_where('user_id_to', $id)->count();
	}

	/**
     * check relation of two users
     *
     * @param  $id is user id need get friend.
     *
     * @return true is friend, false is not friend.
     *
     */
	public function is_friend($user_id, $user_id_to) 
	{	
		$result = $this->where('user_id', $user_id)->where('user_id_to', $user_id_to)->or_where('user_id', $user_id_to)->or_where('user_id_to', $user_id)->first();
       
        if ($result) {
        	return true;
        } else {
        	return false;
        }
	}

    /**
     * return row friend of two user
     *
     * @param  $id is user id need get friend.
     *
     * @return row or false.
     *
     */
    public function friend($user_id, $user_id_to) 
    {   
        $result = $this->where('user_id', $user_id)->where('user_id_to', $user_id_to)->or_where('user_id', $user_id_to)->or_where('user_id_to', $user_id)->first();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * @param int $id is user id
     *
     * @param string $id is string need compare with fullname, username and email
     *
     * @return return array or false.
     *
     */
    public function suggest_friend($id) {
        $not_exist_relation = "SELECT * FROM `friend_relation` WHERE (friend_relation.user_id = $id AND friend_relation.user_id_to = user.id) OR (friend_relation.user_id = user.id AND friend_relation.user_id_to = $id)";
        $not_exist_request = "SELECT * FROM `friend_request` WHERE (friend_request.user_id = $id AND friend_request.user_id_to = user.id) OR (friend_request.user_id = user.id AND friend_request.user_id_to = $id)";
        $result = $this->query("SELECT * FROM `user` WHERE user.id != $id AND NOT EXISTS ($not_exist_relation) AND NOT EXISTS ($not_exist_request) ORDER BY RAND() LIMIT 6");
        return $result;
    }
}