<?php 
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 2.0.0
 * @license: see license.txt included in package
 */
 
// include db config
include_once("../../config.php");

// include and create object
include(PHPGRID_LIBPATH."inc/jqgrid_dist.php");

// master grid
// Database config file to be passed in phpgrid constructor
$db_conf = array( 	
					"type" 		=> PHPGRID_DBTYPE, 
					"server" 	=> PHPGRID_DBHOST,
					"user" 		=> PHPGRID_DBUSER,
					"password" 	=> PHPGRID_DBPASS,
					"database" 	=> PHPGRID_DBNAME
				);

$grid = new jqgrid($db_conf);

$opt["caption"] = "Clients Data";
$opt["height"] = "150";

// following params will enable subgrid -- by default first column (PK) of parent is passed as param 'id'
$opt["detail_grid_id"] = "list2";

// refresh detail grid on master edit
$opt["edit_options"]["afterSubmit"] = "function(){ jQuery('#list2').trigger('reloadGrid', [{current:true}]); return [true,'']; jQuery('#list1').setSelection(jQuery('#list1').jqGrid('getGridParam','selrow')); }";

// select after add
$opt["add_options"]["afterComplete"] = "function (response, postdata) { r = JSON.parse(response.responseText); $('#list1').setSelection(r.id); }";

// extra params passed to detail grid, column name comma separated
$opt["subgridparams"] = "client_id,gender,company";
$opt["multiselect"] = false;
$opt["export"] = array("filename"=>"my-file", "sheetname"=>"test", "format"=>"pdf");
$opt["export"]["range"] = "filtered";
$grid->set_options($opt);

$grid->table = "clients";

$grid->set_actions(array(    
                        "add"=>true, // allow/disallow add
                        "edit"=>true, // allow/disallow edit
						"delete"=>false, // allow/disallow delete
                        "rowactions"=>true, // show/hide row wise edit/del/save option
                        "export"=>true, // show/hide export to excel option
                        "autofilter" => true, // show/hide autofilter for search
                        "search" => "advance" // show single/multi field search condition (e.g. simple or advance)
                    ) 
                );
                
$out_master = $grid->render("list1");

// detail grid
$grid = new jqgrid($db_conf);


// receive id, selected row of parent grid
$id = intval($_GET["rowid"]);
$gender = $_GET["gender"];
$company = addslashes($_GET["company"]);
$cid = intval($_GET["client_id"]);

// for non-int fields as PK
// $id = (empty($_GET["rowid"])?0:$_GET["rowid"]);

$opt = array();	
$opt["sortname"] = 'id'; // by default sort grid by this field
$opt["sortorder"] = "desc"; // ASC or DESC
$opt["datatype"] = "local"; // stop loading detail grid at start
$opt["height"] = ""; // autofit height of subgrid
$opt["caption"] = "Invoice Data"; // caption of grid
$opt["multiselect"] = true; // allow you to multi-select through checkboxes
$opt["reloadedit"] = true; // reload after inline edit
$opt["footerrow"] = true;
$opt["loadComplete"] = "function(){grid_onload();}";

$opt["export"] = array("filename"=>"my-file", "sheetname"=>"test", "format"=>"pdf", "range"=>"filtered"); // export to excel parameters

// Check if master record is selected before detail addition
// $opt["add_options"]["beforeInitData"] = "function(formid){ var selr = jQuery('#list1').jqGrid('getGridParam','selrow'); if (!selr) { alert('Please select master record first'); return false; } }";

// fill detail grid add dialog with master grid id
$opt["add_options"]["afterShowForm"] = 'function() { var selr = jQuery("#list1").jqGrid("getGridParam","selrow"); jQuery("#client_id").val( selr ) }';

// reload master after detail update
$opt["onAfterSave"] = "function(){ var selr = jQuery('#list1').jqGrid('getGridParam','selrow'); jQuery('#list1').trigger('reloadGrid',[{jqgrid_page:1}]); setTimeout( function(){jQuery('#list1').setSelection(selr,true);},500 ); }";
$grid->set_options($opt);

// and use in sql for filteration
$grid->select_command = "SELECT id,client_id,invdate,amount,tax,note,total,'$company' as 'company' FROM invheader WHERE client_id = $id";

// this db table will be used for add,edit,delete
$grid->table = "invheader";

$col = array();
$col["title"] = "Id"; // caption of column
$col["name"] = "id"; // field name, must be exactly same as with SQL prefix or db field
$col["width"] = "20";
$cols[] = $col;    

$col = array();
$col["title"] = "Company"; // caption of column
$col["name"] = "company"; // field name, must be exactly same as with SQL prefix or db field
$col["width"] = "100";
$col["editable"] = false;
$col["show"] = array("list"=>true,"edit"=>true,"add"=>false,"view"=>false);
$cols[] = $col;    
        
$col = array();
$col["title"] = "Client";
$col["name"] = "client_id";
$col["width"] = "100";
$col["align"] = "left";
$col["search"] = true;
$col["editable"] = true;
$col["edittype"] = "select";
$col["formatter"] = "select";
$str = $grid->get_dropdown_values("select distinct client_id as k, name as v from clients");
$col["editoptions"] = array("value"=>":;".$str);
$col["editoptions"]["onload"]["sql"] = "select distinct client_id as k, name as v from clients"; 
$col["editoptions"]["onchange"]["sql"] = "select note as k, note as v from invheader WHERE client_id = '{client_id}'"; 
$col["editoptions"]["onchange"]["update_field"] = "note"; 
$cols[] = $col;

$col = array();
$col["title"] = "Date";
$col["name"] = "invdate";
$col["formatter"] = "date";
$col["width"] = "100";
$col["search"] = true;
$col["editable"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "Amount";
$col["name"] = "amount";
$col["width"] = "100";
$col["search"] = true;
$col["editable"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "Total";
$col["name"] = "total";
$col["width"] = "100";
$col["search"] = true;
$col["editable"] = false;
$cols[] = $col;

$col = array();
$col["title"] = "Invoices";
$col["name"] = "note";
$col["width"] = "100";
$col["search"] = true;
$col["editable"] = true;
$col["edittype"] = "select";
$str = $grid->get_dropdown_values("select distinct note as k, note as v from invheader");
$col["editoptions"] = array("value"=>":;".$str);
$cols[] = $col;

$grid->set_columns($cols);

$grid->set_actions(array(    
						"add"=>true, // allow/disallow add
                        "edit"=>true, // allow/disallow edit
                        "delete"=>true, // allow/disallow delete
                        "rowactions"=>true, // show/hide row wise edit/del/save option
                        "autofilter" => true, // show/hide autofilter for search
                        "search" => "advance" // show single/multi field search condition (e.g. simple or advance)
                    ) 
                );

$e["on_insert"] = array("add_client", null, true);
$e["on_update"] = array("update_client", null, true);
$grid->set_events($e);

function add_client(&$data)
{
    $id = intval($_GET["rowid"]);
    $data["params"]["client_id"] = $id;
    $data["params"]["total"] = $data["params"]["amount"] + $data["params"]["tax"];
}

function update_client(&$data)
{
    $id = intval($_GET["rowid"]);
    $g = $_GET["gender"] . ' client note';
    $data["params"]["note"] = $g;
    $data["params"]["client_id"] = $id;
    $data["params"]["total"] = $data["params"]["amount"] + $data["params"]["tax"];
}

// generate grid output, with unique grid name as 'list1'
$out_detail = $grid->render("list2");
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
</head>
<body>
    <div style="margin:10px">
    Master Detail Grid, on same page
    <br>
    <br>
    <?php echo $out_master ?>
    <br>
    <?php echo $out_detail; ?>
    </div>
	
	<script>
	// e.g. to show footer summary
	function grid_onload() 
	{
		var grid = $("#list2");

		// sum of displayed result
		sum = grid.jqGrid('getCol', 'total', false, 'sum'); // 'sum, 'avg', 'count' (use count-1 as it count footer row).

		// 4th arg value of false will disable the using of formatter
		grid.jqGrid('footerData','set', {total: 'Total: ' + sum.toFixed(2)}, false);
	};	
	</script>
	
	<script>
	jQuery(document).ready(function(){

		jQuery('#list2').jqGrid('navButtonAdd', '#list2_pager', 
		{
			'caption'      : 'Export Selected', 
			'buttonicon'   : 'ui-icon-extlink', 
			'onClickButton': function()
			{
				// for selected rows
				var rows = jQuery('#list2').jqGrid('getGridParam','selarrrow'); 
			

				if (rows.length)
				{
					var data = rows.join();
					
					// client_id is first column and it's data will be passed as selected row ids.
					var filter = '{"rules":[{"field":"id","op":"in","data":"'+data+'"}]}';
					
					// get parent row id
					var rowid = jQuery("#list1").jqGrid('getGridParam','selrow');
					
					window.open("<?php echo $grid->options["url"]?>" + "&rowid="+rowid+"&export=1&jqgrid_page=1&export_type=pdf&_search=true&filters="+filter);
				}	
			},
			'position': 'last'
		});
	});
	</script>
	
</body>
</html> 