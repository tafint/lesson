<?php 
/**
 * This is a interface DB
 */
interface DB
{
	public function connect($dsn, $user = '', $pass = '');
    public function query($query);
}