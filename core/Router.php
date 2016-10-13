<?php
namespace Core;
/**
 * This is a class Router
 */
class Router
{

    /** @var array|null $_routes store route config */
    protected $_routes = array(
        'GET'    => array(),
        'POST'   => array(),
        'PUT'    => array(),
        'DELETE' => array(),   
    );

    public $patterns = array(
        ':any'  => '.*',
        ':id'   => '[0-9]+',
        ':slug' => '[a-z\-]+',
        ':name' => '[a-zA-Z]+',
    );

    const REGVAL = '/({:.+?})/';    

    /**
     * load new model and create new property.
     *
     * @param string $model name of model, format is lowercase and divided by underscore.
     *
     */
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

    /**
     * add route to $this->_routes 
     *
     * @param $method GET,POST,PUT,DELETE
     *
     * @param $path uri
     *
     * @param $handle contain controller and method or funtion handle uri
     *
     */
    protected function add_route($method, $path, $handler){
        array_push($this->_routes[$method], [$path => $handler]);
    }

    /**
     * match uri width routes
     *
     * @param $server
     *
     * @return controller, method and argument or do fuction handle
     *
     */
    public function match(array $server = []){
        $request_method = $server['REQUEST_METHOD'];
        $request_uri    = $server['REQUEST_URI'];

        if (!in_array($request_method, array_keys($this->_routes))) {
            return FALSE;
        }

        $method = $request_method;

        foreach ($this->_routes[$method]  as $resource) {

            $args    = array(); 
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

    /**
     * parse uri
     *
     * @param $request_uri, $resource
     *
     * @return $arg, $resource and $route
     *
     */
    protected function parse_regex_route($request_uri, $resource){
        $route = preg_replace_callback(self::REGVAL, function($matches) {
            $patterns = $this->patterns; 
            $matches[0] = str_replace(array('{', '}'), '', $matches[0]);

            if(in_array($matches[0], array_keys($patterns))){                       
                return  $patterns[$matches[0]];
            }

        }, $resource);

        $reg_uri = explode('/', $resource); 

        $args = array_diff(array_replace($reg_uri, explode('/', $request_uri)), $reg_uri);  

        return array(array_values($args), $resource, $route); 
    }
}