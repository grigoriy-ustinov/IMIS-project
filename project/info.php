<?php
include('config.php');
$main['title'] = 'Profile';
$main['output'] = '<h1>Profile</h1>';

if(isset($_GET['id'])&&isset($_GET['type']))
{
	$query = 'Select ColumnName FROM Custom_Tables WHERE (TableName = ?);';
	$params = array($_GET['type']);
	$columns = $db->SelectQuery($query,$params);
	
	$id = $_GET['id'];
	$type ='PG_'.$_GET['type'];
	$query = 'SELECT * FROM '.$type.' WHERE (Id = ?);';
	$params = array($id);
	$result = $db->SelectQuery($query,$params);
	$objects = array();
	foreach($result as $res)
	{
		unset($res->Id);
		foreach($res as $r)
		{
			$objects[] = $r;
		}
	}
	$columnNames = array();
	foreach($columns as $c)
	{
		$columnNames[] = $c->ColumnName;
	}
/*	print_r($columnNames);
	print_r($objects);*/
	$key = count($objects);
	for($i = 0; $i<$key;$i++)
	{
		if(isset($columnNames[$i]))
		{
			$main['output'] .= '<p>'.$columnNames[$i];
		}
		if(isset($objects[$i]))
		{
			$main['output'] .= ': '.$objects[$i].'</p>';
		}
	}
}
else
{
	$main['output'] = 'No user has been selected.';
}

include('templates/index.php');


?>
