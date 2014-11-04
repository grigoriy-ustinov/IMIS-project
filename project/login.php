<?php
include('config.php');
$main['title'] = 'Login';
$main['output'] = '
<form method="post">
<p>Login: <input type="text" name="Login" value=""></p>
<p>Password: <input type="password" name="Password" value=""></p>
<p><input type="submit" name="submit" value="Login"></p>';
if(isset($_POST['submit']))
{
	$query = 'SELECT Id, Account_type,Active FROM Login WHERE (Login = ?) AND (Password = ?);';
	$params = array($_POST['Login'],md5($_POST['Password']));
	$id = $db->SelectQuery($query,$params);
	if(isset($id['0']))
	{
		$id = get_object_vars($id['0']);
		if($id['Active'] != 1)
		{
			$main['output'] .= 'Your account have not yet been activated.';
		}
		else
		{
			
			$_SESSION['user']['Id'] = $id['Id'];
			$_SESSION['user']['Account_type'] = $id['Account_type'];
			header('Location: profile.php');
		}
	}
	else
	{
		$main['output'] .= 'Wrong username or password';
	}
}


include('templates/index.php');


?>

