<?php 
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 2.0.0
 * @license: see license.txt included in package
 */

// todo: import/export settings https://phpgrid.desk.com/customer/portal/questions/16151684-export-and-import-report-settings-persistent- (24-2-16)
 
// include db config
include_once("../../config.php");

// include and create object
include(PHPGRID_LIBPATH."inc/jqgrid_dist.php");

// Database config file to be passed in phpgrid constructor
$db_conf = array( 	
					"type" 		=> PHPGRID_DBTYPE, 
					"server" 	=> PHPGRID_DBHOST,
					"user" 		=> PHPGRID_DBUSER,
					"password" 	=> PHPGRID_DBPASS,
					"database" 	=> PHPGRID_DBNAME
				);
				
$g = new jqgrid($db_conf);


$col = array();
$col["title"] = "Id"; // caption of column
$col["name"] = "id"; 
$col["width"] = "10";
$cols[] = $col;		
		
$col = array();
$col["title"] = "Client";
$col["name"] = "client_id";
$col["dbname"] = "invheader.client_id"; // this is required as we need to search in name field, not id
$col["width"] = "100";
$col["align"] = "left";
$col["search"] = true;
$col["editable"] = true;
$col["edittype"] = "select"; // render as select
# fetch data from database, with alias k for key, v for value
$str = $g->get_dropdown_values("select distinct client_id as k, name as v from clients");
$col["editoptions"] = array("value"=>$str); 

$col["stype"] = "select";
$col["searchoptions"] = array("value"=>$str); 

$col["editoptions"]["onload"]["sql"] = "select distinct client_id as k, name as v from clients"; 

$col["formatter"] = "select"; // display label, not value
$cols[] = $col;

$col = array();
$col["title"] = "Date";
$col["name"] = "invdate"; 
$col["width"] = "50";
$col["editable"] = true; // this column is editable
$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
$col["editrules"] = array("required"=>true); // and is required
$col["formatter"] = "date"; // format as date
$col["search"] = false;
$cols[] = $col;

$col = array();
$col["title"] = "Amount";
$col["name"] = "amount"; 
$col["width"] = "50";
$col["editable"] = true; // this column is editable
$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
$col["editrules"] = array("required"=>true); // and is required

$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => "",
								"decimalSeparator" => ".",
								"decimalPlaces" => 2);
								
$cols[] = $col;

$col = array();
$col["title"] = "Note";
$col["name"] = "note"; 
$col["width"] = "50";
$col["editable"] = true; // this column is editable
$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
$col["hidedlg"] = true; // hide in column selection
$cols[] = $col;

$grid["sortname"] = 'id'; // by default sort grid by this field
$grid["sortorder"] = "desc"; // ASC or DESC
$grid["caption"] = "Invoice Data"; // caption of grid
$grid["multiselect"] = true; // allow you to multi-select through checkboxes
$grid["autoresize"] = true; // allow you to multi-select through checkboxes
$grid["scroll"] = true; // allow you to multi-select through checkboxes

$grid["onAfterSave"] = "function(){ do_onload(); }";

$g->set_options($grid);

$g->set_actions(array(	
						"add"=>false, // allow/disallow add
						"edit"=>true, // allow/disallow edit
						"delete"=>true, // allow/disallow delete
						"showhidecolumns"=>true, // allow/disallow delete
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"autofilter" => true, // show/hide autofilter for search
						"search" => "advance",
					)
				);

$g->select_command = "SELECT id, invdate, invheader.client_id, amount, note FROM invheader 
						INNER JOIN clients on clients.client_id = invheader.client_id
						";

// this db table will be used for add,edit,delete
$g->table = "invheader";

// pass the cooked columns to grid
$g->set_columns($cols);

$e["js_on_load_complete"] = "do_onload";
$g->set_events($e);

// generate grid output, with unique grid name as 'list1'
$out = $g->render("list1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/themes/redmond/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/jqgrid/css/ui.jqgrid.css"></link>	
	
	<script src="../../lib/js/jquery.min.js" type="text/javascript"></script>
	<script src="../../lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="../../lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="../../lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
	
	<!-- library for checkbox in column chooser -->
	<link href="https://gitcdn.xyz/repo/wenzhixin/multiple-select/master/multiple-select.css" rel="stylesheet" />
	<script src="https://gitcdn.xyz/repo/wenzhixin/multiple-select/master/multiple-select.js"></script>	
	
	<!-- library for persistance storage -->
	<script src="//cdn.jsdelivr.net/jstorage/0.1/jstorage.min.js" type="text/javascript"></script>	
	<script src="//cdn.jsdelivr.net/json2/0.1/json2.min.js" type="text/javascript"></script>
	<script src="//cdn.rawgit.com/gridphp/jqGridState/e06788e68cd9f97db9da4bb4ba1c4a82890aed9e/jqGrid.state.js" type="text/javascript"></script>
	
</head>
<body>

	<script>
	var opts = {
		"stateOptions": {         
					storageKey: "gridState-list1",
					columns: true, // remember column chooser settings
					selection: true, // row selection
					expansion: true, // subgrid expansion
					filters: true, // subgrid expansion
					pager: true, // page number
					order: true // field ordering
		}
	};
	
	function do_onload(ids){ 

		var ids = jQuery('#list1').jqGrid('getDataIDs');
		for (var x = 0; x < ids.length; x++) {

			var i = x;
			var row =  $('#list1').getRowData(ids[x]);
			
			if (row.amount == '100.00' || parseInt(row.amount) == 100)
			{
				jQuery('#list1 tr.jqgrow:eq('+i+')').css('background','inherit').css({'background-color':'#8CBF26'});
			}
		}
		
	 }
	</script>	

	<div style="margin:10px">
	<?php echo $out?>
	<br>
	<button onclick="$('#list1').gridState().remove('gridState-list1');">Forget Settings</button>
	</div>
	
</body>
</html>
