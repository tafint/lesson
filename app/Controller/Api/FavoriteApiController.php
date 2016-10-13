<?php
namespace App\Controller\Api;
use \Exception;
/**
 * This is a class FavoriteController
 */
class FavoriteApiController extends ApiController

{   
    public function __construct()
    {   
        parent::__construct();
        $this->_model->load('favorite');
    }

    /**
     * api delete image
     *
     */
    public function add()
    {   
        try {

            $data = $this->_data;
            $user_id = $_POST['user_id'];
            $user = $this->user->find_id($user_id);
            
            if (!$user) {
                throw new Exception("User not exist");
            } 

            if ($user_id == $data['user']['id']) {
                throw new Exception("Not follow yourself");
            } 
            
            $is_favorite = $this->favorite->is_favorite($data['user']['id'], $user_id);
            
            if ($is_favorite) {
                throw new Exception("Have favorite");
            }

            $favorite = $this->favorite->insert(array('user_id' => $data['user']['id'], 'user_id_to' => $user_id));
            
            if (!$favorite) {
                throw new Exception("Insert error");
            }

            $this->_result = array('error' => false);

        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }

        $this->response();
    }

    /**
     * api delete image
     *
     */
    public function remove()
    {   
        try {

            $data = $this->_data;
            $user_id = $_POST['user_id'];
            $user = $this->user->find_id($user_id);
            
            if (!$user) {
                throw new Exception("User not exist");
            }

            $favorite = $this->favorite->is_favorite($data['user']['id'], $user_id);
            
            if (!$favorite) {
                throw new Exception("Favorite user not exist");
            } 

            $delete = $this->favorite->where('user_id',$data['user']['id'])->where('user_id_to', $user_id)->delete();
            
            if (!$delete) {
                throw new Exception("Delete error");
            }

            $this->_result = array('error' => false);

        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }
}