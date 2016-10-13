<?php
namespace App\Controller;

use App\Service\MessageService;
use \Exception;
use App\Exception\UserException as UserException;
use App\Exception\CheckException as CheckException;

/**
 * This is a class MessageController
 */
class MessageController extends Controller
{   
    public function __construct()
    {   
        parent::__construct();
    }

    /**
     * action view message in inbox
     *
     */
    public function index()
    {
        $data = $this->_data;
        try {
            if (isset($data['error'])) {
                throw new Exception("Please login");
            }

            $message_service = new MessageService();
            $message_data = $message_service->data($data['user']['id']);

            if ($message_data) {
                $data['data_messages'] = $message_data;
            } else {
                $data["message"] = "Not have message";
            }
            
        } catch (Exception $e) {
            redirect();
        }
        
        $this->_view->load_content('friend.message', $data);

    }
}