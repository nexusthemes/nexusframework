<?php
function nxs_popup_optiontype_widgettypeconverter_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	// default settings, overriden by extract statements below
	// $initial_toggle_state = "open";
	$label = "Type converter";
	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter

	// handle possible postback
 	if ($nxs_widgettypeconverter_converttotype != "")
 	{
		nxs_persistwidgettype($nxs_widgettypeconverter_postid, $nxs_widgettypeconverter_placeholderid, $nxs_widgettypeconverter_converttotype);

		// re-trigger the refresh immediately to reflect the new situation
		?>
		<script>
			nxs_js_htmldialogmessageok('One moment...', '<div>Page will now refresh, one moment...</div>');
			setTimeout
			(
				function()
				{
					nxs_js_refreshcurrentpage();		
				}
				,
				1000
			);			
		</script>
		<?php
 	}	

	?>
	<div class="content2">
	  <div class="box">
	  	<div class="box-title">
				<h4><?php echo $label; ?></h4>
			</div>
      <div class="box-content">
      
	      <?php
	      	$postid = $args["clientpopupsessioncontext"]["postid"];
	      	$placeholderid = $args["clientpopupsessioncontext"]["placeholderid"];
	      	$allwidgets = array
	      	(
	      		//"calloutthemepreview",
	      		//"themelivedemo"
	      		"formbox",
	      		"contactbox"
	      		);
	      	foreach ($allwidgets as $currentwidget)
      		{
      			?>
      			<a href="#" onclick="nxs_js_widgets_convertwidgettype('<?php echo $postid;?>', '<?php echo $placeholderid;?>', '<?php echo $currentwidget;?>'); return false;"><?php echo $currentwidget;?></a><br />
      			<?php
      		}
      	?>
      </div>
    </div>
    <div class="nxs-clear"></div>
  </div>
  <script>
  	function nxs_js_widgets_convertwidgettype(postid, placeholderid, newwidgettype)
  	{
  		var answer = confirm(nxs_js_gettrans('Sure you want to convert to this type?'));
			if (!answer)
			{
				// toch niet
				return true;
			}
			
  		// confirmed
  		nxs_js_popup_setshortscopedata("nxs_widgettypeconverter_postid", postid);
  		nxs_js_popup_setshortscopedata("nxs_widgettypeconverter_placeholderid", placeholderid);
  		nxs_js_popup_setshortscopedata("nxs_widgettypeconverter_converttotype", newwidgettype);
			nxs_js_popup_refresh_v2(true);
  	}
  </script>
  <?php
	//
}


function nxs_popup_optiontype_widgettypeconverter_renderstorestatecontroldata($optionvalues)
{
	// nothing to do here
}

function nxs_popup_optiontype_widgettypeconverter_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	// nothing to do here
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_widgettypeconverter_getpersistbehaviour()
{
	return "readonly";
}

?>