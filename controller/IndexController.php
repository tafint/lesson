<?php
namespace Controller;
use Core\Controller as Controller;
/**
 * This is a class IndexController
 */
class IndexController extends Controller

{	
	public function __construct()
	{
		parent::__construct();
		
		$this->_model->load('user');
		$this->_helper->load('functions');
		
		$this->load_template_before('header');
		$this->load_template_after('footer');
		
		//check session
		if (isset($_SESSION['user_id'])) {
			$user = $this->user->find_id($_SESSION['user_id']);
			if($user) {
				$this->_data['user'] = $user ;
			}
		}
	}

	/**
     * action index
     *
     */
	public function index()
	{	
		if (isset($_SESSION['user_id'])) {
			redirect('/user/home');
		}
		
		$this->_view->load_content('wellcome');
	}

	public function error_404()
	{	
		$this->_view->load_content('404');
	}
}