<?php

$db_host='localhost';
$db_user='root';
$db_pass='';
$db_name='CRUD_!';

try
{
	$db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);

	$db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);//error dorar jonno
}
catch(PDOException $e)
{
	echo $e->getMessage();
}


?>