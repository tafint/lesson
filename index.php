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
$app = $router->match($server);

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

require_once PATH . "/core/Controller.php";

require_once PATH . "/core/DB.php";
require_once PATH . "/core/DBMysql.php";
require_once PATH . "/core/BaseModel.php";
require_once PATH . "/config/db.php";
$db = new DBMysql();
$db->connect("mysql:host=" . DB_HOST . ";dbname=". DB_NAME, DB_USER, DB_PASS);

if (file_exists(PATH . '/controller/' . $controller .'.php')) {
	require_once PATH . '/controller/' . $controller .'.php';

	$ctrl= new $controller();

	if (count($args) == 0) {
		$ctrl->{$action}();
	} else {
		$ctrl->{$action}($args);
	}
} else {
	die("Error");
}

die;

// if (file_exists(PATH . '/controller/' . $controller .'.php')) {
// 	require_once PATH . '/controller/' . $controller .'.php';

// 	$ctrl= new $controller();

// 	if (count($args) == 0) {
// 		$ctrl->{$action}();
// 	} else {
// 		$ctrl->{$action}($args);
// 	}
// } else {
// 	die("Error");
// }