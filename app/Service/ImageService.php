<?php
namespace App\Service;

use Model\User;
use Model\FriendList;
use Model\Image;
use Model\ImageLike;

/**
 * This is a class ImageService
 */
class ImageService extends Service
{	
	public function index($user_id, $user_id_to)
	{	
		$image = new Image();
		$image_like = new ImageLike();

		$images = $image->get_all($user_id_to);
	    $result= array();
	    
	    foreach ($images as $image) {
	        
	        if ($image_like->is_like($user_id, $image['id'])) {
	            $image['is_like'] = true;
	        } else {
	            $image['is_like'] = false;
	        }

	        $image['like'] = $image_like->count_all($image['id']);
	        $result[] = $image;
	    }

	    return $result;
	}
}