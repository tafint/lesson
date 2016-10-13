<?php
namespace Model;
use Core\DB\BaseModel as BaseModel;
use Core\DB\DB as DB;
/**
 * This is a class Follow
 */
class Follow extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        // set table
        $this->_table = 'follow';
    }

    /**
     * get all follow
     *
     * @param  $id is user id need get follow list.
     *
     * @return array or false.
     *
     */
    public function get_all($id) 
    {
        return $this->query("SELECT user_from.fullname as user_from_name,user_to.fullname as user_to_name,user_log.type as type,user_log.id as log_id ,user_from.id as user_from_id, user_to.id as user_to_id ,user_log.created_at as created_at from follow INNER JOIN user_log ON follow.user_id_to = user_log.user_id INNER JOIN user as user_from ON user_log.user_id = user_from.id INNER JOIN user as user_to ON user_log.user_id_to = user_to.id WHERE follow.user_id = $id ORDER BY user_log.id DESC;",'select');
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
        return $this->count_raw("SELECT COUNT(*) from follow INNER JOIN user_log ON follow.user_id_to = user_log.user_id INNER JOIN user as user_from ON user_log.user_id = user_from.id INNER JOIN user as user_to ON user_log.user_id_to = user_to.id WHERE follow.user_id = $id AND NOT EXISTS (SELECT * FROM user_log_view WHERE user_log_view.user_id=$id AND user_log_view.log_id=user_log.id);");
    }

    /**
     * check relation of two users
     *
     * @param  $id is user id need get friend.
     *
     * @return true is friend, false is not friend.
     *
     */
    public function is_follow($user_id, $user_id_to) 
    {    
        return $this->where('user_id', $user_id)->where('user_id_to', $user_id_to)->first() ? true : false;
    }
}