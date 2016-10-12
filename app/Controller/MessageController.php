<?php
namespace App\Controller;
use \Exception;
use \Exception\UserException as UserException;
use \Exception\CheckException as CheckException;
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
				throw new UserException("Please login");
			}
			
			$messages = $this->message_log->get_all_message($data['user']['id']);
			
			if (!$messages) {
				throw new CheckException("Not have message");
				
			}
			
			$data['data_messages'] = $messages;
		} catch (CheckException $e) {
			$data['message'][] = $e->getMessage();
		} catch (UserException $e) {
			redirect();
		}
	    
	    $this->_view->load_content('friend.message', $data);

	}
}