<?php
define('MAIN', __DIR__);
session_start();
include(MAIN."/functions.php");
include(MAIN.'/navbar.php');
$main = array();
$main['database']['dsn'] 		= 'mysql:host=blu-ray.student.bth.se;dbname=grus13;';
$main['database']['username']	= 'grus13';
$main['database']['password']	= 'n)K*[0xB';
$main['database']['drivers']	= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");


$db = new CDatabase($main['database']);
/*var_dump($loggedin);
var_dump($access);
echo($_SESSION['user']['Id']);
echo($_SESSION['user']['Account_type']);*/
if($loggedin == false)
{
	unset($menu['items']['Settings']);
	unset($menu['items']['Logout']);
}
else 
{
	if($access == false)
	{
		unset($menu['items']['Settings']);
	}
	unset($menu['items']['Registrate']);
	unset($menu['items']['Login']);
}

$main['navbar'] = get_navbar($menu);

function checkAccess()
{
	if(!isset($_SESSION['user']['Id']) ||!isset($_SESSION['user']['Account_type'])||$_SESSION['user']['Account_type'] != 'Admin')
	{
		//header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
		die('<h1>Nope.</h1>');
	}
}



$main['stylesheet'] = MAIN.'css/style.css';




?>