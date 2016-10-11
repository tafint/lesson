<?php
namespace Model;
use Core\DB\BaseModel as BaseModel;
use Core\DB\DB as DB;
/**
 * This is a class User
 */
class User extends BaseModel
{
	public function __construct(DB $db){
		parent::__construct($db);
		//set table
		$this->_table = 'user';
	}

	/**
     * @param int $id user id need get 
     *
     * @return return array or false.
     *
     */
	public function find_id($id ) {
		return $this->select("id,username,email,fullname,birthday,address,sex,group_id,introduction,lng,lat,avatar")->where("id", $id)->first();
	}

	/**
     * @param int $id user id need update 
     *
     * @return return true is update success, false is fail.
     *
     */
	public function update_id($id, $params = array()) {
		return $this->where('id',$id)->update($params);
	}

	/**
     * @param string $username and $password
     *
     * @return return array or false.
     *
     */
	public function login($username, $password) {
		return $this->where('username', $username)->where('password', md5($password))->first();
	}

	/**
     * @param int $id is user id
     *
     * @param string $id is string need compare with fullname, username and email
     *
     * @return return array or false.
     *
     */
	public function search_not_friend($id, $content) {
	    $query_not_exist = "SELECT * FROM `friend_relation` WHERE (friend_relation.user_id = $id AND friend_relation.user_id_to = user.id) OR (friend_relation.user_id = user.id AND friend_relation.user_id_to = $id)";
         $result = $this->query("SELECT * FROM `user` WHERE user.id != $id AND (fullname LIKE '%$content%'OR email LIKE '%$content%'OR username LIKE '%$content%') AND NOT EXISTS ($query_not_exist)");
         return $result;
	}

}