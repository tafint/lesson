<?php 
/**
 * This is a interface Controller
 */
interface DB
{
	public function connect($dsn, $user = '', $pass = '');
    public function query($query);
}