<?php

require_once 'config.php';

function db_connect()
{
    $db_connection = new PDO('mysql:host='.DB_HOSTNAME.';dbname='.DB_DATABASE, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTEN
T => false));
    $db_connection->exec("set names 'utf8'");
	return $db_connection;

}

?>
