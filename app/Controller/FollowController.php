<?php
namespace App\Controller;
use \Exception;
use \Exception\UserException as UserException;
use \Exception\CheckException as CheckException;
/**
 * This is a class FollowController
 */
class FollowController extends Controller

{	
	public function __construct()
	{	
		parent::__construct();
	}

	/**
     * action follow list
     *
     */
	public function index()
	{	
		try {
			$data = $this->_data;
			
			if (isset($data['error'])) {
				throw new UserException("Please login");
			}

			$follows = $this->follow->get_all($data['user']['id']);

			foreach ($follows as $follow) {
				if ($this->user_log_view->is_view($data['user']['id'], $follow['log_id'])) {
					$follow['is_view'] = true;
				} else {
					$follow['is_view'] = false;
					$this->user_log_view->insert(array("user_id" => $data['user']['id'], "log_id" => $follow['log_id']));
				}
				$data['follows'][] = $follow;
			}
		} catch (UserException $e) {
			$data['error'] = true;
		}
	
	    $this->_view->load_content('follow', $data);
	}
}