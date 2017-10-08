<?php
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 2.0.0
 * @license: see license.txt included in package
 */
extract($_POST);

$insert_fields = array();
$skip_fields = array();
foreach($fields as $k=>$v)
{
	if ($v == "0") 
	{
		$skip_fields[] = $k;
		continue;
	}
	$insert_fields[$k] = $v;
}
 
$data = unserialize($_SESSION["import_str"]);

// remove header
array_shift($data);

// delete and replace mode (otherwise append)
if ($_POST["import_mode"] == "2")
{
	$sql = "DELETE FROM $this->table";
	$res = $this->con->execute($sql);
}

$q = array_fill(0,count($insert_fields),"?");

$count=0;
foreach($data as $d)
{
	foreach($skip_fields as $v)
		unset($d[$v]);

	// fill blank for empty cells but with headers
	$left = count($insert_fields)-count($d);
	for($i=0; $i<$left; $i++)
		$d[] = "";
	
	$sql = "INSERT INTO ".$this->table." (".implode(",",$insert_fields).") VALUES (".implode(",",$q).")";
	$res = $this->con->execute($sql,$d);

	if ($res) $c++;
}

if ($c == 0)
	$msg = "Nothing imported. Please recheck the data and try again.";
else
	$msg = "$c rows imported successfully!";
?>

<!DOCTYPE html>
<html lang="en">
  <head>
		<meta charset="utf-8">
		<title>CSV Import - Finished</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
	</head>
	
	<body style="background:#FCFDFD">
		<div class="container">
		<div class="row" style="padding:10px">
			<legend>CSV Import - Finished</legend>
			<div class="well"><?php echo $msg?></div>
			<input type="button" class="btn btn-default" value="Close" onclick="closeIt();">
			<script>
			function closeIt()
			{ 
				$('.ui-dialog-titlebar-close',window.parent.document).click();
			}
			</script>
		</div>
		</div>
	</body>
</html>