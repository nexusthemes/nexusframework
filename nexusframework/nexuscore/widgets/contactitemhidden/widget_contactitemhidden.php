<?php

function nxs_widgets_contactitemhidden_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-text"; // . $widget_name;
}

function nxs_widgets_contactitemhidden_gettitle()
{
	return nxs_l18n__("Hidden input", "nxs_td");
}

function nxs_widgets_contactitemhidden_getformitemsubmitresult($args)
{
	// $args consists of "metadata"
	// combined with $_POST this should feed us with all information
	// needed to produce the result :)
	
	extract($args);
	
	$elementid = $metadata["elementid"];
	$overriddenelementid = $metadata["overriddenelementid"];
	$formlabel = $metadata["formlabel"];
		
	$result = array();
	$result["result"] = "OK";
	$result["validationerrors"] = array();
	$result["markclientsideelements"] = array();
	
	nxs_requirewidget("contactbox");
	$prefix = nxs_widgets_contactbox_getclientsideprefix($postid, $placeholderid);
	
	if ($overriddenelementid != "")
	{
		$key = $overriddenelementid;
	}
	else
	{
		$key = $prefix . $elementid;		
	}
	
	$value = $_POST[$key];
	
	$result["output"] = "$formlabel: $value";
	
	return $result;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_contactitemhidden_renderincontactbox($args)
{
	//
	extract($args);
	
	extract($metadata, EXTR_PREFIX_ALL, "metadata");
	
	$result = array();
	$result["result"] = "OK";
	
	nxs_requirewidget("contactbox");
	$prefix = nxs_widgets_contactbox_getclientsideprefix($postid, $placeholderid);
		
	//
	// render actual control / html
	//
	
	nxs_ob_start();
	
	if (nxs_stringstartswith($metadata_value, "queryparam@"))
	{
		// runtime value
		$splitted = explode("@", $metadata_value);
		$queryparametername = $splitted[1];
		
		if ($metadata_overriddenelementid != "")
		{
			$key = $metadata_overriddenelementid;
		}
		else
		{
			$key = $prefix . $metadata_elementid;			
		}
		
		?>
	  <input type="hidden" id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="field_name" value="runtime" />
	  <script type='text/javascript'>
	  	jQuery(window).ready
	  	(
	  		function()
	  		{
  				jQuery("#<?php echo $key; ?>").val(nxs_js_getqueryparametervalue("<?php echo $queryparametername; ?>"));
  			}
  		);
  	</script>
		<?php
	}
	// else if 
	// { 
	// 	 // support other specific types here too
	// }
	//
	else
	{
		if ($metadata_overriddenelementid != "")
		{
			$key = $metadata_overriddenelementid;
		}
		else
		{
			$key = $prefix . $metadata_elementid;			
		}
		

		
		// static value
		?>
		<input type="hidden" id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="field_name" value="<?php echo $metadata_value;?>" />
		<?php
	}
	
	// var_dump($args);
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_contactitemhidden_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes = array_merge($temp_array, $args);
	
	$image_imageid = $mixedattributes['image_imageid'];
	$title = $mixedattributes['title'];
	$text = $mixedattributes['text'];
	$destination_articleid = $mixedattributes['destination_articleid'];
	
	$lookup = wp_get_attachment_image_src($image_imageid, 'full', true);
	
	$width = $lookup[1];
	$height = $lookup[2];		
	
	$lookup = wp_get_attachment_image_src($image_imageid, 'thumbnail', true);
	$url = $lookup[0];
	$url = nxs_img_getimageurlthemeversion($url);

	global $nxs_global_placeholder_render_statebag;
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["enable_decoratewidget"] = false;
	$hovermenuargs["enable_deletewidget"] = false;
	$hovermenuargs["enable_deleterow"] = true;
	$hovermenuargs["metadata"] = $mixedattributes;	
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	/* ADMIN EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-contactitemhidden-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box">
	        	<div class="box-title">
					<h4>Hidden input element: ' . $formlabel . '</h4>
				</div>
				<div class="box-content"></div>
			</div>
			<div class="nxs-clear"></div>
		</div>
	</div>';
	
	/* ------------------------------------------------------------------------------------------------- */

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

// Define the properties of this widget
function nxs_widgets_contactitemhidden_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_contactitemhidden_gettitle(),
		"sheeticonid" => nxs_widgets_contactitemhidden_geticonid(),
	
		"fields" => array
		(
			// GENERAL			
			
			array
			( 
				"id" 				=> "formlabel",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Label", "nxs_td"),
				"placeholder" => nxs_l18n__("Label goes here", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "elementid",
				"type" 				=> "input",
				"visibility"	=> "hide",
				"label" 			=> nxs_l18n__("Element ID", "nxs_td"),
				"placeholder" => nxs_l18n__("Enter a unique ID for this element", "nxs_td"),
			),
			
			array
			( 
				"id" 				=> "overriddenelementid",
				"type" 				=> "input",
				"visibility"	=> "text",
				"label" 			=> nxs_l18n__("Override default element ID", "nxs_td"),
				"placeholder" => nxs_l18n__("Leave blank to use default", "nxs_td"),
			),
			
			// bijv. "queryparam@foo"
			array
			( 
				"id" 				=> "value",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Initial text", "nxs_td"),
			),
		)
	);
	
	return $options;
}

function nxs_widgets_contactitemhidden_initplaceholderdata($args)
{
	extract($args);

	$args["elementid"] = nxs_generaterandomstring(6);
	$args["value"] = "queryparam@foo";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>