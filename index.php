<?php 
session_start();
define('PATH', __DIR__);

// get class Router
require_once 'core/Router.php';
$router = new Router;

// get all route have config
require_once 'route.php';

$server = $_SERVER;
$server['REQUEST_URI'] = str_replace("/lesson","",$server['REQUEST_URI']);
$app = $router->match($server,$_REQUEST);

if ($app ===null) {
	$controller = "IndexController";
	$action = "error_404";
	$args = [];
} else {
	$controller = $app['controller'];
	$action = $app['method'];
	$args = $app['args'];
}
function get_instance(){
	global $controller;
	return $controller::get_instance();
}

require_once PATH . '/core/Controller.php';

if (file_exists(PATH . '/controller/' . $controller .'.php')) {
	require_once PATH . '/controller/' . $controller .'.php';

	$ctrl= new $controller;

	if (count($args) == 0) {
		$ctrl->{$action}();
	} else {
		$ctrl->{$action}($args);
	}
} else {
	die("Error");
}