<?php
/**
 * This is a class ImageController
 */
class ImageController extends Controller

{	
	protected $_data = array ();

	public function __construct()
	{	
		parent::__construct();
		$this->_model->load('user');
		$this->_model->load('friend_list');
		$this->_model->load('image');
		$this->_model->load('image_like');
		
		//check if exist session user_id, redirect to index page if not exist session
		try {
			if (!isset($_SESSION['user_id'])) {
				throw new Exception("Error");
			}
			
			$user = $this->user->find_id($_SESSION['user_id']);
			
			if(!$user) {
				session_unset('user_id');
				throw new Exception("Error");
			}
			
			$this->_data['user'] = $user ;
			$data = $this->_data;
		} catch (Exception $e) {
			$this->_data['error'] = true;
		}
	}

	/**
     * api upload image
     *
     */
	public function upload_image()
	{	
		try {
			if (!isset($_SESSION['user_id'])) {
			    throw new Exception("Please login");
			} 
			
			$data = $this->_data;
			$check = getimagesize($_FILES['image-upload']['tmp_name']);
			
			if(!$check) {
				throw new Exception("Image not exist");
			}
			
			$target_dir = 'public/data/'.date('Ymd');
			$extension = pathinfo( $_FILES['image-upload']['name'],PATHINFO_EXTENSION);
			$target_name = time().'_'.rand(100,500).'.'.$extension;
			$target_file = $target_dir.'/'.$target_name;
			
			// validate image
			if(($extension != "jpg") && ($extension != "png") && ($extension != "jpeg") && ($extension != "gif")) {
				throw new Exception("Image type invalid");
			}
			
			if ($_FILES["image-upload"]["size"] > 10485760) {
			    echo "Sorry, your file is too large.";
			    throw new Exception("Image too large");
			}
			
			// create new dir 
			if(!file_exists($target_dir)) {
				mkdir($target_dir,0777);
			}
			
			// move file
			if (!move_uploaded_file($_FILES["image-upload"]["tmp_name"], $target_file)) {
				throw new Exception("Upload Error");
			}
			
			// insert to database
			$image = $this->image->insert(array('path' => $target_file, 'user_id' => $data['user']['id']));
			
			if (!$image) {
				throw new Exception("Insert to database error");
			}
			
			$result['error'] = false;
			$result['path'] = $target_file;
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		$this->_view->reset();
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * api delete image
     *
     */
	public function delete_image()
	{	
		try {
			$data = $this->_data;
			
			if (isset($data['error'])) {
				throw new Exception("Please login");
			}
			
			$data = $this->_data;
			$image_id = $_POST['image_id'];
			$image = $this->image->find_id($image_id);
			
			if (!$image) {
				throw new Exception("Image not exist");
			} 
			
			if ($image['user_id'] != $data['user']['id']) {
				throw new Exception("Not owner");
			}
			
			$this->image_like->where('image_id',$image_id)->delete();
			$this->image->where('id',$image_id)->delete();
			
			if(file_exists($image['path'])) {
				unlink($image['path']);
			}
			
			$result = array('error' => false);
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * api like image
     *
     */
	public function like_image()
	{	
		try {
			$data = $this->_data;
			
			if (isset($data['error'])) {
				throw new Exception("Please login");
			}
			
			$data = $this->_data;
			$image_id = $_POST['image_id'];
			$image = $this->image->find_id($image_id);
			
			if (!$image) {
				throw new Exception("Image not exist");
			} 
			
			if (!$this->friend_list->is_friend($data['user']['id'], $image['user_id'])&& ($data['user']['id'] != $image['user_id'])) {
				throw new Exception("Not is friend");
			}
			
			$is_like = $this->image_like->is_like($data['user']['id'], $image_id) ;
			
			if ($is_like) {
				throw new Exception("Have liked");
			}
			
			$image_like = $this->image_like->insert(array('user_id' => $data['user']['id'], 'image_id' => $image_id));
			
			if (!$image_like) {
				throw new Exception("Like error");
			}
			
			$like = $this->image_like->count_all($image_id);
			$result = array('error' => false, 'like' => $like);
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * api unlike image
     *
     */
	public function unlike_image()
	{	
		try {
			$data = $this->_data;
			$image_id = $_POST['image_id'];
			$image = $this->image->find_id($image_id);
			
			if (!$image) {
				throw new Exception("Image not exist");
			} 
			
			if (!$this->friend_list->is_friend($data['user']['id'], $image['user_id']) && ($data['user']['id'] != $image['user_id'])) {
				throw new Exception("Not is friend");
			}
			
			$is_like = $this->image_like->is_like($data['user']['id'], $image_id); 
			
			if (!$is_like) {
				throw new Exception("Not exist like");
			}
			
			$unlike = $this->image_like->where('user_id', $data['user']['id'])->where('image_id', $image_id)->delete();
			
			if (!$unlike) {
				throw new Exception("Unlike error");
			}
			
			$like = $this->image_like->count_all($image_id);
			$result = array('error' => false, 'like' => $like);
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	/**
     * api view image
     *
     */
	public function view_image()
	{	
		try {
			$data = $this->_data;
			$image_id = $_POST['image_id'];
			$image = $this->image->find_id($image_id);
			
			if (!$image) {
				throw new Exception("Image not exist");
			} 
			
			if (!$this->friend_list->is_friend($data['user']['id'], $image['user_id'])) {
				throw new Exception("Not is friend");
			}
			
			$increase_view = $this->image->where('id',$image_id)->update(array('view' => ($image['view']+1)));
			$result = array('error' => false, 'view' => $image['view']+1);
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => $e->getMessage());
		}
		
		header('Content-Type: application/json');
		echo json_encode($result);
	}
}