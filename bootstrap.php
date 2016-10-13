<?php
require_once __DIR__ .  "/vendor/autoload.php";
use Config\DB;

//init database
$DB_driver = "DB" . ucfirst(strtolower(DB::DB_TYPE));
$DB_driver_class = "Core\\DB\\$DB_driver";
$db = new $DB_driver_class();
$db->connect("mysql:host=" . DB::DB_HOST . ";dbname=". DB::DB_NAME, DB::DB_USER, DB::DB_PASS);