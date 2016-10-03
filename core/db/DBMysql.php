<?php 
/**
 * This is a class DBMysql inplements DB
 */
class DBMysql implements DB
{
	protected  $db;

	/**
     * connect databse
     *
     * @param  $dsn, $user, $pass info to connect MYSQL DATABASE
     *
     */
	public function connect($dsn, $user = '', $pass = '')
	{	
		$this->db = new PDO($dsn, $user, $pass);
	}

	/**
     * run query
     *
     * @return  result
     *
     */
    public function query($query)
    {
    	return $this->db->query($query);
    }
}