<?php 
namespace Config;
use Illuminate\Database\Capsule\Manager as Capsule; 
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
/**
 * This is a class DB
 */
class Database
{
    public function __construct()
    {
        $capsule = new Capsule; 
         
        $capsule->addConnection(array(
            'driver'    => 'mysql',
            'host'      => '172.16.100.3',
            'database'  => 'nguyen_tai',
            'username'  => 'root',
            'password'  => 'lampart',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ));
        $capsule->setEventDispatcher(new Dispatcher(new Container));

        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}