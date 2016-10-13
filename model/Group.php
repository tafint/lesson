<?php
namespace Model;
use Core\DB\BaseModel as BaseModel;
use Core\DB\DB as DB;
/**
 * This is a class Group
 */
class Group extends BaseModel
{
    public function __construct(){
        parent::__construct();
        //set table
        $this->_table = 'group';
    }

}