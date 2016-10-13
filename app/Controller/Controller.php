<?php
namespace App\Controller;

use Core\Controller as BaseController;
use App\Service\HeaderService;
use \Exception;
use \Exception\UserException as UserException;
use \Exception\CheckException as CheckException;

/**
 * This is a class FollowController
 */
abstract class Controller extends BaseController
{   
    protected $_data = array ();

    public function __construct()
    {   
        parent::__construct();
        $this->_model->load('user');
        $this->_model->load('follow');
        $this->_model->load('friend_list');
        $this->_model->load('friend_request');
        $this->_model->load('message_log');
        $this->_model->load('user_log');
        $this->_model->load('user_log_view');
        $this->_model->load('follow');
        $this->_model->load('group');
        $this->_model->load('token');
        //check session
        try {

            if (!isset($_SESSION['user_id'])) {
                throw new Exception("Error");
            }

            $header_service = new HeaderService();
            $data = $header_service->load_data($_SESSION['user_id']);

            if ($data["error"] == true) {
                session_unset('user_id');
                throw new Exception("Error");
            } 

            $this->_data['user'] = $data["user"];
            $this->load_template_before('header', $data);
            $this->load_template_after('footer');
        } catch (Exception $e) {
            $this->_data['error'] = true;
            $this->load_template_before('header');
            $this->load_template_after('footer');
        }
    }

    
}