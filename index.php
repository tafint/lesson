<?php 
// start session and define PATH
session_start();
define('PATH', __DIR__);

require_once PATH . "/vendor/autoload.php";
use Config\DB;
use Config\Route;
use Core\Router;

// init router
$router = new Router;
$route = new Route($router);
$router = $route->getRoute();

// parse uri to controller, method and argument
$server = $_SERVER;

$server['REQUEST_URI'] = str_replace("/lesson", "", $server['REQUEST_URI']);
$app = $router->match($server);

if ($app === null) {
	$controller = "App\\Controller\\IndexController";
	$method = "error_404";
	$args = [];
} else {
	$controller = "App\\Controller\\" . $app['controller'];
	$method = $app['method'];
	$args = $app['args'];
}

// init database
$DB_driver = "DB" . ucfirst(strtolower(DB::DB_TYPE));
$DB_driver_class = "Core\\DB\\$DB_driver";
$db = new $DB_driver_class();
$db->connect("mysql:host=" . DB::DB_HOST . ";dbname=". DB::DB_NAME, DB::DB_USER, DB::DB_PASS);

// call instance
function get_instance(){
	global $controller;
	return $controller::get_instance();
}

// call controller and run behavie
if (class_exists ($controller)) {
	$ctrl= new $controller();

	if (count($args) == 0) {
		$ctrl->{$method}();
	} else {
		$ctrl->{$method}($args);
	}
} else {
	die("Error : Not exist controller");
}
