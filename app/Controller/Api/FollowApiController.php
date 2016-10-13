<?php
namespace App\Controller\Api;

use App\Service\FollowService;
use \Exception;

/**
 * This is a class FollowApiController
 */
class FollowApiController extends ApiController
{   
    /**
     * api add follow
     *
     */
    public function add()
    {   
        $data = $this->_data;
        $user_id_to = $_POST['user_id'];

        $follow_service = new FollowService();
        $this->_result = $follow_service->add($data["user"]["id"], $user_id_to);

        $this->response();
    }

    /**
     * api unfollow
     *
     */
    public function remove()
    {   
        $data = $this->_data;
        $user_id_to = $_POST['user_id'];
        
        $follow_service = new FollowService();
        $this->_result = $follow_service->remove($data["user"]["id"], $user_id_to);

        $this->response();
    }
}