<?php

/**
  * function validate
  *
  * @param string $min_length and $max_length default =0 will not check.
  *
  * @return void
  */
function validate($string, $type, $min_length = 0, $max_length = 0)
{	
	$result = false ;
	switch ($type) {

		case 'fullname' :  
		$pattern = '/^[a-zA-Z\s]+$/';
		if (preg_match($pattern, $string)) {
			$result = true;
			$min = (strlen($string) >= 4) ? true : false;
			$max = (strlen($string) <= 30) ? true : false;
			if ($min && $max) {
				$result = true;
			}
		}
		break;

		case 'alphabet' :  
		$pattern = '/^[a-zA-Z]+$/';
		if (preg_match($pattern, $string)) {
			$min = ($min_length != 0) ? ((strlen($string) >= $min_length) ? true :false) : true;
			$max = ($max_length != 0) ? ((strlen($string) <= $max_length) ? true :false) : true;
			if ($min && $max) {
				$result = true;
			}
		}
		break;

		case 'alp_number' :
		$pattern = '/^[a-zA-Z0-9]+$/';
		if (preg_match($pattern, $string)) {
			$min = ($min_length != 0) ? ((strlen($string) >= $min_length) ? true :false) : true;
			$max = ($max_length != 0) ? ((strlen($string) <= $max_length) ? true :false) : true;
			if ($min && $max) {
				$result = true;
			}
		}
		break;
		case 'alp_number_under' :
		$pattern = '/^([a-zA-Z0-9]+)([a-zA-Z0-9\_]*)([a-zA-Z0-9]+)$/';
		if (preg_match($pattern, $string)) {
			$min = ($min_length != 0) ? ((strlen($string) >= $min_length) ? true :false) : true;
			$max = ($max_length != 0) ? ((strlen($string) <= $max_length) ? true :false) : true;
			if ($min && $max) {
				$result = true;
			}
		}
		break;

		case 'password' :
		$pattern = '/^[a-zA-Z0-9\@\#\$\%\!]+$/';
		if (preg_match($pattern, $string)) {
			$min = ($min_length != 0) ? ((strlen($string) >= $min_length) ? true :false) : true;
			$max = ($max_length != 0) ? ((strlen($string) <= $max_length) ? true :false) : true;
			if ($min && $max) {
				$result = true;
			}
		}
		break;

		default : 
		break;
	}
	return $result;
}

/**
  * @param string|null $url
  *
  * @return user function header to redirect $url
  *
  */
function redirect($url = '')
{	
	$url = 'http://dev.lampart.com.vn/lesson/'.ltrim($url,'/');
	header("Location: $url");
}

/**
  * @param string|null $url
  *
  * @return return url after add host name
  *
  */
function url($url = '') {
	return "http://$_SERVER[HTTP_HOST]$url";
}

/**
  * @return return mail header 
  *
  */
function mail_header() {
	$headers = "From: noreply@dev.lampart.com.vn \r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8 \r\n";
	return $headers;
}

/**
  * @return return resize
  *
  */
function image_resize($src, $dst, $width, $height, $crop = 0){

	if (!list($w, $h) = getimagesize($src)) {
		return "Unsupported picture type!";
	}

	$type = strtolower(substr(strrchr($src,"."),1));

	if ($type == 'jpeg') {
		$type = 'jpg';
	}

	switch($type){

		case 'bmp': 
		    $img = imagecreatefromwbmp($src); 
		    break;

		case 'gif': 
		    $img = imagecreatefromgif($src); 
		    break;
		case 'jpg': 
		    $img = imagecreatefromjpeg($src); 
		    break;
		case 'png': 
		    $img = imagecreatefrompng($src); 
		    break;
		default : 
		    return "Unsupported picture type!";
	}

    // resize
	if ($crop) {
		if ($w < $width or $h < $height) {
			return "Picture is too small!";
		}
		$ratio = max($width / $w, $height / $h);
		$h = $height / $ratio;
		$x = ($w - $width / $ratio) / 2;
		$w = $width / $ratio;
	}
	else {
		if ($w < $width and $h < $height) {
			return "Picture is too small!";
		}
		$ratio = min($width / $w, $height / $h);
		$width = $w * $ratio;
		$height = $h * $ratio;
		$x = 0;
	}

	$new = imagecreatetruecolor($width, $height);

  // preserve transparency
	if ($type == "gif" or $type == "png") {
		imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
		imagealphablending($new, false);
		imagesavealpha($new, true);
	}

	imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

	switch ($type) {
		case 'bmp': 
		    imagewbmp($new, $dst); 
		    break;
		case 'gif': 
		    imagegif($new, $dst); 
		    break;
		case 'jpg': 
		    imagejpeg($new, $dst); 
		    break;
		case 'png': 
		    imagepng($new, $dst); 
		    break;
	}

	return true;
}

