<?php
include('config.php');
checkAccess();
$main['title'] = 'Settings';
$main['output'] = '
<form method="post">
<input type="Submit" name="Setup" value="setup"/>
';

if(isset($_POST['Setup']))
{
	$query = '
	USE grus13;
	CREATE TABLE IF NOT EXISTS Login(
	Id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	Login varchar(6) NOT NULL,
	Password varchar(32) NOT NULL,
	Person_id INT NOT NULL,
	Account_type TINYTEXT,
	Care_receiver TINYINT(1),
	Care_provider TINYINT(1),
	Active TINYINT(1) DEFAULT 0);
	
	INSERT INTO Login(Login,Password,Person_id,Account_type,Active) VALUES("Admin","21232f297a57a5a743894a0e4a801fc3","1","Admin","1");
	
	CREATE TABLE IF NOT EXISTS Custom_Tables(
	Id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	TableName TINYTEXT,
	ColumnName TINYTEXT,
	ColumnType TINYTEXT);
	
	CREATE TABLE IF NOT EXISTS Group_Person(
	Id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	Group_Id INT NOT NULL,
	Person_Id INT NOT NULL);
	
	
	CREATE TABLE IF NOT EXISTS Message(
	Id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	Title TINYTEXT,
	Content TEXT,
	Sender_Id INT NOT NULL,
	Receiver_Id INT,
	Group_Id INT);
	
	CREATE TABLE IF NOT EXISTS Task_Group(
	Id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	Task_Id INT NOT NULL,
	Group_Id INT);
	
	';
	
	$db->Execute($query);

	$main['output'] .= $db->Dump();
}
$table = 'Login';

$columns = $db->getColumns($table);
$query = 'SELECT * FROM Login';
$result = $db->SelectQuery($query);

$main['output'] = '<table style="width:100%">';
$main['output'] .= '<form method="post">';
$main['output'] .= '<tr><th>Id</th>';
$main['output'] .= '<th>Login</th>';
$main['output'] .= '<th>Person Id</th>';
$main['output'] .= '<th>Account_type</th>';
$main['output'] .= '<th>Care receiver</th>';
$main['output'] .= '<th>Care provider</th>';
$main['output'] .= '<th>Active</th>';
$main['output'] .= '<th>Delete</th></tr>';
$ides =array();
foreach($result as $res)
{
	$ides[] = $res->Id;
	$main['output'] .= '<tr>';
	$main['output'] .= '<td>'.$res->Id.'</td>';
	$main['output'] .= '<td><a href="info.php?id='.$res->Person_id.'&type='.$res->Account_type.'">'.$res->Login.'</a></td>';
	$main['output'] .= '<td>'.$res->Person_id.'</td>';
	$main['output'] .= '<td>'.$res->Account_type.'</td>';
	if($res->Role == 'receiver')
	{
		$main['output'] .= '<td><input type="radio" name="role['.$res->Id.']" value="receiver" checked="true"></td>';
		$main['output'] .= '<td><input type="radio" name="role['.$res->Id.']" value="provider" ></td>';
	}
	else if($res->Role == 'provider')
	{
		$main['output'] .= '<td><input type="radio" name="role['.$res->Id.']" value="receiver"></td>';
		$main['output'] .= '<td><input type="radio" name="role['.$res->Id.']" value="provider" checked="true""></td>';
	}
	if($res->Active == true)
	{
		$main['output'] .= '<td><input type="checkbox" name="active['.$res->Id.']" checked></td>';
	}
	else
	{
		$main['output'] .= '<td><input type="checkbox" name="active['.$res->Id.']"></td>';
	}
	$main['output'] .= '<td><a class="blink" href="settings.php?delete='.$res->Id.'&type='.$res->Account_type.'&pid='.$res->Person_id.'">Delete</a></td>';
	$main['output'] .= '</tr>';
}
$main['output'] .= '<p><input type="submit" name="change" value="Change"></p>';
$main['output'] .= '</form>';
$main['output'] .= '</table>';
$main['output'] .= '<a class="blink" href="tasks.php">Manage task</a>';
$main['output'] .= '<a class="blink" href="subgroups.php">Manage subgroups</a>';
$main['output'] .= '<a class="blink" href="groups.php">Manage groups</a>';

$roles = array();
$active = array();
if(isset($_GET['delete']))
{
	$query = 'DELETE FROM Login WHERE (Id = ?) AND (Account_type = ?);';
	$id = $_GET['delete'];
	if($id == 1)
	{
		header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
		die();
	}
	$person_id = $_GET['pid'];
	$accType = $_GET['type'];
	$params = array($id,$accType);
	$db->Execute($query,$params);
	$query = 'DELETE FROM PG_'.$accType.' WHERE (Id = ?);';
	$params = array($person_id);
	$db->Execute($query,$params);
	header('Location: settings.php');
}
if(isset($_POST['change']))
{
	//print_r($_POST);
	foreach($_POST['role'] as $role)
	{
		$roles[] = $role;
	}
	foreach($ides as $id)
	{
		if(isset($_POST['active'][$id]))
		{
			array_push($active, 1) ;
		}
		else if(!isset($_POST['active'][$id]))
		{
			array_push($active, 0);
		}
	}
	$count = end($ides);
	for($i= 0; $i < $count; $i++)
	{
		if(isset($roles[$i])&&isset($active[$i])&&isset($ides[$i]))
		{
			$params = null;
			$params = array();
			$params[] = $roles[$i];
			$params[] = $active[$i];
			$params[] = $ides[$i];
			$query = 'UPDATE Login SET Role = ?,Active = ? WHERE Id = ?;';
			$db->Execute($query,$params);
		}
		//$main['output'] .= print_r($db->ErrorInfo());
	}
	header('Location: settings.php');
}




include('templates/index.php');
?>