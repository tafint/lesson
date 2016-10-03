<?php 

// start sesson and define PATH
session_start();
define('PATH', __DIR__);

// init router
require_once PATH . '/core/Router.php';
$router = new Router;

require_once PATH . '/config/route.php';

// parse controller and method from uri
$server = $_SERVER;

$server['REQUEST_URI'] = str_replace("/lesson","",$server['REQUEST_URI']);
$app = $router->match($server);

if ($app ===null) {
	$controller = "IndexController";
	$method = "error_404";
	$args = [];
} else {
	$controller = $app['controller'];
	$method = $app['method'];
	$args = $app['args'];
}

// init database
require_once PATH . "/config/db.php";
require_once PATH . "/core/db/DB.php";
require_once PATH . "/core/db/DB" .ucfirst(strtolower(DB_TYPE)). ".php";
require_once PATH . "/core/db/BaseModel.php";

$db = new DBMysql();
$db->connect("mysql:host=" . DB_HOST . ";dbname=". DB_NAME, DB_USER, DB_PASS);

// init controller
function get_instance(){
	global $controller;
	return $controller::get_instance();
}

require_once PATH . "/core/Controller.php";
if (file_exists(PATH . '/controller/' . $controller .'.php')) {
	require_once PATH . '/controller/' . $controller .'.php';

	$ctrl= new $controller();

	if (count($args) == 0) {
		$ctrl->{$method}();
	} else {
		$ctrl->{$method}($args);
	}
} else {
	die("Error");
}
