<?php
namespace Model;
use Core\DB\BaseModel as BaseModel;
use Core\DB\DB as DB;
/**
 * This is a class FriendRequest
 */
class FriendRequest extends BaseModel

{
	public function __construct()
	{
		parent::__construct();
		// set table
		$this->_table = 'friend_request';
	}

	/**
     * check friend request of two users
     *
     * @return true is have request, false is not.
     *
     */
	public function have_request($user_id, $user_id_to)
	{
        $request = $this->where('user_id', $user_id)->where('user_id_to', $user_id_to)->or_where('user_id_to', $user_id)->or_where('user_id', $user_id_to)->first();
        
        if ($request) {
        	return true;
        } else {
        	return false;
        }
	}

	/**
     * count all friend request
     *
     * @param  $id is user id need get friend request.
     *
     * @return number.
     *
     */
	public function count_all($id) 
	{
        return $this->where('user_id_to', $id)->count();
	}
}