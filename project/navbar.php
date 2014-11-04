<?php
/**
 * Define the menu as an array
 */

if(!isset($_SESSION['user']['Id']) || !isset($_SESSION['user']['Account_type']))
{
	$loggedin = false;
	$access = false;
}
else
{
	if($_SESSION['user']['Account_type'] == 'Admin')
	{
		$access = true;
	}
	else
	{
		$access = false;
	}
	$loggedin = true;
}

$menu = array(
  // Use for styling the menu
  'class' => 'navbar',
 
  // Here comes the menu strcture
  'items' => array(
    // This is a menu item
    'Registrate'  => array(
      'text'  =>'Registrate',   
      'url'   =>'register.php',  
      'title' => 'Registrate'
    ), 
	 'Login'  => array(
      'text'  =>'Login',   
      'url'   =>'login.php',  
      'title' => 'Login'
    ), 
	'Logout'  => array(
      'text'  =>'Logout',   
      'url'   =>'logout.php',  
      'title' => 'Logout'
    ), 
    // This is a menu item
    'Settings' => array(
      'text'  =>'Settings', 
      'url'   =>'settings.php',  
      'title' => 'Settings'
    ),
	'Home' => array(
      'text'  =>'Home', 
      'url'   =>'home.php',  
      'title' => 'Home'
    ),
  ),
 
  // This is the callback tracing the current selected menu item base on scriptname
  'callback' => function($url) {
    if(basename($_SERVER['SCRIPT_FILENAME']) == $url) {
      return true;
    }
  }
);