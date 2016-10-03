<?php
/**
 * This is a class Favorite
 */
class Favorite extends BaseModel

{
	public function __construct(DB $db)
	{
		parent::__construct($db);
		// set table
		$this->_table = 'favorite';
	}

	/**
     * get all favorite
     *
     * @param  $id is user id need get friend.
     *
     * @return array or false.
     *
     */
	public function get_all($id) 
	{
        return $this->where('user_id', $id)->get();
	}

	/**
     * count all favorite
     *
     * @param  $id is user id need get friend.
     *
     * @return number.
     *
     */
	public function count_all($id) 
	{
        return $this->where('user_id', $id)->count();
	}

    /**
     * find row id
     *
     * @param  $id is user id need get favorite.
     *
     * @return row or false.
     *
     */
    public function find_id($id) 
    {
        $result = $this->where('id', $id)->first();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

	/**
     * check is favorite
     *
     * @param  $id is user id need get friend.
     *
     * @return true is $user_id favorite $user_id_to, false is not
     *
     */
	public function is_favorite($user_id, $user_id_to) 
	{	
		$result = $this->where('user_id', $user_id)->where('user_id_to', $user_id_to)->first();
        if ($result) {
        	return true;
        } else {
        	return false;
        }
	}
}