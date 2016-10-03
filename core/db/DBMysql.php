<?php 
/**
 * This is a class DBMysql inplements DB
 */
class DBMysql implements DB
{
	protected  $db;

	public function connect($dsn, $user = '', $pass = '')
	{	
		$this->db = new PDO($dsn, $user, $pass);
	}

    public function query($query)
    {
    	return $this->db->query($query);
    }
}