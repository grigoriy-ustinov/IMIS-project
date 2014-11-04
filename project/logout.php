<?php
include('config.php');
$main['title'] = 'Logout';
$main['output'] = '';


unset($_SESSION['user']['Id']);
unset($_SESSION['user']['Account_type']);
header('Location: home.php');

include('templates/index.php');


?>

