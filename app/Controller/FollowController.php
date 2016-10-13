<?php
namespace App\Controller;

use App\Service\FollowService;
use \Exception;

/**
 * This is a class FollowController
 */
class FollowController extends Controller
{   
    /**
     * action follow list
     *
     */
    public function index()
    {   
        try {
            $data = $this->_data;
            
            if (isset($data['error'])) {
                throw new Exception("Please login");
            }

            $follow_service = new FollowService();
            $follow_data = $follow_service->data($data["user"]["id"]);

            if (count($follow_data) > 0) {
                $data["follows"] = $follow_data;
            }
        } catch (Exception $e) {
            $data['error'] = true;
        }
    
        $this->_view->load_content('follow', $data);
    }
}