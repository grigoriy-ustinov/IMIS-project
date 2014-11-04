<?php
include('config.php');
if(!isset($_SESSION['register']['tableName'])&&(!isset($_SESSION['register']['columnNames']))&&(!isset($_SESSION['register']['columnType'])))
{
	$_SESSION['register']['tableName'] = null;
	$_SESSION['register']['columnNames'] = null;
	$_SESSION['register']['columnType'] = null;
}

$main['title'] = 'Registrate';
$main['output'] = null;
$prefix = 'PG';
$query = "SHOW TABLES LIKE '".$prefix."\_%'";

$result =$db->SelectQuery($query);
$groups = array();
$object = null;
foreach($result as $res)
{
	$object = get_object_vars($res);
	$groups[] = substr($object['Tables_in_grus13 (PG\_%)'], 3);
}
if(!isset($_POST['submit'])&& !isset($_POST['registrate']))
{
	$main['output'] = '<form method="POST">
						
						<label>Select account type: </label>
						<select name="accType"></p>';
		 foreach($groups as $group)
		 {
			 $main['output'] .= '<option value = "'.$group.'">'.$group.'</option>';
		 }
	$main['output'] .= '</select>
	<input type="submit" name="submit" value="Choose">';
}
if(isset($_POST['submit']))
{
	$query = 'SELECT TableName, ColumnName, ColumnType FROM Custom_Tables WHERE TableName = "'.$_POST['accType'].'";';
	$result = $db->SelectQuery($query);
	$main['output'] = '<form method="post">';
	$columnNames = array();
	$columnType = array();
	foreach($result as $res)
	{
		$columnType[] = $res->ColumnType;
		$columnNames[] = $res->ColumnName;
		if($res->ColumnType == 'DATETIME')
		{
			$main['output'] .='<input type="hidden" name="'.$res->ColumnName.'" value=""> ';
			continue;
		}
		$main['output'] .= '<p>'.$res->ColumnName.': <input type="text" name="'.$res->ColumnName.'" value=""> '.$res->ColumnType.'</p>';
		$tableName = $res->TableName;
		
	}
	$main['output'] .= '<p>Login*: <input type="text" name="Login" value=""></p>';
	$main['output'] .= '<p>Password: <input type="password" name="Password" value=""></p>';
	$main['output'] .= 'Login must be max 6 letters.';
	$_SESSION['register']['columnType'] = $columnType;
	$_SESSION['register']['columnNames'] = $columnNames;
	$_SESSION['register']['tableName'] =  $tableName;
	$main['output'] .= '<input type="submit" name="registrate" value="Registrate">';
}

if(isset($_POST['registrate']))
{
	$tableName = $_SESSION['register']['tableName'];
	$columnNames = $_SESSION['register']['columnNames'];
	$columnType = $_SESSION['register']['columnType'];
	$count = count($columnNames);
	$query = 'SELECT Login FROM Login WHERE (Login = ?);';
	$params = array($_POST['Login']);
	$login = $db->SelectQuery($query,$params);
	//print_r($login);
	if($login != null)
	{
		$main['output'] .= 'That login name already exists.';
	}
	else
	{
		$query = 'INSERT INTO PG_'.$tableName.'(';
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
		$db->Execute($query);
		$query = 'Select MAX(Id) FROM PG_'.$tableName.'';
		$object = $db->SelectQuery($query);
		$id = get_object_vars($object['0']);
		$role = 'receiver';
		$query = 'INSERT INTO Login(Login, Password, Person_id, Account_type,Role) VALUES(?,?,?,?,?);';
		$params = array($_POST['Login'],md5($_POST['Password']), $id['MAX(Id)'], $tableName,$role);
		$db->Execute($query,$params);
		header('Location: login.php');
	}
	
}

include('templates/index.php');


?>