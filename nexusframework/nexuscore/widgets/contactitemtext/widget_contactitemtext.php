<?php

function nxs_widgets_contactitemtext_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-text"; // . $widget_name;
}

function nxs_widgets_contactitemtext_gettitle()
{
	return nxs_l18n__("Text input", "nxs_td");
}

function nxs_widgets_contactitemtext_getformitemsubmitresult($args)
{
	// $args consists of "metadata"
	// combined with $_REQUEST this should feed us with all information
	// needed to produce the result :)
	
	extract($args);
	
	$elementid = $metadata["elementid"];
	$overriddenelementid = $metadata["overriddenelementid"];
	$formlabel = $metadata["formlabel"];
	$isrequired = $metadata["isrequired"];
		
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
	$value = $_REQUEST[$key];
	
	if ($isrequired != "")
	{
		// it is required field
		if (trim($value) == '')
		{
			// error
			$result["validationerrors"][] = sprintf(nxs_l18n__("%s is a required field", "nxs_td"), $formlabel);
			$result["markclientsideelements"][] = $key;
		}
	}
	
	$result["output"] = "$formlabel: $value";
	
	return $result;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_contactitemtext_renderincontactbox($args)
{
	extract($args);
	
	extract($metadata, EXTR_PREFIX_ALL, "metadata");
	
	$result = array();
	$result["result"] = "OK";
	
	nxs_requirewidget("contactbox");
	$prefix = nxs_widgets_contactbox_getclientsideprefix($postid, $placeholderid);
	
	if ($metadata_overriddenelementid != "")
	{
		$key = $metadata_overriddenelementid;
	}
	else
	{
		$key = $prefix . $metadata_elementid;
	}
	
	if (!isset($value) || $value == "")
	{
		$value = $metadata_initialtext;
	}
	
	//
	// render actual control / html
	//
	
	ob_start();

	$colorzencssclass = "";
	if ($form_metadata["items_colorzen"] != "")
	{
		$colorzencssclass = "nxs-colorzen nxs-colorzen-" . $form_metadata["items_colorzen"];
	}

	?>
	
  <label class="field_name"><?php echo $metadata_formlabel;?><?php if ($metadata_isrequired != "") { ?>*<?php } ?></label>
  
  <?php
  if ($metadata_numofrows == "" || $metadata_numofrows == 0 || $metadata_numofrows == 1)
  {
  	?>
	  <input type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="field_name <?php echo $colorzencssclass; ?>" value="<?php echo $value;?>" placeholder="<?php echo $metadata_placeholder; ?>" />
		<?php 
	} 
	else
	{
		?>
	  <textarea style='min-height: 0px;' rows="<?php echo $metadata_numofrows; ?>" id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="field_name <?php echo $colorzencssclass; ?>" placeholder="<?php echo $metadata_placeholder; ?>"><?php echo $value;?></textarea>
		<?php 
	}
	
	// var_dump($args);
	
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_contactitemtext_render_webpart_render_htmlvisualization($args)
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
	
	ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-contactitemtext-item";
	
	/* ADMIN OUTPUT
	---------------------------------------------------------------------------------------------------- */
	
	echo '
	<div ' . $class . '>
		<div class="content2">
			<div class="box">
	        	<div class="box-title nxs-width20"><h4><span class="nxs-icon-text" style="font-size: 16px;" /> Text</h4></div>
				<div class="box-content  nxs-width80">'.$formlabel.'</div>
			</div>
			<div class="nxs-clear"></div>
		</div>
	</div>';
	
	/* ------------------------------------------------------------------------------------------------- */

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = ob_get_contents();
	ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

// Define the properties of this widget
function nxs_widgets_contactitemtext_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_contactitemtext_gettitle(),
		"sheeticonid" => nxs_widgets_contactitemtext_geticonid(),
	
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
			/*
			can only be set by code			
			array
			( 
				"id" 				=> "overriddenelementid",
				"type" 				=> "input",
				"visibility"	=> "text",
				"label" 			=> nxs_l18n__("Override default element ID", "nxs_td"),
				"placeholder" => nxs_l18n__("Leave blank to use default", "nxs_td"),
			),
			*/
			array
			( 
				"id" 				=> "isrequired",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Is required", "nxs_td"),
			),

			array
			( 
				"id" 				=> "placeholder",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Placeholder", "nxs_td"),
			),

			array
			( 
				"id" 				=> "initialtext",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Initial text", "nxs_td"),
			),
			
			array(
				"id" 				=> "numofrows",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Number of lines", "nxs_td"),
				"dropdown" 			=> array
				(
					"1"	=> "1", 
					"2"	=> "2",
					"3"	=> "3",
					"4"	=> "4",
					"5"	=> "5",
					"6"	=> "6",
					"7"	=> "7",
					"8"	=> "8",
					"9"	=> "9",
					"10"	=> "10"
				),
			),
		)
	);
	
	return $options;
}

function nxs_widgets_contactitemtext_initplaceholderdata($args)
{
	extract($args);

	$args["elementid"] = nxs_generaterandomstring(6);
	$args["numofrows"] = "1";
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

?>
