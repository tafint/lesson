<?php
/**
 * This is a class Image
 */
class Image extends Model

{
	public function __construct()
	{
		parent::__construct();
		// set table
		$this->_table = 'image';
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
        return $this->where('user_id', $id)->sort_by('id','DESC')->get();
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
     * count all friend
     *
     * @param  $id is user id need get friend.
     *
     * @return number.
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

}