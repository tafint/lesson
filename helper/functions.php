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

