<?php
include('config.php');
$main['title'] = 'Profile';
$main['output'] = '<h1>My profile</h1>';

$myId = $_SESSION['user']['Id'];

$query = 'SELECT Group_Id FROM Group_Person WHERE (Person_Id = ?);';
$params = array($myId);

$myGroups = $db->SelectQuery($query,$params);
if($myGroups != null)
{
	foreach($myGroups as $gr)
	{
		$query = 'SELECT Task_Name FROM Task_Group WHERE (Group_Id = ?)';
		$params = array($gr->Group_Id);
		$taskNames = $db->SelectQuery($query,$params);
	}
	
	$properties = array();
	if(!isset($_POST['submit'])&& !isset($_POST['registrate']))
	{
		$main['output'] = '<form method="POST">
							
							<label>Select task: </label>
							<select name="accType"></p>';
			 foreach($taskNames as $task)
			 {
				 $main['output'] .= '<option value = "'.$task->Task_Name.'">'.$task->Task_Name.'</option>';
			 }
		$main['output'] .= '</select>
		<input type="submit" name="submit" value="Choose">';
	}
	if(isset($_POST['submit']))
	{
		$tableName = $_POST['accType'];
		$query = 'SELECT TableName, ColumnName, ColumnType FROM Custom_Tables WHERE TableName = "'.$_POST['accType'].'";';
		$result = $db->SelectQuery($query);
		$main['output'] .= '<h2>'.$tableName.'</h2>';
		$main['output'] .= '<form method="post">';
		$columnNames = array();
		$columnType = array();
		foreach($result as $res)
		{
			$columnType[] = $res->ColumnType;
			$columnNames[] = $res->ColumnName;
			//$properties['form'][''.$task->Task_Name.'']['columns'] = $res->ColumnName;
			if($res->ColumnType == 'DATETIME')
			{
				$main['output'] .='<input type="hidden" name="'.$res->ColumnName.'" value=""> ';
				continue;
			}
			$main['output'] .= '<p>'.$res->ColumnName.': <input type="text" name="'.$res->ColumnName.'" value=""> '.$res->ColumnType.'</p>';
			$tableName = $res->TableName;
		}
		$main['output'] .= '<input type="submit" name="save" value="Submit">';
		$main['output'] .= '</form>';
		$_SESSION['register']['columnType'] = $columnType;
		$_SESSION['register']['columnNames'] = $columnNames;
		$_SESSION['register']['tableName'] =  $tableName;
	}
	if(isset($_POST['save']))
	{
		$tableName = $_SESSION['register']['tableName'];
		$columnNames = $_SESSION['register']['columnNames'];
		$columnType = $_SESSION['register']['columnType'];
		$count = count($columnNames);
	
		$query = 'INSERT INTO T_'.$tableName.'(';
		foreach($columnNames as $column)
		{
			$query .= ''.$column.',';
		}
		$query = substr($query, 0, -1);
		$query .= ') VALUES(';
		for($i = 0;$i < $count; $i++)
		{
			if($columnType[$i] == 'DATETIME')
			{
				$query .= 'NOW(),';
			}
			else
			{
				$query .= '"'. $_POST[''.$columnNames[$i].''] .'"'. ',';
			}
		}
		$query = substr($query, 0, -1);
		$query .= ');';
		//echo($query);
		$db->Execute($query);
		header('Location: profile.php');
			
	}
}

include('templates/index.php');


?>
