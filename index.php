<?php 

// start session and define PATH
session_start();
define('PATH', __DIR__);

require_once PATH . "/vendor/autoload.php";

// init router
$router = new Core\Router;
$route = new Config\Route($router);
$router = $route->getRoute();

// parse uri to controller, method and argument
$server = $_SERVER;

$server['REQUEST_URI'] = str_replace("/lesson","",$server['REQUEST_URI']);
$app = $router->match($server);

if ($app === null) {
	$controller_file = "IndexController";
	$method = "error_404";
	$args = [];
} else {
	$controller_file = $app['controller'];
	$method = $app['method'];
	$args = $app['args'];
}
$controller = "Controller\\" . $controller_file;
$controller_file = str_replace("\\","/",$controller_file);

// init database
$DB_driver = "DB" . ucfirst(strtolower(Config\DB::DB_TYPE));
$DB_driver_class = "Core\\DB\\$DB_driver";
$db = new $DB_driver_class();
$db->connect("mysql:host=" . Config\DB::DB_HOST . ";dbname=". Config\DB::DB_NAME, Config\DB::DB_USER, Config\DB::DB_PASS);

// call instance
function get_instance(){
	global $controller;
	return $controller::get_instance();
}

// call controller and run behavie
if (file_exists(PATH . '/controller/' . $controller_file .'.php')) {
	require_once PATH . '/controller/' . $controller_file .'.php';

	$ctrl= new $controller();

	if (count($args) == 0) {
		$ctrl->{$method}();
	} else {
		$ctrl->{$method}($args);
	}
} else {
	die("Error");
}
