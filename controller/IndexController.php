<?php
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
		
		if (isset($_SESSION['user_id'])) {
			$user = new User;
			$user = $user->find_id($_SESSION['user_id']);
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
			redirect('/home');
		}
		
		$this->_view->load_content('index');
	}

	public function error_404()
	{	
		$this->_view->load_content('404');
	}
}