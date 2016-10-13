<?php
namespace App\Controller\Api;
use \Exception;
/**
 * This is a class ImageController
 */
class ImageApiController extends ApiController

{   
    public function __construct()
    {   
        parent::__construct();
        $this->_model->load('friend_list');
        $this->_model->load('image');
        $this->_model->load('image_like');
    }

    /**
     * api upload image
     *
     */
    public function upload()
    {   
        try {
            $data = $this->_data;
            $msg_success = array();
            $msg_error = array();
            $images_data = array();
            $flag = false;

            for ($i = 0; $i < sizeof($_FILES["image-upload"]["name"]); $i++) {

                try {

                    $check= getimagesize($_FILES['image-upload']['tmp_name'][$i]);

                    if(!$check) {
                        throw new Exception($_FILES["image-upload"]["name"][$i] . " not is image");
                    }
                    
                    $date = date('Ymd');
                    $target_dir_original = "public/data/$date/original";
                    $target_dir_resize = "public/data/$date/resize";
                    $extension = pathinfo( $_FILES['image-upload']['name'][$i],PATHINFO_EXTENSION);
                    $target_name = time().'_'.rand(100,500).'.'.$extension;
                    $target_file_origin = $target_dir_original.'/'.$target_name;
                    $target_file_resize = $target_dir_resize.'/'.$target_name;
                    
                    // validate image
                    if(($extension != "bmp") && ($extension != "jpg") && ($extension != "png") && ($extension != "jpeg") && ($extension != "gif")) {
                        throw new Exception($_FILES["image-upload"]["name"][$i] . " type invalid");
                    }
                    
                    if ($_FILES["image-upload"]["size"][$i] > 10485760) {
                        throw new Exception($_FILES["image-upload"]["name"][$i] . " too large");
                    }

                    // create new dir 
                    if(!file_exists($target_dir_original)) {
                        mkdir($target_dir_original, 0777, true);
                    }
                    
                    // move file
                    if (!move_uploaded_file($_FILES["image-upload"]["tmp_name"][$i], $target_file_origin)) {
                        throw new Exception("Upload " . $_FILES["image-upload"]["name"][$i] . " error");
                    }

                    // create new dir risize
                    if(!file_exists($target_dir_resize)) {
                        mkdir($target_dir_resize, 0777, true);
                    }

                    // resize image
                    $target_file_open = fopen($target_file_resize, "w");

                    if (!image_resize($target_file_origin, $target_file_open, 260, 175, 1)) {
                        throw new Exception("Error when resize " . $_FILES["image-upload"]["name"][$i]);
                    }

                    // insert to database
                    $image = $this->image->insert(array('path' => $target_file_origin, 'thumbnail' => $target_file_resize, 'user_id' => $data['user']['id']));
                    
                    if (!$image) {
                        throw new Exception("Insert " . $_FILES["image-upload"]["name"][$i] ." to database error");
                    }

                    $image = $this->image->get_insert();
                    $msg_success[] = "Upload " . $_FILES["image-upload"]["name"][$i] . " success";
                    $images_data[] = array("thumbnail" => $target_file_resize, "path" => $target_file_origin, 'id' => $image['id']);

                } catch (Exception $e) {
                    $flag = true;
                    $msg_error[] = $e->getMessage();
                }
                
            }
            $this->_result = array(
                                 "error" => false,
                                 "images_data" => $images_data,
                                 "msg_success" => $msg_success,
                                 "msg_error" => $msg_error
                             );
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }

    /**
     * api delete image
     *
     */
    public function delete()
    {   
        try {
            $data = $this->_data;       
            $data = $this->_data;
            $image_id = $_POST['image_id'];
            $image = $this->image->find_id($image_id);
            
            if (!$image) {
                throw new Exception("Image not exist");
            } 
            
            if ($data['user']['avatar'] == $image['path']) {
                throw new Exception("Not delete avatar");
            }
            if ($image['user_id'] != $data['user']['id']) {
                throw new Exception("Not owner");
            }
            
            $this->image_like->where('image_id',$image_id)->delete();
            $this->image->where('id',$image_id)->delete();
            
            if(file_exists($image['path'])) {
                unlink($image['path']);
            }

            if(file_exists($image['thumbnail'])) {
                unlink($image['thumbnail']);
            }
            
            $this->_result = array('error' => false);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }

    /**
     * api like image
     *
     */
    public function like()
    {   
        try {
            $data = $this->_data;
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
            $this->_result = array('error' => false, 'like' => $like);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }

    /**
     * api unlike image
     *
     */
    public function unlike()
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
            $this->_result = array('error' => false, 'like' => $like);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }

    /**
     * api view image
     *
     */
    public function view()
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
            $this->_result = array('error' => false, 'view' => $image['view']+1);
        } catch (Exception $e) {
            $this->_result = array('error' => true, 'message' => $e->getMessage());
        }
        
        $this->response();
    }
}