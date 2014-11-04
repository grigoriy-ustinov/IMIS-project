<?php
include('config.php');
checkAccess();
$prefix = 'PG_';
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
$main['output'] = '';
$query = "SHOW TABLES LIKE 'PG\_%'";
$result = $db->SelectQuery($query);
$groups = array();
$object = null;
foreach($result as $res)
{
	$object = get_object_vars($res);
	$groups[] = substr($object['Tables_in_grus13 (PG\_%)'], 3);
}


$main['title'] = 'Create a group';
$main['output'] .= '
<h1>Create a group</h1>
<form method="post">
Group name: <input type="text" name="name">
Amount of attributes :<input type="number" name="columns">
<input type="submit" name="submit" value="Continue">
</table>
';
foreach($groups as $group)
{
	$main['output'] .= '<p>'.$group.' <a class="blink" href="groups.php?delete='.$group.'">delete</a></p>';
}
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
		$main['output'] .= 'You need to specify name and number of columns!';
	}
	foreach($groups as $group)
	{
		if($_SESSION['creator']['name'] == $group)
		{
			$main['output'] .= '<p>That group name already exists, pick another one.</p>';
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
	$db->Execute($result);
	//echo($result);
}
if(isset($_GET['delete']))
{
	$groupname = 'PG_'. $_GET['delete'];
	$query = 'DROP TABLE '.$groupname;
	//$params =array($taskname);
	$db->Execute($query);
	$groupname =$_GET['delete'];
	$query = 'DELETE FROM Custom_Tables WHERE (TableName = ?);';
	$params = array($groupname);
	$db->Execute($query,$params);
	$query = 'DELETE FROM Login WHERE (Account_type = ?);';
	$params = array($groupname);
	$db->Execute($query,$params);
	header('Location: groups.php');
}

include('templates/index.php');
?>