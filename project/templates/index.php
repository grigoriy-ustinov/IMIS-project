<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?=$main['title']?></title>
<link rel='stylesheet' type='text/css' href='css/style.css'/>
</head>
<body>
    <div id="wrapper">
		<?=$main['navbar'];?>
        <div id="content">
        	<?=$main['output']?>
        </div>
        <?php include('footer.php');?>
    </div>
</body>
</html>
