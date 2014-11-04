<?php
include('config.php');
checkAccess();
$main['title'] = 'Subgroups';
$main['output'] = '<h2>Manage subgroups</h2>';
$query = 'Select Id,Name, Description from SubGroups ';

$subgroups = $db->SelectQuery($query);
$result = array();
foreach($subgroups as $group)
{
	$result[] = get_object_vars($group);
}

$main['output'] .= '<table style="width:100%"><tr>';
$main['output'] .= '<tr><th>Name</th>';
$main['output'] .= '<th>Description</th>';
$main['output'] .= '<th>Delete</th>';
$main['output'] .= '<th>Add Members</th>';
$main['output'] .= '<th>View</th></tr>';
foreach($result as $res)
{
	$main['output'] .= '<tr><td>'.$res['Name'].'</td>';
	$main['output'] .= '<td>'.$res['Description'].'</td>';
	$main['output'] .= '<td><a class="blink" href="?remove='.$res['Id'].'">Remove</a></td>';
	$main['output'] .= '<td><a class="blink" href="?addto='.$res['Id'].'">Add members</a></td>';
	$main['output'] .= '<td><a class="blink" href="?view='.$res['Id'].'">View</a></td></tr>';
}
$main['output'] .= '</table>';
$main['output'] .= '
<form method="post">
<p>Name:</p>
<p> <input type="text" name="Name" value=""></p>
<p>Description:</p>
<p><textarea name="Description"></textarea></p>
<p><input type="submit" name="submit" value="Create"></p>
</form>';
if(isset($_POST['submit']))
{
	$query = 'INSERT INTO SubGroups(Name,Description) VALUES(?,?);';
	$params = array($_POST['Name'],$_POST['Description']);
	$db->Execute($query,$params);
	header('Location: subgroups.php');
}


if(isset($_GET['remove']))
{
	$id = $_GET['remove'];
	$query = 'DELETE FROM SubGroups WHERE (Id = ?) LIMIT 1;'; 
	$params = array($id);
	$db->Execute($query,$params);
	$query = 'DELETE FROM Group_Person WHERE (Group_Id = ?);';
	$db->Execute($query,$params);
	header('Location: subgroups.php');
}

if(isset($_GET['addto']))
{
	$query = 'SELECT Id,Login, Account_type FROM Login;';
	$tableId = $_GET['addto'];
	$result = $db->SelectQuery($query);
	$main['output'] = '<table style="width:100%"><tr>';
	$main['output'] .= '<tr><th>Login</th>';
	$main['output'] .= '<th>Group</th>';
	$main['output'] .= '<th>Add</th>';
	$main['output'] .= '<form method="post">';
	//print_r($result);
	foreach($result as $res)
	{
		$main['output'] .= '<tr><td>'.$res->Login.'</td>';
		$main['output'] .= '<td>'.$res->Account_type.'</td>';
		$main['output'] .= '<td><input type="checkbox" name="add[]" value="'.$res->Id.'"></td></tr>';
	}
	$main['output'] .= '</table>';
	$main['output'] .= '<input type="submit" name="addselected" value="Add selected">';
	$main['output'] .= '</from>';
	
	if(isset($_POST['addselected']))
	{
		if(isset($_POST['add']))
		{
			//print_r($_POST['add']);
			$query = 'INSERT INTO Group_Person(Group_Id, Person_Id)VALUES';
			$params = array();
			foreach($_POST['add'] as $post)
			{
				$query .='(?,?),';
				$params[] = $tableId;
				$params[] = $post;
			}
			$query = substr_replace($query ,"",-1);
			
			$query .= ';';
			$db->Execute($query,$params);
			//echo($query);
			//print_r($params);
			header('Location: subgroups.php');
		}
	}
}


if(isset($_GET['view']))
{
	$query = '
	SELECT Login.Id,Login,Account_type FROM Login 
	INNER JOIN Group_Person 
	ON Group_Person.Person_Id = Login.Id 
	WHERE (Group_Id = ?);';
	$groupId = $_GET['view'];
	$params = array($_GET['view']);
	$people = $db->SelectQuery($query,$params);
	$main['output'] = '<table style="width:100%"><tr>';
	$main['output'] .= '<tr><th>Login</th>';
	$main['output'] .= '<th>Group</th>';
	$main['output'] .= '<th>Delete</th></tr>';
	//print_r($people);
	foreach($people as $p)
	{
		$main['output'] .= '<tr><td>'.$p->Login.'</td>';
		$main['output'] .= '<td>'.$p->Account_type.'</td>';
		$main['output'] .= '<td><a class="blink" href="subgroups.php?delete='.$p->Id.'">Delete</a></td></tr>';
	}
	$main['output'] .= '</table>';
	$main['output'] .= '<a class="blink" href="subgroups.php">Go back</a>';
}
if(isset($_GET['delete']))
{
	$toDelete =$_GET['delete'];
	$query = 'DELETE FROM Group_Person WHERE (Person_Id = ?)';
	$params = array($toDelete);
	$db->Execute($query,$params);
	header('Location: subgroups.php');
}
include('templates/index.php');


?>

