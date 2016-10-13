<?php
namespace Core;
/**
 * This is a class Model
 */
class Model
{   
    /** @var string|null $_conn store connection */
    protected $_conn;
    
    public function __construct()
    {   
        //
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

        $object = get_instance();
        $new_class = "Model\\$model_name";
        $object->$model = new $new_class();
    }

}