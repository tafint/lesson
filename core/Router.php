<?php
/**
 * This is a class Router
 */
class Router{

protected $routes = [
				    'GET'    => [],
					'POST'   => [],
				    'PUT'    => [],
					'DELETE' => [],   
					];

public $patterns = [
    ':any'  => '.*',
    ':id'   => '[0-9]+',
    ':slug' => '[a-z\-]+',
    ':name' => '[a-zA-Z]+',
];

const REGVAL = '/({:.+?})/';    

public function any($path, $handler){
    $this->add_route('GET', $path, $handler);
    $this->add_route('POST', $path, $handler);
    $this->add_route('PUT', $path, $handler);
    $this->add_route('DELETE', $path, $handler);
}

public function get($path, $handler){
    $this->add_route('GET', $path, $handler);
}

public function post($path, $handler){
    $this->add_route('POST', $path, $handler);
}

public function put($path, $handler){
    $this->add_route('PUT', $path, $handler);
}

public function delete($path, $handler){
    $this->add_route('DELETE', $path, $handler);
}

protected function add_route($method, $path, $handler){
    array_push($this->routes[$method], [$path => $handler]);
}

public function match(array $server = []){
    $request_method = $server['REQUEST_METHOD'];
    $request_uri    = $server['REQUEST_URI'];

    if (!in_array($request_method, array_keys($this->routes))) {
        return FALSE;
    }

    $method = $request_method;

    foreach ($this->routes[$method]  as $resource) {

        $args    = []; 
        $route   = key($resource); 
        $handler = reset($resource);

        if(preg_match(self::REGVAL, $route)){
            list($args, $uri, $route) = $this->parse_regex_route($request_uri, $route);  
        }
       
        if(!preg_match("#^$route$#", $request_uri)){
            unset($this->routes[$method]);
            continue ;
        } 

        if(is_string($handler) && strpos($handler, '@')){
            list($ctrl, $method) = explode('@', $handler); 
            return ['controller' => $ctrl, 'method' => $method, 'args' => $args];
        }

        if(empty($args)){
            return $handler(); 
        }

         return call_user_func_array($handler, $args);

      }

      header('HTTP/1.1 404');
 }

protected function parse_regex_route($request_uri, $resource){
    $route = preg_replace_callback(self::REGVAL, function($matches) {
        $patterns = $this->patterns; 
        $matches[0] = str_replace(['{', '}'], '', $matches[0]);

        if(in_array($matches[0], array_keys($patterns))){                       
            return  $patterns[$matches[0]];
        }

    }, $resource);


    $reg_uri = explode('/', $resource); 

    $args = array_diff(
                array_replace($reg_uri, 
                explode('/', $request_uri)
            ), $reg_uri
        );  

    return [array_values($args), $resource, $route]; 
}
}