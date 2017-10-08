<?php
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 2.0.0
 * @license: see license.txt included in package
 */

if(!file_exists("config.php"))
{
	header("location: ./install.php");
	die;
}
		
if (!empty($_GET["file"]))
{
	$f = $_GET["file"];
	
	$f = str_replace(".php","",$f);
	
	// remote file inclusion attempt fix
	if (strpos($f,".")!==false)
		die("+1 for you");
		
	$f = "demos/$f.php";

	if (!file_exists($f))
		die("+1 for you");

	$code = file_get_contents($f);
	
	// removed db settings
	$code = preg_replace("/mysql_connect(.*)/i","mysql_connect('localhost','user','pass');",$code);
	
	highlight_string($code);
	echo "<br>&nbsp;";
	die;
}	
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>PHP Grid Framework Demos | www.phpgrid.org</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
	body {
		padding-top: 60px;
		padding-bottom: 0px;
	}
	.sidebar-nav {
		padding: 9px 0;
	}
	.nav
	{
		margin-bottom:10px;
	}
	.accordion-inner a {
		font-size: 13px;
		font-family:tahoma;
	}
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

  </head>

  <body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">PHP Grid Framework Demos</a>
          <div class="nav-collapse collapse">
            <p class="navbar-text pull-right">
              <a target="_blank" href="http://www.phpgrid.org/" class="navbar-link">www.phpgrid.org</a>
            </p>
            <ul class="nav">
              <li><a target="_blank" href="http://www.phpgrid.org/">Home</a></li>
              <li class="active"><a href="#">Demos</a></li>
              <li><a target="_blank" href="http://www.phpgrid.org/faqs/">FAQ</a></li>
              <li><a target="_blank" href="http://www.phpgrid.org/docs/">Docs</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

	<?php 
	function dirToArray($dir) 
	{
		$result = array();
		$cdir = scandir($dir);
		foreach ($cdir as $key => $value)
		{
		  if (!in_array($value,array(".","..","temp")) && strpos($value,"_") === false && strpos($value,"bak") === false)
		  {
			 if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
			 {
				$result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
			 }
			 else
			 {
				$result[] = $value;
			 }
		  }
		}

		return $result;
	}
	$samples = dirToArray("demos");
	?>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span2">
			<div class="accordion" id="accordion_menu">
					<?php 
					foreach($samples as $k=>$v) 
					{
						if (is_numeric($k)) continue;
						$folder = ucwords($k);
						?>
						<div class="accordion-group">
						<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_menu" href="#collapse<?php echo $k?>">
						  <strong><?php echo $folder;?></strong>
						</a>
						</div>	
						<div id="collapse<?php echo $k?>" class="accordion-body collapse">
							<div class="accordion-inner">
								<?php
								foreach($v as $f) 
								{
									$fname = str_replace(".php","",$f);
									$fname = str_replace("-"," ",$fname);
									
									if (is_array($fname))
										continue;
										
									$fname = ucwords($fname);
										
									echo "<a href='demos/$k/$f' onclick=\"jQuery('#code').load('index.php?file=/$k/$f'); $('#grid-demo-tabs a:first').tab('show');\" target='demo_frame'> $fname </a><br/>";
								}
								?>
							</div>
						</div>				
						</div>				
						<?php
					}
					?>
			</div>
		  
		  
        </div><!--/span-->
		
        <div class="span10">
          <div class="row-fluid">
            <div class="span12">
			
				<ul class="nav nav-tabs" id="grid-demo-tabs">
					<li class="active"><a href="#demo" data-toggle="tab">Demo</a></li>
					<li><a href="#code" data-toggle="tab">Code</a></li>
					<li class="pull-right">
						<style>
						#select_theme option { text-transform: capitalize; }
						</style>
						Themes:
						<select id="select_theme" class="input-medium">

							<?php
							$black = array("metro-black","black-tie","dark-hive","dot-luv","trontastic","vader","ui-darkness");
							$white = array("base","metro-light","blitzer","south-street","start","cupertino","flick","hot-sneaks","redmond","smoothness");
							$mix = array("metro-dark","swanky-purse","eggplant","le-frog","mint-choc","sunny","ui-lightness","pepper-grinder","overcast","humanity","excite-bike");
							$wijmo = array("arctic","midnight","aristo","rocket","cobalt","sterling");
							?>
							
							<optgroup label="White">
							<?php foreach($white as $t) {
							$s = ($t == "redmond")?"selected":"";
							?>
							<option <?php echo $s?> value="<?php echo $t?>"><?php echo ucwords($t) ?></option>
							<?php } ?>
							</optgroup>
						
							<optgroup label="Black">
							<?php foreach($black as $t) { ?>
							<option value="<?php echo $t?>"><?php echo ucwords($t) ?></option>
							<?php } ?>
							</optgroup>

							<optgroup label="Mix">
							<?php foreach($mix as $t) { ?>
							<option value="<?php echo $t?>"><?php echo ucwords($t) ?></option>
							<?php } ?>
							</optgroup>

							<optgroup label="Wijmo">
							<?php foreach($wijmo as $t) { ?>
							<option value="<?php echo $t?>"><?php echo ucwords($t) ?></option>
							<?php } ?>
							</optgroup>
							
						</select>					
						Layout:
						<select id="select_layout" class="input-small">
							<option value="">Default</option>
							<option value=".classic">Classic</option>
							<option value=".bs">Bootstrap</option>
						</select>					
					</li>
				</ul>
				
				<div class="tab-content" id="grid-demo-tabs-content">
				  
					<div id="demo" class="tab-pane fade in active">
					<iframe style="overflow:auto" onload="iframeLoaded(this)" name="demo_frame" frameborder="0" width="100%" height="500" src="demos/editing/index.php"></iframe>
					</div>
				  
					<div id="code" class="tab-pane fade">
					</div>
				  
				</div>

            </div><!--/span-->
          </div><!--/row-->
        </div><!--/span-->
		
		<div class="row-fluid">
			<div class="span12">
			  <div class="row-fluid">
				<div class="alert alert-info">
					<a name="contact"></a>
				  <h2>Technical Support</h2>
				  <p class="text-info">For technical support query, ask at our <a target="_blank" href="http://www.phpgrid.org/support/">Support Center</a> </p>
				  <p>&copy; <a target="_blank" href="http://www.phpgrid.org/">www.phpgrid.org</a> 2010-<?php echo date("Y");?></p>
				</div><!--/span-->
			  </div><!--/row-->
			</div><!--/span-->
		  </div><!--/row-->
		  
      </div><!--/row-->

		<!-- Le javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="bootstrap/js/jquery.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script>
		
			$('#grid-demo-tabs a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
			})
			
			jQuery('#code').load('index.php?file=/editing/index.php');
				
			function iframeLoaded(iFrameID,stop) 
			{
				if(iFrameID) 
				{
					if(iFrameID.contentDocument){
						if (iFrameID.contentDocument.body)
							if (iFrameID.height != iFrameID.contentDocument.body.scrollHeight)
								iFrameID.height = iFrameID.contentDocument.body.scrollHeight + 35;
					} else {
						iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + 45 + "px";
					}
				}
				
				setTimeout(function(){iframeLoaded(iFrameID,1);},1000);
				
				if (!stop) update_themes();
			}

			var update_themes = function(){

				var t = jQuery("#select_theme").val();
				var l = jQuery("#select_layout").val();
				
				switch(t)
				{
					// wijmo
					case "arctic":
					case "midnight":
					case "aristo":
					case "rocket":
					case "cobalt":
					case "sterling":
						$("iframe[name=demo_frame]").contents().find("body").append('<link rel="stylesheet" type="text/css" media="screen" href="http://cdn.wijmo.com/themes/'+t+'/jquery-wijmo.css"></link>');
					default:
						$("iframe[name=demo_frame]").contents().find("body").append('<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/themes/'+t+'/jquery-ui.custom.css"></link>');
				}
				
				if ( $("iframe[name=demo_frame]")[0].contentWindow.location.href.indexOf('bootstrap') != -1)
					$("iframe[name=demo_frame]").contents().find("body").append('<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/jqgrid/css/ui.jqgrid.bs.css"></link>');
				else
					$("iframe[name=demo_frame]").contents().find("body").append('<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/jqgrid/css/ui.jqgrid'+l+'.css"></link>');

				if (l != '')
					setTimeout(function(){$("iframe[name=demo_frame]").contents().find("link[href*='ui.jqgrid.css']").remove();},100);
				
			}
			
			$("#select_theme").change(update_themes);
			$("#select_layout").change(update_themes);
			
			$(window).load(function(){
				update_themes();
			})
			
		</script>

	</div><!--/.fluid-container-->

	</body>
</html>
