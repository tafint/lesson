<?php
/**
 * This is a class ImageLike
 */
class ImageLike extends Model

{
	public function __construct()
	{
		parent::__construct();
		// set table
		$this->_table = 'image_like';
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
        return $this->where('image_id', $id)->count();
	}

	/**
     * action like image
     *
     * @param  $user_id and $image_id.
     *
     * @return number.
     *
     */
    public function is_like($user_id, $image_id) 
    {
        $is_like = $this->where('user_id', $user_id)->where('image_id', $image_id)->first();
        
        if ($is_like) {
            return true;
        } else {
            return false;
        }
    }
}