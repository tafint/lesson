<?php
/**
 * This is a class Model
 */
class Model
{	
    /** @var string|null $_conn store connection */
	protected $_conn;


    public function __construct(DB $db)
    {   
        $this->_conn = $db;
    }

    /**
     * load new model and create new property.
     *
     * @param string $model name of model, format is lowercase and divided by underscore.
     *
     */
    public function load($model)
    {	
        $model_name=explode('_', $model);

        foreach ($model_name as $key => $value) {
            $model_name[$key] = ucfirst(strtolower($value));
        }

        $model_name=implode('', $model_name);
    	require_once PATH . '/model/'. $model_name .'.php';

        $object = get_instance();
        $object->$model = new $model_name($this->_conn);
    }

}