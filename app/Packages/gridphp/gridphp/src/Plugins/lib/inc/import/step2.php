<?php
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 2.0.0
 * @license: see license.txt included in package
 */

// for mac/linux/win files newline detection in fgetcsv()
ini_set("auto_detect_line_endings", "1"); 
 
$data = array();
$full_data = array();

function get_delimiter($str, $checkLines = 2)
{
	$delimiters = array(
	  ',',
	  '\t',
	  ';',
	  '|',
	  ':'
	);
	$results = array();
	$i = 0;
	$lines = explode("\n",$str);
	 while($i <= $checkLines){
		$line = $lines[$i];
		foreach ($delimiters as $delimiter){
			$regExp = '/['.$delimiter.']/';
			$fields = preg_split($regExp, $line);
			if(count($fields) > 1){
				if(!empty($results[$delimiter])){
					$results[$delimiter]++;
				} else {
					$results[$delimiter] = 1;
				}   
			}
		}
	   $i++;
	}
	$results = array_keys($results, max($results));
	return $results[0];
}

// if file is uploaded
if (!empty($_FILES["csv_file"]["tmp_name"]))
{
	$f = fopen($_FILES["csv_file"]["tmp_name"], "r");
	
	$str = "";
	for($x=0;$x<5;$x++)
		$str .= fgets($f);
	$delim =  get_delimiter($str);

	fseek($f,0);

	$delim = str_replace("\\t","\t",$delim);
	while( ($line = fgetcsv($f,0,"$delim") ) !== false)
	{
		$i++;
		if ($i <= 5) 
			$data[] = $line;

		$full_data[] = $line;
	}
}
else
{
	$str = $_POST["csv_str"];
	$delim =  get_delimiter($str);
	$delim = str_replace("\\t","\t",$delim);
	
	$arr = explode("\n",$str);
	foreach($arr as $r)
	{
		$r = trim($r);
		if (empty($r)) continue;
		
		$i++;
		if ($i <= 5) 
			$data[] = explode($delim,$r);

		$full_data[] = explode($delim,$r);
	}
}

$header = array_shift($data);

$rows = array();
foreach($this->options["colModel"] as $c)
{
	$rows[] = array("field_name" => $c["name"], "title" => $c["title"]);	
}

array_unshift($rows,array("field_name"=>0,"title"=>"--none--"));

$html = "<select name='fields[]'>";
foreach($rows as $r)
{
	$html .= "<option {{select_{$r["field_name"]}}} value='{$r["field_name"]}'>{$r["title"]}</option>";
}	
$html .= "</select>";

$selection = array();
foreach($header as $d)
{
	$h = $html;
	$sel = "";
	$field = "";
	$matches = 0;
	foreach($rows as $r)
	{
		similar_text(strtolower($d),strtolower($r["field_name"]),$percent);
		// if (strtolower($d) == strtolower($r["field_name"]))
		if ($percent > 80)
		{
			$sel = "selected";
			$field = $r["field_name"];
			$matches++;
			break;
		}
	}
	$h = str_replace("{{select_$field}}",$sel,$h);
	$selection[] = $h;	
}

// if data header not sent
if ($_POST["first_row_label"] != "1")
{
	array_unshift($data,$header);
	$header = array_fill(0, count($header), 'X.?');
	array_unshift($full_data,$header);
}

// put imported data in session for step3
$_SESSION["import_str"] = serialize($full_data);

// append dropdown for fields
array_unshift($data,$selection);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
		<meta charset="utf-8">
		<title>CSV Import - Step 2</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	</head>
	<body style="background:#FCFDFD">
		<div class="container">
		<div class="row" style="padding:10px">
			<legend>CSV Import - Step 2</legend>
			<form method="post">
				<input type="hidden" name="step" value="3">
				<input type="hidden" name="import" value="1">
				<div width="90%" style="overflow:auto; margin-bottom:20px;">
				<?php
				if (empty($data) || empty($data[0]))
				{
					echo "Nothing to import, Recheck data to import and try again.";
					$class = "hidden";
				}
				else
				{
				?>
				<table class="table table-striped">
					<caption>Map imported data on fields</caption>
					<thead>
					<tr>
						<?php foreach($header as $v) { ?>
						<th>
							<?php echo $v ?>
							<input type="hidden" name="csv_fields[]" value="<?php echo $v ?>">
						</th>
						<?php } ?>
					</tr>
					</thead>
					<tbody>
					
					<?php foreach($data as $r) { ?>
					<tr>
						<?php foreach($r as $v) { ?>
						<td>
							<?php echo $v ?>
						</td>
						<?php } ?>					
					</tr>
					<?php } ?>					
					</tbody>
				</table>
				<?php 
				} 
				?>
				</div>
				
				<!-- Check -->
				<div style="padding-bottom:10px">
					<div>
					<label style="font-weight:normal;"><input id="append_label" value="1" name="import_mode" type="radio" checked> Append Rows</label>
					</div>
					<div>
					<label style="font-weight:normal;"><input id="append_label" value="2" name="import_mode" type="radio"> Delete & Replace </label>
					</div>
				</div>
				
				<a class="btn btn-default" href="?step=1&import=1">Back</a>
				<input type="submit" class="<?php echo $class ?> btn btn-default" value="Next" onclick="this.value = 'Please wait ...'; return true;">
			</form>
		</div>

		</div>
	</body>
</html>