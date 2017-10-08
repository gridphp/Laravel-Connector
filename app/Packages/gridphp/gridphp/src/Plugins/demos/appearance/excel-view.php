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

// Database config file to be passed in phpgrid constructor
$db_conf = array( 	
					"type" 		=> PHPGRID_DBTYPE, 
					"server" 	=> PHPGRID_DBHOST,
					"user" 		=> PHPGRID_DBUSER,
					"password" 	=> PHPGRID_DBPASS,
					"database" 	=> PHPGRID_DBNAME
				);

$g = new jqgrid($db_conf);

$grid["caption"] = "Invoice Data"; // caption of grid
$grid["autowidth"] = true; // expand grid to screen width
$grid["export"] = array("filename"=>"my-file", "sheetname"=>"test"); // export to excel parameters

// excel visual params
$grid["cellEdit"] = true; // inline cell editing, like spreadsheet
$grid["rownumbers"] = true;
$grid["rownumWidth"] = 30;

// after submit event
// $grid["afterSaveCell"] = "function(){ alert('refresh code'); }";

$grid["autowidth"] = false;
$grid["shrinkToFit"] = false; // dont shrink to fit on screen
$grid["width"] = "900";

$g->set_options($grid);

$g->set_actions(array(	
						"add"=>true, // allow/disallow add
						"edit"=>true, // allow/disallow edit
						"delete"=>true, // allow/disallow delete
						"clone"=>true, // allow/disallow delete
						"export"=>true, // show/hide export to excel option
						"autofilter" => true, // show/hide autofilter for search
						"search" => "advance" // show single/multi field search condition (e.g. simple or advance)
					) 
				);

// you can provide custom SQL query to display data
$g->select_command = "SELECT * FROM invheader";

// this db table will be used for add,edit,delete
$g->table = "invheader";


$col = array();
$col["title"] = "Id"; // caption of column
$col["name"] = "id"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$col["width"] = "30";
$col["frozen"] = true;
$col["editable"] = false;
$cols[] = $col;

$col = array();
$col["title"] = "Date"; // caption of column
$col["name"] = "invdate"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$col["width"] = "80";
$col["editable"] = false;
$cols[] = $col;

$col = array();
$col["title"] = "Note"; // caption of column
$col["name"] = "note"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$col["width"] = "350";
$col["editable"] = true;
$col["edittype"] = "textarea";
$col["editoptions"]["dataInit"] = "function(){ fix_textarea_enter(); }";
$cols[] = $col;

// pass the cooked columns to grid
$g->set_columns($cols,true);

// server-validation & custom events work on excel view, but only first (pk) and changed column is available
$e["on_update"] = array("update_client", null, true);
$g->set_events($e);

function update_client($data)
{
	/*
		These comments are just to show the input param format

		$data => Array
		(
			[id] => 2
			[params] => Array
				(
					[amount] => 400
				)

		)
	*/

	if (isset($data["params"]["amount"]))
	{
		if ($data["params"]["amount"] < 100)
			phpgrid_error("Amount must be greater than 100");

		$str = "UPDATE invheader SET amount={$data["params"]["amount"]}+10
						WHERE id = {$data["id"]}";

		global $g;
		$g->execute_query($str);
		die;
	}
}
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
</head>
<body>
	<script type="text/javascript">
		var opts = {
		    errorCell: function(res,stat,err)
		    {
				jQuery.jgrid.info_dialog(jQuery.jgrid.errors.errcap,
											'<div class=\"ui-state-error\">'+ res.responseText +'</div>', 
											jQuery.jgrid.edit.bClose,
											{buttonalign:'right'}
										);		    	
		    }
		};	
		
		function fix_textarea_enter()
		{
			$('textarea').keydown(
									function(event) 
									{ 
										if (!event.ctrlKey && event.keyCode == 13)
										{
											return true;
										}
										else
										{
											// if ctrl+enter
											if(event.keyCode == 13) 
											{
												event.stopImmediatePropagation();
												var $txt = jQuery(this);
												var caretPos = $txt[0].selectionStart;
												var textAreaTxt = $txt.val();
												var txtToAdd = "\n";
												$txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
												$txt[0].selectionStart = caretPos + txtToAdd.length;
												$txt[0].selectionEnd = caretPos + txtToAdd.length;
											}
										}
										
									}
								);
		}
	</script>
	

	<style>
	/* fix for freeze column div position */
	.ui-jqgrid .editable {margin: 0px !important;}
		
	.ui-jqgrid tr.ui-row-ltr td.edit-cell {
		padding-top: 3px;
		padding-bottom: 3px;
	}	
	.ui-jqgrid tr.jqgrow td {
		height: 25px;
	}
	</style>
	
	<script>
	function normalize_height()
	{
		var grid = jQuery('#list1')[0];
		
		// adjust height of rows (for multi line cell content)
		jQuery('.frozen-bdiv tr.jqgrow').each(function () {
								var h = jQuery('#'+jQuery.jgrid.jqID(this.id)).height();
								
								if (jQuery.browser.chrome)
									h-=2;
								else
									h-=1;
								
								jQuery(this).height(h);
							});		
							
		// sync top position
		$(grid.grid.fbDiv).css('top',$(grid.grid.bDiv).offset().top-11);
		$(grid.grid.fhDiv).css('top',$(grid.grid.hDiv).offset().top-11);
		
		// sync scrolling position
		$(grid.grid.fbDiv).scrollTop($(grid.grid.bDiv).scrollTop());
		$(grid.grid.fbDiv).scrollLeft($(grid.grid.bDiv).scrollLeft());
		
		// fix for height pixel
		if (jQuery.browser.msie)
			$(grid.grid.fbDiv).height($(grid.grid.bDiv).height()-16);
		else
			$(grid.grid.fbDiv).height($(grid.grid.bDiv).height()-18);						
		
	}
	
	jQuery(document).ready(function(){

		setTimeout(function(){
			jQuery('#list1').jqGrid('navButtonAdd', '#list1_pager', 
			{
				'caption'      : 'Freeze Mode', 
				'buttonicon'   : 'ui-icon-extlink', 
				'onClickButton': function()
				{
					var t;
					if (jQuery('div.frozen-bdiv').length == 0)
					{
						jQuery("#list1").jqGrid("setGridParam",{cellEdit : false});
						jQuery('#list1').jqGrid('setFrozenColumns');
						jQuery("#list1").jqGrid("setGridParam",{cellEdit : true});

						// adjust height
						var grid = jQuery('#list1')[0];
						$(grid.grid.bDiv).scroll(function () { normalize_height(); });		
						normalize_height();
						
						// fix for ie
						if (jQuery.browser.msie)
							$(grid.grid.fhDiv).css("top","+=1");
						
						// jQuery(".ui-icon-pencil, .ui-icon-disk, .ui-icon-cancel").click(function(){ setTimeout("normalize_height()",100); });
						
						// sync frozen rows height
						jQuery('#list1').bind("mousedown keydown keyup",function(){
							t = setInterval("normalize_height()",100); 
							setTimeout(function(){clearInterval(t);},1000);
						});
						
						// remove frozen while add
						jQuery(".ui-icon-plus").click(function(){ jQuery('#list1').jqGrid('destroyFrozenColumns'); });
					}
					else
					{
						clearInterval(t);
						jQuery('#list1').jqGrid('destroyFrozenColumns');
					}				
				},
				'position': 'last'
			});
			
			jQuery('#list1').jqGrid('destroyFrozenColumns').trigger('reloadGrid', [{current:true}]);
			
		},200);
	});
	
	function do_load()
	{
		normalize_height();
	}
	
	</script>	
	<div style="margin:10px">
	<?php echo $out?>
	</div>
</body>
</html>