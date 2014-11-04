<?php
include('config.php');
checkAccess();
$prefix = 'T_';
if(!isset($_SESSION['creator']['name'])&&(!isset($_SESSION['creator']['columns'])&&(!isset($_SESSION['creator']['prefix']))))
{
	$_SESSION['creator']['columns'] = null;
	$_SESSION['creator']['name'] = null;
	$_SESSION['creator']['prefix'] = $prefix;
}
if($_SESSION['creator']['prefix'] != $prefix)
{
	$_SESSION['creator']['prefix'] = $prefix;
}
$main['title'] = 'Create a task';
$main['output'] = '<h1>Manage tasks</h1>';
$query = "SHOW TABLES LIKE 'T\_%'";
$result = $db->SelectQuery($query);
$tasks = array();
$object = null;
foreach($result as $res)
{
	$object = get_object_vars($res);
	$tasks[] = substr($object['Tables_in_grus13 (T\_%)'], 2);
}
foreach($tasks as $task)
{
	$main['output'] .= '<p>Assign task <a class="blink" href="tasks.php?task='.$task.'">'.$task.'</a> or 
	<a class="blink" href="tasks.php?delete='.$task.'">delete</a>or <a class="blink" href="tasks.php?view='.$task.'">view</a></p>';
}

$main['output'] .= '
<h2>Create a task</h2>
<form method="post">
Task name: <input type="text" name="name">
Amount of attributes :<input type="number" name="columns">
<input type="submit" name="submit" value="Continue">
</table>
';
$continue = null;
if(isset($_POST['submit']))
{
	$continue = null;
	if(!empty($_POST['name'])&&!empty($_POST['columns'])&& is_numeric($_POST['columns'])&&$_POST['columns'] > 1)
	{
		$_SESSION['creator']['name'] = $_POST['name'];
		$_SESSION['creator']['columns'] = $_POST['columns'];
		$continue = true;
	}
	else
	{
		$main['output'] .= '<p>You need to specify name and number of columns!</p>';
	}
	foreach($tasks as $task)
	{
		if($_SESSION['creator']['name'] == $task)
		{
			$main['output'] .= '<p>That task name already exists, pick another one.</p>';
			$continue = false;
		}
	}
}
$creator = new CTableCreator();
if($continue == true)
{
	
	$main['output'] = $creator->formCreator();
}
if(isset($_POST['save']))
{
	$columnNames = $_POST['columnName'];
	$dataType = $_POST['dataType'];
	$result = $creator->queryBuilder($columnNames,$dataType);
	//echo($result);
	$db->Execute($result);
	header('Location: tasks.php');
	
}
if(isset($_GET['task']))
{
	//getting id of groups that already asigned with this task
	$query = 'SELECT Group_Id FROM Task_Group WHERE (Task_Name = ?)';
	$taskname = $_GET['task'];
	$params = array($taskname);
	$Ides = $db->SelectQuery($query,$params);
	
	$query = 'SELECT Name, Id FROM SubGroups;';
	$groups = $db->SelectQuery($query);
	$main['output'] = '<form method="post">';
	//print_r($groups);
	$outputed = array();
	foreach($groups as $group)
	{
		foreach($Ides as $id)
		{
			if($id->Group_Id == $group->Id)
			{
				$outputed[] = $group->Id;
			}
		}
		if(in_array($group->Id,$outputed))
		{
						$main['output'] .= '<p>'.$group->Name.'<input type="checkbox" name="add[]" value="'.$group->Id.'"checked="true"></p>';
			}
			else
			{
				$main['output'] .= '<p>'.$group->Name.'<input type="checkbox" name="add[]" value="'.$group->Id.'"></p>';
			}
	}
	//print_r($outputed);
	$main['output'] .= '<p><input type="submit" value="Assign" name="assign"></p>
						</form>';
	
	if(isset($_POST['assign']))
	{
		$query ='INSERT INTO Task_Group(Task_name,Group_id) VALUES';
		$params = array();
		if(isset($_POST['add']))
		{
			foreach($_POST['add'] as $post)
			{
				$query .='(?,?),';
				$params[] = $taskname;
				$params[] = $post;
			}
			$query = substr_replace($query ,"",-1);
		}
		$query .= ';';
		$db->Execute($query,$params);
		header('Location: tasks.php');
		
	}
}
if(isset($_GET['delete']))
{
	$taskname = 'T_'. $_GET['delete'];
	$query = 'DROP TABLE '.$taskname;
	//$params =array($taskname);
	$db->Execute($query);
	$taskname =$_GET['delete'];
	$query = 'DELETE FROM Custom_Tables WHERE (TableName = ?);';
	$params = array($taskname);
	$db->Execute($query,$params);
	$query = 'DELETE FROM Task_Group WHERE (Task_Name = ?);';
	$params = array($taskname);
	$db->Execute($query,$params);
	header('Location: tasks.php');
}

if(isset($_GET['view']))
{
	$taskname = 'T_'.$_GET['view'];
	$query = 'Select ColumnName FROM Custom_Tables WHERE (TableName = ?);';
	$params = array($_GET['view']);
	$columns = $db->SelectQuery($query,$params);
	$query = 'SELECT * FROM '.$taskname;
	$result = $db->SelectQuery($query);
	foreach($result as $res)
	{
		unset($res->Id);
	}
	$data = array();
	foreach($result as $res)
	{
		$data[] = get_object_vars($res);
	}
	$main['output'] = '<table style="width:100%">';
	$main['output'] .= '<tr>';
	$names = array();
	foreach($columns as $c)
	{
		$main['output'] .= '<th>'.$c->ColumnName.'</th>';
		$names[] = $c->ColumnName;
	}
	$main['output'] .= '</tr>';
	
	foreach($data as $d)
	{
		$main['output'] .= '<tr>';
		foreach($d as $o)
		{
			$main['output'] .= '<td>'.$o.'</td>';
		}
		$main['output'] .= '</tr>';
	}
	$main['output'] .= '</table>';
	$main['output'] .= '<p><a class="blink" href="tasks.php">Back</a></p>';
/*	print_r($result);
	print_r($columns);
	print_r($data);*/
/*	$query = 'SELECT * FROM '.$taskname;
	$result = $db->SelectQuery($query);
	$main['output'] = '<pre>'.print_r($result).'</pre>';*/
}
include('templates/index.php');


?>

